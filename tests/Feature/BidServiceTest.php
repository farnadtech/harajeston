<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Services\BidService;
use App\Services\WalletService;
use App\Services\DepositService;
use App\Exceptions\Auction\DepositNotPaidException;
use App\Exceptions\Auction\InvalidBidAmountException;
use App\Exceptions\Auction\AuctionNotActiveException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->walletService = new WalletService();
    $this->depositService = new DepositService($this->walletService);
    $this->bidService = new BidService();
});

describe('BidService - placeBid', function () {
    
    test('Property 14: Bid Requires Participation - cannot bid without deposit', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        // Try to bid without participation
        expect(fn() => $this->bidService->placeBid($bidder, $listing, 150000))
            ->toThrow(DepositNotPaidException::class);
    });
    
    test('Property 15: Bid Amount Validation - bid must be higher than current highest', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder1 = User::create(['name' => 'Bidder 1', 'username' => 'bidder1', 'email' => 'bidder1@test.com', 'password' => 'password', 'role' => 'buyer']);
        $bidder2 = User::create(['name' => 'Bidder 2', 'username' => 'bidder2', 'email' => 'bidder2@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder1->id, 'balance' => 50000, 'frozen' => 0]);
        Wallet::create(['user_id' => $bidder2->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        // Both participate
        $this->depositService->participateInAuction($bidder1, $listing);
        $this->depositService->participateInAuction($bidder2, $listing);
        
        // Bidder 1 places bid
        $this->bidService->placeBid($bidder1, $listing, 150000);
        
        // Bidder 2 tries to place lower or equal bid
        expect(fn() => $this->bidService->placeBid($bidder2, $listing, 150000))
            ->toThrow(InvalidBidAmountException::class);
        
        expect(fn() => $this->bidService->placeBid($bidder2, $listing, 140000))
            ->toThrow(InvalidBidAmountException::class);
    });
    
    test('Property 16: Bid Updates Auction State - listing is updated with new highest bid', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        $this->depositService->participateInAuction($bidder, $listing);
        
        $this->bidService->placeBid($bidder, $listing, 150000);
        
        $listing->refresh();
        
        expect((float)$listing->current_highest_bid)->toBe(150000.0);
        expect($listing->highest_bidder_id)->toBe($bidder->id);
    });
    
    test('bid must be at least base price', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        $this->depositService->participateInAuction($bidder, $listing);
        
        expect(fn() => $this->bidService->placeBid($bidder, $listing, 50000))
            ->toThrow(InvalidBidAmountException::class);
    });
    
    test('cannot bid on non-active auction', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending', // Not active
        ]);
        
        $this->depositService->participateInAuction($bidder, $listing);
        
        expect(fn() => $this->bidService->placeBid($bidder, $listing, 150000))
            ->toThrow(AuctionNotActiveException::class);
    });
});

describe('BidService - getCurrentRankings', function () {
    
    test('returns rankings ordered by highest bid', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder1 = User::create(['name' => 'Bidder 1', 'username' => 'bidder1', 'email' => 'bidder1@test.com', 'password' => 'password', 'role' => 'buyer']);
        $bidder2 = User::create(['name' => 'Bidder 2', 'username' => 'bidder2', 'email' => 'bidder2@test.com', 'password' => 'password', 'role' => 'buyer']);
        $bidder3 = User::create(['name' => 'Bidder 3', 'username' => 'bidder3', 'email' => 'bidder3@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder1->id, 'balance' => 50000, 'frozen' => 0]);
        Wallet::create(['user_id' => $bidder2->id, 'balance' => 50000, 'frozen' => 0]);
        Wallet::create(['user_id' => $bidder3->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        $this->depositService->participateInAuction($bidder1, $listing);
        $this->depositService->participateInAuction($bidder2, $listing);
        $this->depositService->participateInAuction($bidder3, $listing);
        
        $this->bidService->placeBid($bidder1, $listing, 150000);
        $this->bidService->placeBid($bidder2, $listing, 160000);
        $this->bidService->placeBid($bidder3, $listing, 180000);
        $this->bidService->placeBid($bidder1, $listing, 200000); // Bidder1 bids again higher
        
        $rankings = $this->bidService->getCurrentRankings($listing);
        
        expect($rankings)->toHaveCount(3);
        expect($rankings[0]['rank'])->toBe(1);
        expect($rankings[0]['user']->id)->toBe($bidder1->id);
        expect((float)$rankings[0]['amount'])->toBe(200000.0);
        
        expect($rankings[1]['rank'])->toBe(2);
        expect($rankings[1]['user']->id)->toBe($bidder3->id);
        expect((float)$rankings[1]['amount'])->toBe(180000.0);
        
        expect($rankings[2]['rank'])->toBe(3);
        expect($rankings[2]['user']->id)->toBe($bidder2->id);
        expect((float)$rankings[2]['amount'])->toBe(160000.0);
    });
    
    test('returns only highest bid per user', function () {
        $seller = User::create(['name' => 'Seller', 'username' => 'seller', 'email' => 'seller@test.com', 'password' => 'password', 'role' => 'seller']);
        $bidder = User::create(['name' => 'Bidder', 'username' => 'bidder', 'email' => 'bidder@test.com', 'password' => 'password', 'role' => 'buyer']);
        
        Wallet::create(['user_id' => $bidder->id, 'balance' => 50000, 'frozen' => 0]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->subHour(),
            'end_time' => now()->addDays(7),
            'status' => 'active',
        ]);
        
        $this->depositService->participateInAuction($bidder, $listing);
        
        // Place multiple bids
        $this->bidService->placeBid($bidder, $listing, 150000);
        $this->bidService->placeBid($bidder, $listing, 160000);
        $this->bidService->placeBid($bidder, $listing, 170000);
        
        $rankings = $this->bidService->getCurrentRankings($listing);
        
        expect($rankings)->toHaveCount(1);
        expect((float)$rankings[0]['amount'])->toBe(170000.0);
    });
});
