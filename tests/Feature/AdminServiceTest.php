<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\AdminService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->adminService = app(AdminService::class);
    $this->walletService = app(WalletService::class);
});

/**
 * Property 33: Admin Statistics Accuracy
 * Validates: Requirements 13.1
 * 
 * Property: Admin statistics accurately reflect database state
 */
test('property 33: admin statistics are accurate', function () {
    // Create test data
    $users = User::factory()->count(5)->create();
    $sellers = User::factory()->count(3)->create(['role' => 'seller', 'username' => fn() => 'seller' . rand(1000, 9999)]);
    
    // Create wallets
    foreach ($users->merge($sellers) as $user) {
        $this->walletService->createWallet($user);
    }
    
    // Create listings
    $activeListings = Listing::factory()->count(10)->create([
        'seller_id' => $sellers->random()->id,
        'status' => 'active',
        'type' => 'direct_sale',
    ]);
    
    $endedListings = Listing::factory()->count(5)->create([
        'seller_id' => $sellers->random()->id,
        'status' => 'ended',
        'type' => 'auction',
    ]);
    
    // Create orders
    $completedOrders = Order::factory()->count(7)->create([
        'buyer_id' => $users->random()->id,
        'seller_id' => $sellers->random()->id,
        'status' => 'delivered',
        'total' => 1000,
    ]);
    
    $pendingOrders = Order::factory()->count(3)->create([
        'buyer_id' => $users->random()->id,
        'seller_id' => $sellers->random()->id,
        'status' => 'pending',
        'total' => 500,
    ]);
    
    // Create wallet transactions
    foreach ($users->take(3) as $user) {
        $this->walletService->addFunds($user, 1000, 'تست');
    }
    
    // Get statistics
    $stats = $this->adminService->getStatistics();
    
    // Verify statistics accuracy
    expect($stats['active_listings'])->toBe(10);
    expect($stats['total_users'])->toBe(8); // 5 buyers + 3 sellers
    expect($stats['total_orders'])->toBe(10); // 7 completed + 3 pending
    expect($stats['pending_orders'])->toBe(3);
    
    // Transaction volume should include all wallet transactions
    expect($stats['transaction_volume'])->toBeGreaterThan(0);
    
    // Revenue should only include delivered/shipped orders
    expect($stats['revenue'])->toBe(7000.0); // 7 orders * 1000
});

test('admin statistics count active listings correctly', function () {
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'testseller']);
    $this->walletService->createWallet($seller);
    
    // Create various listing statuses
    Listing::factory()->count(5)->create(['seller_id' => $seller->id, 'status' => 'active', 'type' => 'auction']);
    Listing::factory()->count(3)->create(['seller_id' => $seller->id, 'status' => 'ended', 'type' => 'auction']);
    Listing::factory()->count(2)->create(['seller_id' => $seller->id, 'status' => 'pending', 'type' => 'direct_sale']);
    
    $count = $this->adminService->getActiveListingsCount();
    
    expect($count)->toBe(5);
});

test('admin statistics count total users correctly', function () {
    User::factory()->count(10)->create();
    User::factory()->count(5)->create(['role' => 'seller', 'username' => fn() => 'seller' . rand(1000, 9999)]);
    
    $count = $this->adminService->getTotalUsersCount();
    
    expect($count)->toBe(15);
});

test('admin statistics calculate transaction volume correctly', function () {
    $users = User::factory()->count(3)->create();
    
    foreach ($users as $user) {
        $this->walletService->createWallet($user);
        $this->walletService->addFunds($user, 1000, 'تست');
    }
    
    $volume = $this->adminService->getTransactionVolume();
    
    expect($volume)->toBe(3000.0);
});

test('admin statistics calculate revenue correctly', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    
    // Create orders with different statuses
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'delivered',
        'total' => 1000,
    ]);
    
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'shipped',
        'total' => 500,
    ]);
    
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'pending',
        'total' => 300,
    ]);
    
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'cancelled',
        'total' => 200,
    ]);
    
    $revenue = $this->adminService->getTotalRevenue();
    
    // Only delivered and shipped orders count
    expect($revenue)->toBe(1500.0);
});

test('admin statistics filter by date range', function () {
    $buyer = User::factory()->create();
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    
    // Create old order
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'delivered',
        'total' => 1000,
        'created_at' => now()->subDays(10),
    ]);
    
    // Create recent order
    Order::factory()->create([
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'status' => 'delivered',
        'total' => 500,
        'created_at' => now()->subDay(),
    ]);
    
    $stats = $this->adminService->getStatisticsByDateRange(
        now()->subDays(2)->toDateString(),
        now()->toDateString()
    );
    
    expect($stats['new_orders'])->toBe(1);
    expect($stats['revenue'])->toBe(500.0);
});

test('admin statistics return zero for empty database', function () {
    $stats = $this->adminService->getStatistics();
    
    expect($stats['active_listings'])->toBe(0);
    expect($stats['total_users'])->toBe(0);
    expect($stats['transaction_volume'])->toBe(0.0);
    expect($stats['total_orders'])->toBe(0);
    expect($stats['pending_orders'])->toBe(0);
    expect($stats['revenue'])->toBe(0.0);
});


/**
 * Property 34: Admin Auction Cancellation
 * Validates: Requirements 13.4
 * 
 * Property: Admin can cancel auction and all deposits are released
 */
test('property 34: admin cancellation releases all deposits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    $buyers = User::factory()->count(3)->create();
    
    // Create wallets
    $this->walletService->createWallet($seller);
    foreach ($buyers as $buyer) {
        $this->walletService->createWallet($buyer);
        $this->walletService->addFunds($buyer, 1000, 'تست');
    }
    
    // Create auction
    $auctionService = app(\App\Services\AuctionService::class);
    $depositService = app(\App\Services\DepositService::class);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000,
        'required_deposit' => 100,
    ]);
    
    // All buyers participate
    foreach ($buyers as $buyer) {
        $depositService->participateInAuction($buyer, $listing);
    }
    
    // Verify deposits are frozen
    foreach ($buyers as $buyer) {
        expect($buyer->wallet->fresh()->frozen)->toBe('100.00');
        expect($buyer->wallet->fresh()->balance)->toBe('900.00');
    }
    
    // Admin cancels auction
    $auctionService->cancelAuctionByAdmin($listing, $admin, 'تست لغو توسط ادمین');
    
    // Verify all deposits are released
    foreach ($buyers as $buyer) {
        expect($buyer->wallet->fresh()->frozen)->toBe('0.00');
        expect($buyer->wallet->fresh()->balance)->toBe('1000.00');
    }
    
    // Verify auction status is failed
    expect($listing->fresh()->status)->toBe('failed');
    
    // Verify admin action was logged
    $log = \App\Models\AdminActionLog::where('admin_id', $admin->id)
        ->where('action', 'cancel_auction')
        ->first();
    
    expect($log)->not->toBeNull();
    expect($log->target_id)->toBe($listing->id);
    expect($log->reason)->toBe('تست لغو توسط ادمین');
});

test('admin can cancel auction with no participants', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    $this->walletService->createWallet($seller);
    
    $auctionService = app(\App\Services\AuctionService::class);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000,
        'required_deposit' => 100,
    ]);
    
    // Admin cancels auction with no participants
    $auctionService->cancelAuctionByAdmin($listing, $admin, 'لغو بدون شرکت‌کننده');
    
    // Verify auction status is failed
    expect($listing->fresh()->status)->toBe('failed');
});

test('admin cancellation only releases frozen deposits', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    $buyer1 = User::factory()->create();
    $buyer2 = User::factory()->create();
    
    // Create wallets
    $this->walletService->createWallet($seller);
    $this->walletService->createWallet($buyer1);
    $this->walletService->createWallet($buyer2);
    $this->walletService->addFunds($buyer1, 1000, 'تست');
    $this->walletService->addFunds($buyer2, 1000, 'تست');
    
    $auctionService = app(\App\Services\AuctionService::class);
    $depositService = app(\App\Services\DepositService::class);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000,
        'required_deposit' => 100,
    ]);
    
    // Only buyer1 participates
    $depositService->participateInAuction($buyer1, $listing);
    
    // Admin cancels auction
    $auctionService->cancelAuctionByAdmin($listing, $admin, 'تست');
    
    // Verify only buyer1's deposit is released
    expect($buyer1->wallet->fresh()->frozen)->toBe('0.00');
    expect($buyer1->wallet->fresh()->balance)->toBe('1000.00');
    
    // Buyer2 should be unaffected
    expect($buyer2->wallet->fresh()->frozen)->toBe('0.00');
    expect($buyer2->wallet->fresh()->balance)->toBe('1000.00');
});


/**
 * Property 35: Admin Action Audit Logging
 * Validates: Requirements 13.6
 * 
 * Property: All admin actions are logged with complete context
 */
test('property 35: admin actions are logged with context', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
    $this->walletService->createWallet($seller);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
    ]);
    
    // Log an admin action
    $adminService = $this->adminService;
    $log = $adminService->logAction(
        $admin,
        'cancel_auction',
        $listing,
        ['participants_count' => 5, 'deposits_released' => 500],
        'تست لغو مزایده'
    );
    
    // Verify log was created
    expect($log)->not->toBeNull();
    expect($log->admin_id)->toBe($admin->id);
    expect($log->action)->toBe('cancel_auction');
    expect($log->target_type)->toBe(Listing::class);
    expect($log->target_id)->toBe($listing->id);
    expect($log->context)->toBe(['participants_count' => 5, 'deposits_released' => 500]);
    expect($log->reason)->toBe('تست لغو مزایده');
    expect($log->ip_address)->not->toBeNull();
});

test('admin action logs can be filtered', function () {
    $admin1 = User::factory()->create(['role' => 'admin']);
    $admin2 = User::factory()->create(['role' => 'admin']);
    
    // Create logs for different admins and actions
    $this->adminService->logAction($admin1, 'cancel_auction', null, [], 'دلیل 1');
    $this->adminService->logAction($admin1, 'release_deposit', null, [], 'دلیل 2');
    $this->adminService->logAction($admin2, 'cancel_auction', null, [], 'دلیل 3');
    
    // Filter by admin
    $logs = $this->adminService->getActionLogs(['admin_id' => $admin1->id]);
    expect($logs->total())->toBe(2);
    
    // Filter by action
    $logs = $this->adminService->getActionLogs(['action' => 'cancel_auction']);
    expect($logs->total())->toBe(2);
});

test('admin action logs are ordered by date descending', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    // Create logs at different times
    $log1 = $this->adminService->logAction($admin, 'action1', null, [], 'اول');
    sleep(1);
    $log2 = $this->adminService->logAction($admin, 'action2', null, [], 'دوم');
    sleep(1);
    $log3 = $this->adminService->logAction($admin, 'action3', null, [], 'سوم');
    
    $logs = $this->adminService->getActionLogs();
    
    // Most recent should be first
    expect($logs->first()->id)->toBe($log3->id);
    expect($logs->last()->id)->toBe($log1->id);
});
