<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\AuctionParticipation;
use App\Services\DepositService;
use App\Services\WalletService;
use App\Exceptions\Auction\AlreadyParticipatingException;
use App\Exceptions\Wallet\InsufficientBalanceException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->walletService = new WalletService();
    $this->depositService = new DepositService($this->walletService);
});

describe('DepositService - participateInAuction', function () {
    
    test('Property 11: Participation Requires Sufficient Balance', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        // Insufficient balance
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 5000, // Less than required deposit
            'frozen' => 0,
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        expect(fn() => $this->depositService->participateInAuction($user, $listing))
            ->toThrow(InsufficientBalanceException::class);
    });
    
    test('Property 12: Participation Creates Record', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 50000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        $participation = $this->depositService->participateInAuction($user, $listing);
        
        expect($participation)->toBeInstanceOf(AuctionParticipation::class);
        expect($participation->user_id)->toBe($user->id);
        expect($participation->listing_id)->toBe($listing->id);
        expect($participation->deposit_status)->toBe('paid');
        expect((float)$participation->deposit_amount)->toBe(10000.0);
    });
    
    test('Property 13: Participation Idempotence - cannot participate twice', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 50000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        // First participation - should succeed
        $this->depositService->participateInAuction($user, $listing);
        
        // Second participation - should fail
        expect(fn() => $this->depositService->participateInAuction($user, $listing))
            ->toThrow(AlreadyParticipatingException::class);
    });
    
    test('participation freezes deposit in wallet', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 50000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        $this->depositService->participateInAuction($user, $listing);
        
        $wallet->refresh();
        
        expect((float)$wallet->balance)->toBe(40000.0);
        expect((float)$wallet->frozen)->toBe(10000.0);
    });
});

describe('DepositService - hasParticipated', function () {
    
    test('returns true when user has participated', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@test.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 50000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'type' => 'auction',
            'base_price' => 100000,
            'required_deposit' => 10000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        $this->depositService->participateInAuction($user, $listing);
        
        expect($this->depositService->hasParticipated($user, $listing))->toBeTrue();
    });
    
    test('returns false when user has not participated', function () {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer',
            'email' => 'buyer@test.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
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
            'start_time' => now()->addHour(),
            'end_time' => now()->addDays(7),
            'status' => 'pending',
        ]);
        
        expect($this->depositService->hasParticipated($user, $listing))->toBeFalse();
    });
});
