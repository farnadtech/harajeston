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

class ProcessAuctionEnding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [10, 30, 60];

    /**
     * Execute the job.
     */
    public function handle(AuctionService $auctionService): void
    {
        // Query auctions with status='active' and ends_at <= now
        $auctions = Listing::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();

        Log::info('ProcessAuctionEnding: Found ' . $auctions->count() . ' auctions to end');

        foreach ($auctions as $auction) {
            try {
                $auctionService->endAuction($auction);
                Log::info('ProcessAuctionEnding: Ended auction ' . $auction->id);
            } catch (\Exception $e) {
                Log::error('ProcessAuctionEnding: Failed to end auction ' . $auction->id . ': ' . $e->getMessage());
                
                // Re-throw to trigger retry logic
                throw $e;
            }
        }
    }
}
