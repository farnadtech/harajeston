<?php

use App\Livewire\AuctionBidding;
use App\Models\{User, Listing, AuctionParticipation, Wallet};
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('auction bidding component renders correctly', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000000,
    ]);

    Livewire::test(AuctionBidding::class, ['listing' => $listing])
        ->assertSee('پیشنهاد قیمت')
        ->assertSee('بالاترین پیشنهاد فعلی');
});

test('user can place bid successfully', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    
    Wallet::create(['user_id' => $buyer->id, 'balance' => 5000000, 'frozen' => 100000]);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000000,
        'required_deposit' => 100000,
    ]);

    AuctionParticipation::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'deposit_status' => 'paid',
        'deposit_amount' => 100000,
    ]);

    Livewire::actingAs($buyer)
        ->test(AuctionBidding::class, ['listing' => $listing])
        ->set('bidAmount', 1500000)
        ->call('placeBid')
        ->assertSee('پیشنهاد شما با موفقیت ثبت شد')
        ->assertDispatched('bid-placed');
});

test('component refreshes when bid placed event is dispatched', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000000,
        'current_highest_bid' => 1200000,
    ]);

    Livewire::test(AuctionBidding::class, ['listing' => $listing])
        ->dispatch('bid-placed', listingId: $listing->id)
        ->assertSet('currentHighestBid', 1200000);
});

test('component shows error for invalid bid', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    
    Wallet::create(['user_id' => $buyer->id, 'balance' => 5000000, 'frozen' => 100000]);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'base_price' => 1000000,
        'current_highest_bid' => 1500000,
    ]);

    AuctionParticipation::create([
        'listing_id' => $listing->id,
        'user_id' => $buyer->id,
        'deposit_status' => 'paid',
        'deposit_amount' => 100000,
    ]);

    Livewire::actingAs($buyer)
        ->test(AuctionBidding::class, ['listing' => $listing])
        ->set('bidAmount', 1000000)
        ->call('placeBid')
        ->assertSet('errorMessage', fn($value) => !empty($value));
});
