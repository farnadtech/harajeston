<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Services\AuctionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAuctionStarting implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(AuctionService $auctionService): void
    {
        // Query auctions with status='pending' and starts_at <= now
        $auctions = Listing::where('status', 'pending')
            ->where('starts_at', '<=', now())
            ->get();

        Log::info('ProcessAuctionStarting: Found ' . $auctions->count() . ' auctions to start');

        foreach ($auctions as $auction) {
            try {
                // تغییر وضعیت به active
                $auction->update(['status' => 'active']);
                Log::info('ProcessAuctionStarting: Started auction ' . $auction->id);
            } catch (\Exception $e) {
                Log::error('ProcessAuctionStarting: Failed to start auction ' . $auction->id . ': ' . $e->getMessage());
            }
        }
    }
}
