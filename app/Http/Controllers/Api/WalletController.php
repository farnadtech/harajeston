<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    public function show(Request $request)
    {
        $wallet = $request->user()->wallet;
        
        return new WalletResource($wallet);
    }

    public function transactions(Request $request)
    {
        $query = $request->user()->wallet->transactions()->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(50);

        return WalletTransactionResource::collection($transactions);
    }

    public function addFunds(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ], [
            'amount.required' => 'مبلغ الزامی است',
            'amount.numeric' => 'مبلغ باید عدد باشد',
            'amount.min' => 'مبلغ باید حداقل 1 ریال باشد',
        ]);

        try {
            $this->walletService->addFunds($request->user(), $validated['amount'], 'شارژ کیف پول');
            
            return response()->json([
                'message' => 'موجودی با موفقیت افزایش یافت',
                'wallet' => new WalletResource($request->user()->wallet->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
