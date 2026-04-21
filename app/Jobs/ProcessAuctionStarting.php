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
    public function handle(): void
    {
        // Check if approval is required
        $requiresApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
        
        // Query auctions with status='pending' and starts_at <= now
        $query = Listing::where('status', 'pending')
            ->where('starts_at', '<=', now());
        
        // If approval is required, only activate approved listings
        if ($requiresApproval) {
            $query->whereNotNull('approved_at');
        }
        
        $auctions = $query->get();

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
