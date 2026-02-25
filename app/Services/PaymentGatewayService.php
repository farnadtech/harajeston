<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    public function getActiveGateways()
    {
        return PaymentGateway::active()->get();
    }

    public function initiateCharge(User $user, int $amount, string $gatewayName)
    {
        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', true)
            ->firstOrFail();

        // محاسبه مالیات (9%)
        $taxAmount = (int) ($amount * 0.09);
        $finalAmount = $amount + $taxAmount;

        // ایجاد تراکنش
        $transaction = WalletTransaction::create([
            'wallet_id' => $user->wallet->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'final_amount' => $finalAmount,
            'gateway' => $gatewayName,
            'status' => 'pending',
            'description' => 'شارژ کیف پول',
            'balance_before' => $user->wallet->balance,
            'balance_after' => $user->wallet->balance,
            'frozen_before' => $user->wallet->frozen,
            'frozen_after' => $user->wallet->frozen,
        ]);

        // تنظیم درگاه
        $gatewayConfig = $this->getGatewayConfig($gateway);

        try {
            // درگاه‌های ایرانی به ریال کار می‌کنند
            // مبلغ نهایی به تومان است، باید به ریال تبدیل شود (ضرب در 10)
            $amountInRial = $finalAmount * 10;
            
            // برای زرین‌پال در حالت sandbox، از API sandbox استفاده می‌کنیم
            if ($gatewayName === 'zarinpal' && $gateway->sandbox_mode) {
                return $this->initiateZarinpalSandbox($transaction, $amountInRial, $user, $gatewayConfig);
            }
            
            $larapay = app('larapay')->gateway($gatewayName, $gatewayConfig);
            
            $result = $larapay->request(
                $transaction->id,
                $amountInRial,
                '', // national_id - optional
                $user->phone ?? '', // mobile
                route('wallet.payment.callback'),
                [] // allowed_cards - optional
            );

            // ذخیره شناسه تراکنش (token) و درگاه در session
            $transaction->update([
                'transaction_id' => $result['token'],
            ]);
            
            session(['payment_gateway' => $gatewayName]);

            return [
                'transaction' => $transaction,
                'token' => $result['token'],
                'redirect_url' => $this->getRedirectUrl($gatewayName, $result['token']),
            ];
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            $transaction->update(['status' => 'failed']);

            throw new \Exception('خطا در ایجاد پرداخت: ' . $e->getMessage());
        }
    }

    public function verifyPayment($token, $gatewayName, array $params = [])
    {
        $transaction = WalletTransaction::where('transaction_id', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $gateway = PaymentGateway::where('name', $gatewayName)
            ->where('is_active', true)
            ->firstOrFail();

        $gatewayConfig = $this->getGatewayConfig($gateway);

        try {
            // درگاه‌های ایرانی به ریال کار می‌کنند
            $amountInRial = $transaction->final_amount * 10;
            
            // برای زرین‌پال در حالت sandbox، از API sandbox استفاده می‌کنیم
            if ($gatewayName === 'zarinpal' && $gateway->sandbox_mode) {
                return $this->verifyZarinpalSandbox($transaction, $amountInRial, $token, $params);
            }
            
            $larapay = app('larapay')->gateway($gatewayName, $gatewayConfig);
            
            $result = $larapay->verify(
                $transaction->id,
                $amountInRial,
                '', // national_id
                $transaction->wallet->user->phone ?? '',
                $token,
                $params
            );

            DB::transaction(function () use ($transaction, $result) {
                // به‌روزرسانی تراکنش
                $trackingCode = $result['tracking_code'] ?? $result['reference_id'] ?? null;
                
                $transaction->update([
                    'status' => 'completed',
                    'reference_id' => $trackingCode,
                    'balance_after' => $transaction->wallet->balance + $transaction->amount,
                ]);

                // شارژ کیف پول
                $transaction->wallet->increment('balance', $transaction->amount);
            });

            return [
                'success' => true,
                'transaction' => $transaction->fresh(),
                'tracking_code' => $transaction->fresh()->reference_id ?? 'بدون کد پیگیری',
            ];
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            $transaction->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت: ' . $e->getMessage(),
            ];
        }
    }

    private function getGatewayConfig(PaymentGateway $gateway): array
    {
        $config = [];

        switch ($gateway->name) {
            case 'zarinpal':
                // اگر حالت sandbox فعال باشد، از یک UUID دلخواه استفاده می‌کنیم
                if ($gateway->sandbox_mode) {
                    $config = [
                        'merchant_id' => '00000000-0000-0000-0000-000000000000', // UUID دلخواه برای sandbox
                        'sandbox' => true,
                    ];
                } else {
                    $config = [
                        'merchant_id' => $gateway->getCredential('merchant_id'),
                    ];
                }
                break;

            case 'zibal':
                $config = [
                    'merchant' => $gateway->getCredential('merchant_id'), // Zibal uses 'merchant' not 'merchant_id'
                ];
                break;

            case 'vandar':
                $config = [
                    'api_key' => $gateway->getCredential('api_key'),
                ];
                break;

            case 'payping':
                $config = [
                    'token' => $gateway->getCredential('api_key'), // PayPing uses 'token' not 'api_key'
                ];
                break;
        }

        return $config;
    }

    private function getRedirectUrl(string $gatewayName, string $token): string
    {
        // برای زرین‌پال، اگر sandbox فعال باشد از URL sandbox استفاده می‌شود
        if ($gatewayName === 'zarinpal') {
            $gateway = PaymentGateway::where('name', 'zarinpal')->first();
            if ($gateway && $gateway->sandbox_mode) {
                return 'https://sandbox.zarinpal.com/pg/StartPay/' . $token;
            }
            return 'https://www.zarinpal.com/pg/StartPay/' . $token;
        }
        
        return match ($gatewayName) {
            'zibal' => 'https://gateway.zibal.ir/start/' . $token,
            'vandar' => 'https://ipg.vandar.io/v4/' . $token,
            'payping' => 'https://api.payping.ir/v2/pay/gotoipg/' . $token,
            default => '#',
        };
    }

    /**
     * درخواست پرداخت با زرین‌پال در حالت Sandbox
     */
    private function initiateZarinpalSandbox($transaction, $amountInRial, $user, $config)
    {
        try {
            $url = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';
            
            $response = \Illuminate\Support\Facades\Http::post($url, [
                'merchant_id' => '00000000-0000-0000-0000-000000000000', // UUID تستی برای sandbox
                'amount' => $amountInRial,
                'description' => $transaction->id . '-' . $amountInRial,
                'callback_url' => route('wallet.payment.callback'),
            ]);

            $result = $response->json();

            if (!isset($result['data']) || $result['data']['code'] != 100) {
                throw new \Exception('خطا در ایجاد درخواست sandbox: ' . ($result['errors']['message'] ?? 'خطای نامشخص'));
            }

            $authority = $result['data']['authority'];

            $transaction->update([
                'transaction_id' => $authority,
            ]);

            session(['payment_gateway' => 'zarinpal']);

            return [
                'transaction' => $transaction,
                'token' => $authority,
                'redirect_url' => 'https://sandbox.zarinpal.com/pg/StartPay/' . $authority,
            ];
        } catch (\Exception $e) {
            Log::error('Zarinpal Sandbox initiation failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * تایید پرداخت با زرین‌پال در حالت Sandbox
     */
    private function verifyZarinpalSandbox($transaction, $amountInRial, $token, $params)
    {
        // چک کردن پارامترهای callback
        if (!isset($params['Authority']) || $params['Authority'] != $token) {
            throw new \Exception('عدم تطبیق توکن');
        }

        if (!isset($params['Status']) || $params['Status'] != 'OK') {
            throw new \Exception('پرداخت ناموفق');
        }

        try {
            $url = 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json';
            
            $response = \Illuminate\Support\Facades\Http::post($url, [
                'merchant_id' => '00000000-0000-0000-0000-000000000000', // UUID تستی برای sandbox
                'amount' => $amountInRial,
                'authority' => $token,
            ]);

            $result = $response->json();

            if (!isset($result['data']) || $result['data']['code'] != 100 && $result['data']['code'] != 101) {
                throw new \Exception('خطا در تایید پرداخت sandbox: ' . ($result['errors']['message'] ?? 'خطای نامشخص'));
            }

            $refId = $result['data']['ref_id'] ?? null;

            DB::transaction(function () use ($transaction, $refId) {
                $transaction->update([
                    'status' => 'completed',
                    'reference_id' => $refId,
                    'balance_after' => $transaction->wallet->balance + $transaction->amount,
                ]);

                // شارژ کیف پول
                $transaction->wallet->increment('balance', $transaction->amount);
            });

            return [
                'success' => true,
                'transaction' => $transaction->fresh(),
                'tracking_code' => $refId ?? 'بدون کد پیگیری',
            ];
        } catch (\Exception $e) {
            Log::error('Zarinpal Sandbox verification failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
