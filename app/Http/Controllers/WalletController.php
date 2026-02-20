<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Display wallet and transaction history
     */
    public function show()
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.show', compact('wallet', 'transactions'));
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $this->walletService->addFunds(
            auth()->user(),
            $request->amount,
            'افزودن موجودی'
        );

        return redirect()
            ->route('wallet.show')
            ->with('success', 'موجودی با موفقیت افزوده شد.');
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
