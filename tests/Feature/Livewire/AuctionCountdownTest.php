<?php

use App\Livewire\AuctionCountdown;
use App\Models\{User, Listing};
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('countdown component renders correctly', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'end_time' => Carbon::now()->addHours(2),
    ]);

    Livewire::test(AuctionCountdown::class, ['listing' => $listing])
        ->assertSee('زمان باقی‌مانده')
        ->assertSet('hasEnded', false);
});

test('countdown shows ended state for completed auction', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'ended',
        'end_time' => Carbon::now()->subHours(1),
    ]);

    Livewire::test(AuctionCountdown::class, ['listing' => $listing])
        ->assertSee('پایان یافته')
        ->assertSet('hasEnded', true);
});

test('countdown calculates remaining time correctly', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'end_time' => Carbon::now()->addDays(2)->addHours(3),
    ]);

    Livewire::test(AuctionCountdown::class, ['listing' => $listing])
        ->assertSet('hasEnded', false)
        ->assertSee('روز');
});

test('countdown shows ended when end time passed', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'end_time' => Carbon::now()->subMinutes(10),
    ]);

    Livewire::test(AuctionCountdown::class, ['listing' => $listing])
        ->assertSee('پایان یافته')
        ->assertSet('hasEnded', true);
});
