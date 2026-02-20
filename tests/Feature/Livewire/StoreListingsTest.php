<?php

use App\Livewire\StoreListings;
use App\Models\{User, Store, Listing};
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store listings component renders correctly', function () {
    $seller = User::factory()->create();
    $store = Store::create([
        'user_id' => $seller->id,
        'store_name' => 'فروشگاه تست',
        'slug' => 'test-store',
    ]);

    Livewire::test(StoreListings::class, ['store' => $store])
        ->assertSee('همه');
});

test('store listings filters by type', function () {
    $seller = User::factory()->create();
    $store = Store::create([
        'user_id' => $seller->id,
        'store_name' => 'فروشگاه تست',
        'slug' => 'test-store',
    ]);

    Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
    ]);

    Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    Livewire::test(StoreListings::class, ['store' => $store])
        ->call('setFilter', 'auction')
        ->assertSet('filterType', 'auction');
});

test('store listings shows only active listings', function () {
    $seller = User::factory()->create();
    $store = Store::create([
        'user_id' => $seller->id,
        'store_name' => 'فروشگاه تست',
        'slug' => 'test-store',
    ]);

    Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'title' => 'Active Listing',
    ]);

    Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'ended',
        'title' => 'Ended Listing',
    ]);

    Livewire::test(StoreListings::class, ['store' => $store])
        ->assertSee('Active Listing')
        ->assertDontSee('Ended Listing');
});
