<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Services\AuctionService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->walletService = new WalletService();
    $this->auctionService = new AuctionService($this->walletService);
});

describe('AuctionService - createAuction', function () {
    
    test('Property 7: Deposit Calculation Invariant - deposit is always 10% of base price', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $basePrice = 100000;
        
        $listing = $this->auctionService->createAuction($seller, [
            'title' => 'Test Auction',
            'base_price' => $basePrice,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
        ]);
        
        expect((float)$listing->required_deposit)->toBe($basePrice * 0.1);
    });
    
    test('Property 8: Auction Time Validation - end time must be after start time', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        expect(fn() => $this->auctionService->createAuction($seller, [
            'title' => 'Test Auction',
            'base_price' => 100000,
            'start_time' => now()->addDays(7),
            'end_time' => now()->addHour(), // Before start time
        ]))->toThrow(\InvalidArgumentException::class);
    });
    
    test('Property 9: New Auction Status Invariant - new auctions always have pending status', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = $this->auctionService->createAuction($seller, [
            'title' => 'Test Auction',
            'base_price' => 100000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
        ]);
        
        expect($listing->status)->toBe('pending');
    });
});

describe('AuctionService - startAuction', function () {
    
    test('startAuction changes status from pending to active', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        $this->auctionService->startAuction($listing);
        
        $listing->refresh();
        expect($listing->status)->toBe('active');
    });
});

describe('AuctionService - endAuction', function () {
    
    test('Property 17: Auction Ending Status Transition - status changes to ended', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subDays(7),
            'end_time' => now()->subHour(),
            'status' => 'active',
        ]);
        
        // Create a bidder
        $bidder = User::create([
            'name' => 'Bidder',
            'username' => 'bidder',
            'email' => 'bidder@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        Wallet::create(['user_id' => $bidder->id, 'balance' => 0, 'frozen' => 10000]);
        
        Bid::create([
            'listing_id' => $listing->id,
            'user_id' => $bidder->id,
            'amount' => 150000,
        ]);
        
        $this->auctionService->endAuction($listing);
        
        $listing->refresh();
        expect($listing->status)->toBe('ended');
    });
    
    test('Property 18: Top 3 Bidder Identification - winner is set correctly', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subDays(7),
            'end_time' => now()->subHour(),
            'status' => 'active',
        ]);
        
        // Create 3 bidders
        $bidder1 = User::create(['name' => 'Bidder 1', 'username' => 'bidder1', 'email' => 'bidder1@test.com', 'password' => 'password', 'role' => 'buyer']);
        $bidder2 = User::create(['name' => 'Bidder 2', 'username' => 'bidder2', 'email' => 'bidder2@test.com', 'password' => 'password', 'role' => 'buyer']);
        $bidder3 = User::create(['name' => 'Bidder 3', 'username' => 'bidder3', 'email' => 'bidder3@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder1->id, 'balance' => 0, 'frozen' => 10000]);
        Wallet::create(['user_id' => $bidder2->id, 'balance' => 0, 'frozen' => 10000]);
        Wallet::create(['user_id' => $bidder3->id, 'balance' => 0, 'frozen' => 10000]);
        
        Bid::create(['listing_id' => $listing->id, 'user_id' => $bidder1->id, 'amount' => 200000]);
        Bid::create(['listing_id' => $listing->id, 'user_id' => $bidder2->id, 'amount' => 150000]);
        Bid::create(['listing_id' => $listing->id, 'user_id' => $bidder3->id, 'amount' => 120000]);
        
        $this->auctionService->endAuction($listing);
        
        $listing->refresh();
        expect($listing->current_winner_id)->toBe($bidder1->id);
    });
    
    test('Property 20: Winner Finalization Deadline - 48 hours deadline is set', function () {
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subDays(7),
            'end_time' => now()->subHour(),
            'status' => 'active',
        ]);
        
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        Wallet::create(['user_id' => $bidder->id, 'balance' => 0, 'frozen' => 10000]);
        Bid::create(['listing_id' => $listing->id, 'user_id' => $bidder->id, 'amount' => 150000]);
        
        $beforeEnd = now();
        $this->auctionService->endAuction($listing);
        $afterEnd = now();
        
        $listing->refresh();
        
        expect($listing->finalization_deadline)->not->toBeNull();
        expect($listing->finalization_deadline->greaterThan($beforeEnd->addHours(47)))->toBeTrue();
        expect($listing->finalization_deadline->lessThan($afterEnd->addHours(49)))->toBeTrue();
    });
});

describe('AuctionService - completeWinnerPayment', function () {
    
    test('Property 24: Payment Completes Auction - status changes to completed', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $winner = User::create(['name' => 'Winner', 'username' => 'winner', 'email' => 'winner@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $seller->id, 'balance' => 0, 'frozen' => 0]);
        Wallet::create(['user_id' => $winner->id, 'balance' => 200000, 'frozen' => 10000]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subDays(7),
            'end_time' => now()->subHours(2),
            'status' => 'ended',
            'current_winner_id' => $winner->id,
            'finalization_deadline' => now()->addHours(46),
        ]);
        
        Bid::create(['listing_id' => $listing->id, 'user_id' => $winner->id, 'amount' => 150000]);
        
        $this->auctionService->completeWinnerPayment($listing, $winner);
        
        $listing->refresh();
        expect($listing->status)->toBe('completed');
    });
});
