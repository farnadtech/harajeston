<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Services\WalletService;

class BidController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
        $this->middleware('admin');
    }

    /**
     * Cancel a bid (admin action)
     */
    public function cancel(Bid $bid)
    {
        if ($bid->listing->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'فقط پیشنهادات مزایده‌های فعال قابل ابطال هستند'
            ]);
        }

        // Refund if this was the highest bid
        if ($bid->id === $bid->listing->bids()->orderBy('amount', 'desc')->first()?->id) {
            // Find the second highest bid
            $secondHighest = $bid->listing->bids()
                ->where('id', '!=', $bid->id)
                ->orderBy('amount', 'desc')
                ->first();

            if ($secondHighest) {
                $bid->listing->update(['current_price' => $secondHighest->amount]);
            } else {
                $bid->listing->update(['current_price' => $bid->listing->starting_price]);
            }
        }

        $bid->delete();

        // Log action
        \App\Models\AdminActionLog::create([
            'listing_id' => $bid->listing_id,
            'admin_id' => auth()->id(),
            'action' => 'cancel_bid',
            'description' => "پیشنهاد {$bid->amount} تومانی کاربر {$bid->user->name} ابطال شد",
            'icon' => 'close'
        ]);

        return response()->json(['success' => true]);
    }
}
