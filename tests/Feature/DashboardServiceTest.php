<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Bid;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->dashboardService = new DashboardService();
});

describe('DashboardService - Seller Statistics', function () {
    
    test('Property 45: Dashboard Statistics Accuracy - seller statistics are accurate', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $buyer = User::factory()->create(['role' => 'buyer']);
        
        Wallet::factory()->create(['user_id' => $seller->id]);
        Wallet::factory()->create(['user_id' => $buyer->id]);
        
        // Create active auction
        $activeAuction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'active',
        ]);
        
        // Create active direct sale
        $activeDirectSale = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'direct_sale',
            'status' => 'active',
            'price' => 5000,
            'stock' => 10,
        ]);
        
        // Create completed auction
        $completedAuction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'completed',
        ]);
        
        // Create orders
        $order1 = Order::create([
            'order_number' => 'ORD-001',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'delivered',
            'subtotal' => 10000,
            'shipping_cost' => 500,
            'total' => 10500,
            'shipping_address' => 'تهران، خیابان ولیعصر',
        ]);
        
        $order2 = Order::create([
            'order_number' => 'ORD-002',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'pending',
            'subtotal' => 5000,
            'shipping_cost' => 300,
            'total' => 5300,
            'shipping_address' => 'تهران، خیابان آزادی',
        ]);
        
        // Get statistics
        $stats = $this->dashboardService->getSellerStatistics($seller);
        
        // Verify accuracy
        expect($stats['active_auctions'])->toBe(1);
        expect($stats['active_direct_sales'])->toBe(1);
        expect($stats['completed_auctions'])->toBe(1);
        expect($stats['total_orders'])->toBe(2);
        expect($stats['pending_orders'])->toBe(1);
        expect($stats['total_sales'])->toBe(1); // Only delivered
        expect($stats['total_revenue'])->toBe(10500.0); // Only delivered
    });
    
    test('seller statistics returns zero for new seller', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        Wallet::factory()->create(['user_id' => $seller->id]);
        
        $stats = $this->dashboardService->getSellerStatistics($seller);
        
        expect($stats['total_sales'])->toBe(0);
        expect($stats['active_auctions'])->toBe(0);
        expect($stats['active_direct_sales'])->toBe(0);
        expect($stats['completed_auctions'])->toBe(0);
        expect($stats['total_orders'])->toBe(0);
        expect($stats['pending_orders'])->toBe(0);
        expect($stats['total_revenue'])->toBe(0.0);
    });
});

describe('DashboardService - Buyer Statistics', function () {
    
    test('Property 45: Dashboard Statistics Accuracy - buyer statistics are accurate', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        $buyerWallet = Wallet::factory()->create([
            'user_id' => $buyer->id,
            'balance' => 5000,
            'frozen' => 2000,
        ]);
        
        Wallet::factory()->create(['user_id' => $seller->id]);
        
        // Create active auction with bid
        $activeAuction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'active',
        ]);
        
        Bid::create([
            'listing_id' => $activeAuction->id,
            'user_id' => $buyer->id,
            'amount' => 10000,
        ]);
        
        // Create won auction
        $wonAuction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'completed',
            'current_winner_id' => $buyer->id,
        ]);
        
        // Create orders
        $order1 = Order::create([
            'order_number' => 'ORD-001',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'delivered',
            'subtotal' => 10000,
            'shipping_cost' => 500,
            'total' => 10500,
            'shipping_address' => 'تهران، خیابان ولیعصر',
        ]);
        
        $order2 = Order::create([
            'order_number' => 'ORD-002',
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'pending',
            'subtotal' => 5000,
            'shipping_cost' => 300,
            'total' => 5300,
            'shipping_address' => 'تهران، خیابان آزادی',
        ]);
        
        // Get statistics
        $stats = $this->dashboardService->getBuyerStatistics($buyer);
        
        // Verify accuracy
        expect($stats['active_bids'])->toBe(1);
        expect($stats['won_auctions'])->toBe(1);
        expect($stats['total_purchases'])->toBe(1); // Only delivered
        expect($stats['frozen_balance'])->toBe(2000.0);
        expect($stats['pending_orders'])->toBe(1);
        expect($stats['total_spent'])->toBe(10500.0); // Only delivered
    });
    
    test('buyer statistics returns zero for new buyer', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        Wallet::factory()->create([
            'user_id' => $buyer->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $stats = $this->dashboardService->getBuyerStatistics($buyer);
        
        expect($stats['active_bids'])->toBe(0);
        expect($stats['won_auctions'])->toBe(0);
        expect($stats['total_purchases'])->toBe(0);
        expect($stats['frozen_balance'])->toBe(0.0);
        expect($stats['pending_orders'])->toBe(0);
        expect($stats['total_spent'])->toBe(0.0);
    });
    
    test('buyer statistics counts multiple bids on same auction as one', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        
        Wallet::factory()->create(['user_id' => $buyer->id]);
        Wallet::factory()->create(['user_id' => $seller->id]);
        
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'active',
        ]);
        
        // Create multiple bids on same auction
        Bid::create([
            'listing_id' => $auction->id,
            'user_id' => $buyer->id,
            'amount' => 10000,
        ]);
        
        Bid::create([
            'listing_id' => $auction->id,
            'user_id' => $buyer->id,
            'amount' => 12000,
        ]);
        
        $stats = $this->dashboardService->getBuyerStatistics($buyer);
        
        // Should count as 1 active bid (distinct listing)
        expect($stats['active_bids'])->toBe(1);
    });
});
