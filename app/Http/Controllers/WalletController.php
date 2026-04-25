<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected PaymentGatewayService $gatewayService
    ) {}

    /**
     * Display wallet and transaction history
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        
        // فقط تراکنش‌های موفق نمایش داده شوند
        $query = $wallet->transactions()->where('status', 'completed');

        // Filter by date range (Jalali dates)
        if ($request->has('from_date') && $request->from_date) {
            try {
                $gregorianDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d H:i', $request->from_date)->toCarbon();
                $query->where('created_at', '>=', $gregorianDate);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }
        
        if ($request->has('to_date') && $request->to_date) {
            try {
                $gregorianDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d H:i', $request->to_date)->toCarbon();
                $query->where('created_at', '<=', $gregorianDate);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get active payment gateways
        $gateways = $this->gatewayService->getActiveGateways();

        // Get wallet settings
        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);
        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);
        $maxWithdraw = $wallet->balance; // حداکثر برداشت = موجودی فعلی
        $taxPercentage = \App\Models\SiteSetting::get('wallet_tax_percentage', 9);

        // Return different views based on user role
        if ($user->role === 'admin') {
            return view('wallet.admin', compact('wallet', 'transactions', 'gateways', 'minDeposit', 'maxDeposit', 'minWithdraw', 'maxWithdraw', 'taxPercentage'));
        } elseif ($user->canSell()) {
            return view('wallet.seller', compact('wallet', 'transactions', 'gateways', 'minDeposit', 'maxDeposit', 'minWithdraw', 'maxWithdraw', 'taxPercentage'));
        } else {
            return view('wallet.show', compact('wallet', 'transactions', 'gateways', 'minDeposit', 'maxDeposit', 'minWithdraw', 'maxWithdraw', 'taxPercentage'));
        }
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);

        $request->validate([
            'amount' => "required|numeric|min:{$minDeposit}|max:{$maxDeposit}",
            'gateway' => 'required|string|exists:payment_gateways,name',
        ], [
            'amount.min' => "حداقل مبلغ افزایش موجودی " . number_format($minDeposit) . " تومان است.",
            'amount.max' => "حداکثر مبلغ افزایش موجودی " . number_format($maxDeposit) . " تومان است.",
            'gateway.required' => 'لطفا درگاه پرداخت را انتخاب کنید.',
            'gateway.exists' => 'درگاه پرداخت انتخاب شده معتبر نیست.',
        ]);

        try {
            $result = $this->gatewayService->initiateCharge(
                auth()->user(),
                (int) $request->amount,
                $request->gateway
            );

            // Redirect to payment gateway
            return redirect($result['redirect_url']);
        } catch (\Exception $e) {
            return redirect()
                ->route('wallet.show')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Payment callback
     */
    public function paymentCallback(Request $request)
    {
        try {
            // Get token from different gateway parameters
            $token = $request->input('Authority') // ZarinPal
                ?? $request->input('trackId') // Zibal
                ?? $request->input('token') // Vandar
                ?? $request->input('refid'); // PayPing

            // Determine gateway from request or session
            $gateway = $request->input('gateway') ?? session('payment_gateway', 'zarinpal');

            if (!$token) {
                throw new \Exception('توکن پرداخت یافت نشد');
            }

            $result = $this->gatewayService->verifyPayment($token, $gateway, $request->all());

            if ($result['success']) {
                return redirect()
                    ->route('wallet.show')
                    ->with('success', "پرداخت با موفقیت انجام شد. کد پیگیری: {$result['tracking_code']}");
            }

            return redirect()
                ->route('wallet.show')
                ->with('error', $result['message']);
        } catch (\Exception $e) {
            return redirect()
                ->route('wallet.show')
                ->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * Withdraw funds from wallet - Create withdrawal request
     */
    public function withdraw(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);

        $request->validate([
            'amount'       => "required|numeric|min:{$minWithdraw}|max:{$wallet->balance}",
            'full_name'    => 'required|string|max:100',
            'bank_name'    => 'required|string|max:100',
            'card_number'  => 'required|string|size:16',
            'sheba_number' => 'required|string|size:24',
            'national_id'  => 'required|string|size:10',
        ], [
            'amount.min'           => "حداقل مبلغ برداشت " . number_format($minWithdraw) . " تومان است.",
            'amount.max'           => 'مبلغ برداشت نمی‌تواند بیشتر از موجودی باشد.',
            'full_name.required'   => 'نام و نام خانوادگی الزامی است.',
            'bank_name.required'   => 'نام بانک الزامی است.',
            'card_number.required' => 'شماره کارت الزامی است.',
            'card_number.size'     => 'شماره کارت باید 16 رقم باشد.',
            'sheba_number.required'=> 'شماره شبا الزامی است.',
            'sheba_number.size'    => 'شماره شبا باید 24 رقم باشد.',
            'national_id.required' => 'کد ملی الزامی است.',
            'national_id.size'     => 'کد ملی باید 10 رقم باشد.',
        ]);

        try {
            \App\Models\WithdrawalRequest::create([
                'user_id'      => $user->id,
                'amount'       => $request->amount,
                'full_name'    => $request->full_name,
                'bank_name'    => $request->bank_name,
                'card_number'  => $request->card_number,
                'sheba_number' => $request->sheba_number,
                'national_id'  => $request->national_id,
                'status'       => 'pending',
            ]);

            return redirect()
                ->route('wallet.show')
                ->with('success', 'درخواست برداشت با موفقیت ثبت شد و در انتظار بررسی ادمین است.');
        } catch (\Exception $e) {
            return redirect()
                ->route('wallet.show')
                ->with('error', 'خطا در ثبت درخواست: ' . $e->getMessage());
        }
    }

    /**
     * List transactions with filters
     */
    public function transactions(Request $request)
    {
        $user = auth()->user();
        $query = $user->wallet->transactions();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('wallet.transactions', compact('transactions'));
    }

    /**
     * Export transactions as CSV
     */
    public function export()
    {
        $user = auth()->user();
        $transactions = $user->wallet->transactions()->orderBy('created_at', 'desc')->get();

        $typeLabels = [
            'deposit'                    => 'واریز',
            'withdrawal'                 => 'برداشت',
            'freeze'                     => 'مسدود',
            'unfreeze'                   => 'آزادسازی',
            'freeze_deposit'             => 'بلاک سپرده',
            'unfreeze_refund'            => 'بازگشت وجه',
            'release_deposit'            => 'آزادسازی سپرده',
            'auction_payment'            => 'پرداخت حراجی',
            'deduct_frozen'              => 'کسر از مسدودی',
            'order_cancellation_penalty' => 'جریمه لغو سفارش',
            'commission'                 => 'کمیسیون',
            'refund'                     => 'بازگشت وجه',
            'charge'                     => 'شارژ',
            'payment'                    => 'پرداخت',
        ];

        $filename = 'transactions_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Transfer-Encoding' => 'binary',
        ];

        $callback = function () use ($transactions, $typeLabels) {
            $file = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['تاریخ', 'نوع', 'مبلغ (تومان)', 'توضیحات', 'موجودی قبل', 'موجودی بعد']);

            foreach ($transactions as $transaction) {
                $jalaliDate = \Morilog\Jalali\Jalalian::fromCarbon($transaction->created_at)->format('Y/m/d H:i');
                $typeLabel = $typeLabels[$transaction->type] ?? $transaction->type;

                fputcsv($file, [
                    $jalaliDate,
                    $typeLabel,
                    number_format($transaction->amount),
                    $transaction->description ?? '',
                    $transaction->balance_before !== null ? number_format($transaction->balance_before) : '',
                    $transaction->balance_after !== null ? number_format($transaction->balance_after) : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
