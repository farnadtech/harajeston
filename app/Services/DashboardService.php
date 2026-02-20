<?php

namespace App\Services;

use App\Models\User;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get seller dashboard statistics
     * 
     * @param User $seller
     * @return array
     */
    public function getSellerStatistics(User $seller): array
    {
        return [
            'total_sales' => $this->getSellerTotalSales($seller),
            'active_auctions' => $this->getSellerActiveAuctions($seller),
            'active_direct_sales' => $this->getSellerActiveDirectSales($seller),
            'completed_auctions' => $this->getSellerCompletedAuctions($seller),
            'total_orders' => $this->getSellerTotalOrders($seller),
            'pending_orders' => $this->getSellerPendingOrders($seller),
            'total_revenue' => $this->getSellerTotalRevenue($seller),
        ];
    }
    
    /**
     * Get buyer dashboard statistics
     * 
     * @param User $buyer
     * @return array
     */
    public function getBuyerStatistics(User $buyer): array
    {
        return [
            'active_bids' => $this->getBuyerActiveBids($buyer),
            'won_auctions' => $this->getBuyerWonAuctions($buyer),
            'total_purchases' => $this->getBuyerTotalPurchases($buyer),
            'frozen_balance' => $this->getBuyerFrozenBalance($buyer),
            'pending_orders' => $this->getBuyerPendingOrders($buyer),
            'total_spent' => $this->getBuyerTotalSpent($buyer),
        ];
    }
    
    /**
     * Get seller's total sales count
     */
    private function getSellerTotalSales(User $seller): int
    {
        return Order::where('seller_id', $seller->id)
            ->whereIn('status', ['delivered', 'shipped'])
            ->count();
    }
    
    /**
     * Get seller's active auctions count
     */
    private function getSellerActiveAuctions(User $seller): int
    {
        return Listing::where('seller_id', $seller->id)
            ->where('type', 'auction')
            ->where('status', 'active')
            ->count();
    }
    
    /**
     * Get seller's active direct sales count
     */
    private function getSellerActiveDirectSales(User $seller): int
    {
        return Listing::where('seller_id', $seller->id)
            ->whereIn('type', ['direct_sale', 'hybrid'])
            ->where('status', 'active')
            ->count();
    }
    
    /**
     * Get seller's completed auctions count
     */
    private function getSellerCompletedAuctions(User $seller): int
    {
        return Listing::where('seller_id', $seller->id)
            ->where('type', 'auction')
            ->where('status', 'completed')
            ->count();
    }
    
    /**
     * Get seller's total orders count
     */
    private function getSellerTotalOrders(User $seller): int
    {
        return Order::where('seller_id', $seller->id)->count();
    }
    
    /**
     * Get seller's pending orders count
     */
    private function getSellerPendingOrders(User $seller): int
    {
        return Order::where('seller_id', $seller->id)
            ->where('status', 'pending')
            ->count();
    }
    
    /**
     * Get seller's total revenue
     */
    private function getSellerTotalRevenue(User $seller): float
    {
        return (float) Order::where('seller_id', $seller->id)
            ->whereIn('status', ['delivered', 'shipped'])
            ->sum('total');
    }
    
    /**
     * Get buyer's active bids count
     */
    private function getBuyerActiveBids(User $buyer): int
    {
        return Bid::where('user_id', $buyer->id)
            ->whereHas('listing', function ($query) {
                $query->where('status', 'active');
            })
            ->distinct('listing_id')
            ->count('listing_id');
    }
    
    /**
     * Get buyer's won auctions count
     */
    private function getBuyerWonAuctions(User $buyer): int
    {
        return Listing::where('current_winner_id', $buyer->id)
            ->where('status', 'completed')
            ->count();
    }
    
    /**
     * Get buyer's total purchases count
     */
    private function getBuyerTotalPurchases(User $buyer): int
    {
        return Order::where('buyer_id', $buyer->id)
            ->whereIn('status', ['delivered', 'shipped'])
            ->count();
    }
    
    /**
     * Get buyer's frozen balance
     */
    private function getBuyerFrozenBalance(User $buyer): float
    {
        return (float) $buyer->wallet->frozen ?? 0;
    }
    
    /**
     * Get buyer's pending orders count
     */
    private function getBuyerPendingOrders(User $buyer): int
    {
        return Order::where('buyer_id', $buyer->id)
            ->where('status', 'pending')
            ->count();
    }
    
    /**
     * Get buyer's total spent amount
     */
    private function getBuyerTotalSpent(User $buyer): float
    {
        return (float) Order::where('buyer_id', $buyer->id)
            ->whereIn('status', ['delivered', 'shipped', 'processing'])
            ->sum('total');
    }
}
