<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\AuctionService;
use Carbon\Carbon;

class ReleaseAuctionPayments extends Command
{
    protected $signature = 'auction:release-payments';
    protected $description = 'Release frozen payments to sellers after delivery period';

    public function handle(AuctionService $auctionService)
    {
        // Get setting for auto-release days (default 7 days)
        $autoReleaseDays = (int) \App\Models\SiteSetting::get('auction_auto_release_days', 7);
        
        // Find delivered orders that are past the auto-release period
        // Only for auction orders (check if listing has required_deposit)
        // And payment not yet released
        $orders = Order::where('status', 'delivered')
            ->whereNull('payment_released_at')
            ->where('updated_at', '<=', Carbon::now()->subDays($autoReleaseDays))
            ->whereHas('items.listing', function ($query) {
                $query->whereNotNull('required_deposit');
            })
            ->get();
        
        foreach ($orders as $order) {
            try {
                $auctionService->releasePaymentToSeller($order);
                $this->info("Released payment for order #{$order->order_number}");
            } catch (\Exception $e) {
                $this->error("Failed to release payment for order #{$order->order_number}: " . $e->getMessage());
            }
        }
        
        $this->info("Processed {$orders->count()} orders");
    }
}
