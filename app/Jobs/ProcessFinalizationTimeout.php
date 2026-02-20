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

class ProcessFinalizationTimeout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(AuctionService $auctionService): void
    {
        // Query auctions with finalization_deadline <= now and status='ended'
        $auctions = Listing::where('type', 'auction')
            ->where('status', 'ended')
            ->where('finalization_deadline', '<=', now())
            ->get();

        Log::info('ProcessFinalizationTimeout: Found ' . $auctions->count() . ' auctions with timeout');

        foreach ($auctions as $auction) {
            try {
                $auctionService->handleFinalizationTimeout($auction);
                Log::info('ProcessFinalizationTimeout: Processed timeout for auction ' . $auction->id);
            } catch (\Exception $e) {
                Log::error('ProcessFinalizationTimeout: Failed to process timeout for auction ' . $auction->id . ': ' . $e->getMessage());
            }
        }
    }
}
