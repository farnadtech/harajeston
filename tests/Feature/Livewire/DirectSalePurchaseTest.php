<?php

use App\Livewire\DirectSalePurchase;
use App\Models\{User, Listing, Wallet};
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('direct sale purchase component renders correctly', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'price' => 500000,
        'stock' => 10,
    ]);

    Livewire::test(DirectSalePurchase::class, ['listing' => $listing])
        ->assertSee('خرید مستقیم')
        ->assertSee('قیمت');
});

test('user can add item to cart', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    
    Wallet::create(['user_id' => $buyer->id, 'balance' => 1000000, 'frozen' => 0]);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'price' => 500000,
        'stock' => 10,
    ]);

    Livewire::actingAs($buyer)
        ->test(DirectSalePurchase::class, ['listing' => $listing])
        ->set('quantity', 2)
        ->call('addToCart')
        ->assertSee('محصول به سبد خرید اضافه شد')
        ->assertDispatched('cart-updated');
});

test('component shows out of stock message', function () {
    $seller = User::factory()->create();
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'price' => 500000,
        'stock' => 0,
    ]);

    Livewire::test(DirectSalePurchase::class, ['listing' => $listing])
        ->assertSee('ناموجود');
});
