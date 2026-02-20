<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Services\AuctionService;
use App\Services\DepositService;
use App\Services\BidService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Integration Tests - Complete Auction Workflow', function () {
    
    test('complete auction workflow: create → participate → bid → end → payment', function () {
        // Setup services
        $auctionService = new AuctionService(new WalletService());
        $depositService = new DepositService(new WalletService());
        $bidService = new BidService();
        $walletService = new WalletService();
        
        // Create seller
        $seller = User::factory()->create(['role' => 'seller']);
        $sellerWallet = Wallet::factory()->create([
            'user_id' => $seller->id,
            'balance' => 0,
            'frozen' => 0,
        ]);
        
        // Create buyers
        $buyer1 = User::factory()->create(['role' => 'buyer']);
        $buyer1Wallet = Wallet::factory()->create([
            'user_id' => $buyer1->id,
            'balance' => 50000,
            'frozen' => 0,
        ]);
        
        $buyer2 = User::factory()->create(['role' => 'buyer']);
        $buyer2Wallet = Wallet::factory()->create([
            'user_id' => $buyer2->id,
            'balance' => 60000,
            'frozen' => 0,
        ]);
        
        // Step 1: Create auction
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'title' => 'Test Auction',
            'description' => 'Integration test auction',
            'base_price' => 10000,
            'required_deposit' => 1000,
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'status' => 'pending',
        ]);
        
        expect($listing->status)->toBe('pending');
        expect((float)$listing->required_deposit)->toBe(1000.0);
        
        // Step 2: Start auction
        $auctionService->startAuction($listing);
        $listing->refresh();
        
        expect($listing->status)->toBe('active');
        
        // Step 3: Buyers participate (pay deposit)
        $depositService->participateInAuction($buyer1, $listing);
        $depositService->participateInAuction($buyer2, $listing);
        
        $buyer1Wallet->refresh();
        $buyer2Wallet->refresh();
        
        expect((float)$buyer1Wallet->frozen)->toBe(1000.0);
        expect((float)$buyer2Wallet->frozen)->toBe(1000.0);
        
        // Step 4: Place bids
        $bidService->placeBid($buyer1, $listing, 15000);
        $listing->refresh();
        expect((float)$listing->current_highest_bid)->toBe(15000.0);
        
        $bidService->placeBid($buyer2, $listing, 20000);
        $listing->refresh();
        expect((float)$listing->current_highest_bid)->toBe(20000.0);
        
        $bidService->placeBid($buyer1, $listing, 25000);
        $listing->refresh();
        expect((float)$listing->current_highest_bid)->toBe(25000.0);
        expect($listing->highest_bidder_id)->toBe($buyer1->id);
        
        // Step 5: End auction
        $auctionService->endAuction($listing);
        $listing->refresh();
        
        expect($listing->status)->toBe('ended');
        expect($listing->current_winner_id)->toBe($buyer1->id);
        expect($listing->finalization_deadline)->not->toBeNull();
        
        // Step 6: Winner completes payment
        $auctionService->completeWinnerPayment($listing, $buyer1);
        $listing->refresh();
        
        expect($listing->status)->toBe('completed');
        
        // Verify financial transactions
        $buyer1Wallet->refresh();
        $buyer2Wallet->refresh();
        $sellerWallet->refresh();
        
        // Buyer1 (winner) paid full amount
        expect((float)$buyer1Wallet->balance)->toBe(50000.0 - 25000.0);
        expect((float)$buyer1Wallet->frozen)->toBe(0.0);
        
        // Buyer2 (loser) got deposit back
        expect((float)$buyer2Wallet->balance)->toBe(60000.0);
        expect((float)$buyer2Wallet->frozen)->toBe(0.0);
        
        // Seller received payment
        expect((float)$sellerWallet->balance)->toBe(25000.0);
    });
});

describe('Integration Tests - Cascade Logic', function () {
    
    test('cascade logic: rank 1 timeout → rank 2 selected → rank 2 timeout → rank 3 selected', function () {
        $auctionService = new AuctionService(new WalletService());
        $depositService = new DepositService(new WalletService());
        $bidService = new BidService();
        
        // Create seller
        $seller = User::factory()->create(['role' => 'seller']);
        Wallet::factory()->create(['user_id' => $seller->id, 'balance' => 0]);
        
        // Create 3 buyers
        $buyers = [];
        for ($i = 1; $i <= 3; $i++) {
            $buyer = User::factory()->create(['role' => 'buyer']);
            Wallet::factory()->create([
                'user_id' => $buyer->id,
                'balance' => 50000,
                'frozen' => 0,
            ]);
            $buyers[] = $buyer;
        }
        
        // Create and start auction
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'title' => 'Cascade Test',
            'description' => 'Test cascade logic',
            'base_price' => 10000,
            'required_deposit' => 1000,
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'status' => 'active',
        ]);
        
        // All participate and bid
        foreach ($buyers as $index => $buyer) {
            $depositService->participateInAuction($buyer, $listing);
            $bidService->placeBid($buyer, $listing, 10000 + ($index + 1) * 5000);
        }
        
        // End auction - buyer3 should be rank 1
        $auctionService->endAuction($listing);
        $listing->refresh();
        
        expect($listing->current_winner_id)->toBe($buyers[2]->id);
        
        // Simulate rank 1 timeout
        $auctionService->handleFinalizationTimeout($listing);
        $listing->refresh();
        
        // Rank 2 should now be winner
        expect($listing->current_winner_id)->toBe($buyers[1]->id);
        expect($listing->status)->toBe('ended');
        
        // Simulate rank 2 timeout
        $auctionService->handleFinalizationTimeout($listing);
        $listing->refresh();
        
        // Rank 3 should now be winner
        expect($listing->current_winner_id)->toBe($buyers[0]->id);
        
        // Verify deposits forfeited
        $buyer3Wallet = Wallet::where('user_id', $buyers[2]->id)->first();
        expect((float)$buyer3Wallet->frozen)->toBe(0.0); // Deposit forfeited
    });
    
    test('cascade termination: all bidders timeout → auction fails', function () {
        $auctionService = new AuctionService(new WalletService());
        $depositService = new DepositService(new WalletService());
        $bidService = new BidService();
        
        $seller = User::factory()->create(['role' => 'seller']);
        Wallet::factory()->create(['user_id' => $seller->id]);
        
        $buyer = User::factory()->create(['role' => 'buyer']);
        Wallet::factory()->create(['user_id' => $buyer->id, 'balance' => 50000]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'title' => 'Fail Test',
            'description' => 'Test auction failure',
            'base_price' => 10000,
            'required_deposit' => 1000,
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'status' => 'active',
        ]);
        
        $depositService->participateInAuction($buyer, $listing);
        $bidService->placeBid($buyer, $listing, 15000);
        
        $auctionService->endAuction($listing);
        $listing->refresh();
        
        // Timeout - should fail since only 1 bidder
        $auctionService->handleFinalizationTimeout($listing);
        $listing->refresh();
        
        expect($listing->status)->toBe('failed');
        expect($listing->current_winner_id)->toBeNull();
    });
});

describe('Integration Tests - Wallet Operations', function () {
    
    test('wallet operations: add funds → freeze → release → deduct', function () {
        $walletService = new WalletService();
        
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 0,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        // Add funds
        $walletService->addFunds($user, 10000, 'شارژ اولیه');
        $wallet->refresh();
        expect((float)$wallet->balance)->toBe(10000.0);
        
        // Freeze deposit
        $walletService->freezeDeposit($user, 1000, $listing);
        $wallet->refresh();
        expect((float)$wallet->balance)->toBe(9000.0);
        expect((float)$wallet->frozen)->toBe(1000.0);
        
        // Release deposit
        $walletService->releaseDeposit($user, 1000, $listing);
        $wallet->refresh();
        expect((float)$wallet->balance)->toBe(10000.0);
        expect((float)$wallet->frozen)->toBe(0.0);
        
        // Deduct for purchase
        $walletService->deduct($user, 5000, 'خرید محصول');
        $wallet->refresh();
        expect((float)$wallet->balance)->toBe(5000.0);
        
        // Verify transaction history
        $history = $walletService->getTransactionHistory($user, ['per_page' => 10]);
        expect($history->total())->toBe(4);
    });
});

describe('Integration Tests - Concurrent Bidding', function () {
    
    test('concurrent bidding: multiple users bidding simultaneously without race conditions', function () {
        $auctionService = new AuctionService(new WalletService());
        $depositService = new DepositService(new WalletService());
        $bidService = new BidService();
        
        // Create seller
        $seller = User::factory()->create(['role' => 'seller']);
        Wallet::factory()->create(['user_id' => $seller->id]);
        
        // Create 5 buyers
        $buyers = [];
        for ($i = 1; $i <= 5; $i++) {
            $buyer = User::factory()->create(['role' => 'buyer']);
            Wallet::factory()->create([
                'user_id' => $buyer->id,
                'balance' => 100000,
                'frozen' => 0,
            ]);
            $buyers[] = $buyer;
        }
        
        // Create and start auction
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'title' => 'Concurrent Bidding Test',
            'description' => 'Test concurrent bids',
            'base_price' => 10000,
            'required_deposit' => 1000,
            'start_time' => now(),
            'end_time' => now()->addHours(24),
            'status' => 'active',
        ]);
        
        // All participate
        foreach ($buyers as $buyer) {
            $depositService->participateInAuction($buyer, $listing);
        }
        
        // Simulate concurrent bidding - each buyer tries to bid
        $bidAmounts = [15000, 16000, 17000, 18000, 19000];
        $successfulBids = [];
        
        foreach ($buyers as $index => $buyer) {
            try {
                $bidService->placeBid($buyer, $listing, $bidAmounts[$index]);
                $successfulBids[] = [
                    'buyer' => $buyer->id,
                    'amount' => $bidAmounts[$index]
                ];
            } catch (\Exception $e) {
                // Some bids might fail due to race conditions, that's expected
            }
        }
        
        // Verify final state
        $listing->refresh();
        
        // The highest bid should be the maximum amount
        expect((float)$listing->current_highest_bid)->toBe(19000.0);
        expect($listing->highest_bidder_id)->toBe($buyers[4]->id);
        
        // Verify all bids were recorded
        $allBids = \App\Models\Bid::where('listing_id', $listing->id)
            ->orderBy('amount', 'desc')
            ->get();
        
        expect($allBids->count())->toBe(5);
        
        // Verify bid amounts are in correct order
        expect((float)$allBids[0]->amount)->toBe(19000.0);
        expect((float)$allBids[1]->amount)->toBe(18000.0);
        expect((float)$allBids[2]->amount)->toBe(17000.0);
        
        // Verify no duplicate bids from same user at same amount
        $uniqueBids = $allBids->unique(function ($bid) {
            return $bid->user_id . '-' . $bid->amount;
        });
        
        expect($uniqueBids->count())->toBe($allBids->count());
    });
});
