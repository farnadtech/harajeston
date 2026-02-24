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
     * Withdraw funds from wallet
     */
    public function withdraw(Request $request)
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);

        $request->validate([
            'amount' => "required|numeric|min:{$minWithdraw}|max:{$wallet->balance}",
        ], [
            'amount.min' => "حداقل مبلغ برداشت " . number_format($minWithdraw) . " تومان است.",
            'amount.max' => 'مبلغ برداشت نمی‌تواند بیشتر از موجودی قابل استفاده باشد.'
        ]);

        try {
            $this->walletService->deduct(
                $user,
                $request->amount,
                'برداشت از حساب'
            );

            return redirect()
                ->route('wallet.show')
                ->with('success', 'درخواست برداشت با موفقیت ثبت شد. مبلغ به حساب بانکی شما واریز خواهد شد.');
        } catch (\Exception $e) {
            return redirect()
                ->route('wallet.show')
                ->with('error', 'خطا در ثبت درخواست برداشت: ' . $e->getMessage());
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
        $transactions = $user->wallet->transactions()->get();

        $filename = 'transactions_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['تاریخ', 'نوع', 'مبلغ', 'توضیحات', 'موجودی قبل', 'موجودی بعد']);

            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->type,
                    $transaction->amount,
                    $transaction->description,
                    $transaction->before_balance,
                    $transaction->after_balance,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
