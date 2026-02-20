<?php

use App\Jobs\ProcessAuctionStarting;
use App\Jobs\ProcessAuctionEnding;
use App\Jobs\ProcessFinalizationTimeout;
use App\Models\Listing;
use App\Models\User;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Services\AuctionService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

describe('Scheduled Jobs', function () {
    beforeEach(function () {
        $this->auctionService = app(AuctionService::class);
        $this->walletService = app(WalletService::class);
    });

    test('ProcessAuctionStarting starts pending auctions when start_time reached', function () {
        // Create pending auction with start_time in the past
        $seller = User::factory()->create(['role' => 'seller']);
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'pending',
            'start_time' => now()->subMinute(),
            'end_time' => now()->addDay(),
        ]);

        // Execute job
        $job = new ProcessAuctionStarting();
        $job->handle($this->auctionService);

        // Assert auction status changed to active
        expect($auction->fresh()->status)->toBe('active');
    });

    test('ProcessAuctionStarting does not start auctions with future start_time', function () {
        // Create pending auction with start_time in the future
        $seller = User::factory()->create(['role' => 'seller']);
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'pending',
            'start_time' => now()->addHour(),
            'end_time' => now()->addDay(),
        ]);

        // Execute job
        $job = new ProcessAuctionStarting();
        $job->handle($this->auctionService);

        // Assert auction status remains pending
        expect($auction->fresh()->status)->toBe('pending');
    });

    test('ProcessAuctionEnding ends active auctions when end_time reached', function () {
        // Create active auction with end_time in the past
        $seller = User::factory()->create(['role' => 'seller']);
        $basePrice = 50000;
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'active',
            'base_price' => $basePrice,
            'required_deposit' => $basePrice * 0.1,
            'start_time' => now()->subDay(),
            'end_time' => now()->subMinute(),
        ]);

        // Add some bids
        $bidder = User::factory()->create(['role' => 'buyer']);
        \App\Models\Wallet::create(['user_id' => $bidder->id, 'balance' => 0, 'frozen' => 0]);
        $this->walletService->addFunds($bidder, 100000, 'Initial funds');
        
        AuctionParticipation::create([
            'listing_id' => $auction->id,
            'user_id' => $bidder->id,
            'deposit_amount' => $auction->required_deposit,
            'deposit_status' => 'paid',
        ]);
        
        $this->walletService->freezeDeposit($bidder, $auction->required_deposit, $auction);
        
        Bid::create([
            'listing_id' => $auction->id,
            'user_id' => $bidder->id,
            'amount' => $auction->base_price + 1000,
        ]);

        // Execute job
        $job = new ProcessAuctionEnding();
        $job->handle($this->auctionService);

        // Assert auction status changed to ended
        expect($auction->fresh()->status)->toBe('ended');
    });

    test('ProcessAuctionEnding does not end auctions with future end_time', function () {
        // Create active auction with end_time in the future
        $seller = User::factory()->create(['role' => 'seller']);
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'active',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);

        // Execute job
        $job = new ProcessAuctionEnding();
        $job->handle($this->auctionService);

        // Assert auction status remains active
        expect($auction->fresh()->status)->toBe('active');
    });

    test('ProcessFinalizationTimeout handles timeout for ended auctions', function () {
        // Create ended auction with finalization_deadline in the past
        $seller = User::factory()->create(['role' => 'seller']);
        \App\Models\Wallet::create(['user_id' => $seller->id, 'balance' => 0, 'frozen' => 0]);
        
        $basePrice = 50000;
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'ended',
            'base_price' => $basePrice,
            'required_deposit' => $basePrice * 0.1,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDay(),
            'finalization_deadline' => now()->subHour(),
        ]);

        // Add winner
        $winner = User::factory()->create(['role' => 'buyer']);
        \App\Models\Wallet::create(['user_id' => $winner->id, 'balance' => 0, 'frozen' => 0]);
        $this->walletService->addFunds($winner, 100000, 'Initial funds');
        
        AuctionParticipation::create([
            'listing_id' => $auction->id,
            'user_id' => $winner->id,
            'deposit_amount' => $auction->required_deposit,
            'deposit_status' => 'paid',
        ]);
        
        $this->walletService->freezeDeposit($winner, $auction->required_deposit, $auction);
        
        Bid::create([
            'listing_id' => $auction->id,
            'user_id' => $winner->id,
            'amount' => $auction->base_price + 1000,
        ]);

        $auction->current_winner_id = $winner->id;
        $auction->save();

        // Execute job
        $job = new ProcessFinalizationTimeout();
        $job->handle($this->auctionService);

        // Assert auction status changed to failed (no more bidders)
        expect($auction->fresh()->status)->toBe('failed');
    });

    test('ProcessFinalizationTimeout does not process auctions with future deadline', function () {
        // Create ended auction with finalization_deadline in the future
        $seller = User::factory()->create(['role' => 'seller']);
        $auction = Listing::factory()->create([
            'seller_id' => $seller->id,
            'type' => 'auction',
            'status' => 'ended',
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDay(),
            'finalization_deadline' => now()->addHour(),
        ]);

        // Execute job
        $job = new ProcessFinalizationTimeout();
        $job->handle($this->auctionService);

        // Assert auction status remains ended
        expect($auction->fresh()->status)->toBe('ended');
    });

    test('Property 54: Job Execution Logging - all job executions are logged', function () {
        Log::shouldReceive('info')
            ->with(\Mockery::pattern('/ProcessAuctionStarting: Found \d+ auctions to start/'))
            ->once();

        $job = new ProcessAuctionStarting();
        $job->handle($this->auctionService);
    });

    test('Property 55: Job Retry Logic - ProcessAuctionEnding retries on failure', function () {
        $job = new ProcessAuctionEnding();
        
        // Verify retry configuration
        expect($job->tries)->toBe(3);
        expect($job->backoff)->toBe([10, 30, 60]);
    });
});
