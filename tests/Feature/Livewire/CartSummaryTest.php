<?php

use App\Livewire\CartSummary;
use App\Models\{User, Listing, Cart, CartItem, Wallet};
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('cart summary component renders correctly', function () {
    $user = User::factory()->create();
    Wallet::create(['user_id' => $user->id, 'balance' => 1000000, 'frozen' => 0]);

    Livewire::actingAs($user)
        ->test(CartSummary::class)
        ->assertSet('itemCount', 0);
});

test('cart summary shows correct item count', function () {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    
    Wallet::create(['user_id' => $buyer->id, 'balance' => 1000000, 'frozen' => 0]);
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'price' => 500000,
        'stock' => 10,
    ]);

    $cart = Cart::create(['user_id' => $buyer->id]);
    CartItem::create([
        'cart_id' => $cart->id,
        'listing_id' => $listing->id,
        'quantity' => 3,
        'price_snapshot' => $listing->price,
    ]);

    Livewire::actingAs($buyer)
        ->test(CartSummary::class)
        ->assertSet('itemCount', 3);
});

test('cart summary refreshes on cart updated event', function () {
    $user = User::factory()->create();
    Wallet::create(['user_id' => $user->id, 'balance' => 1000000, 'frozen' => 0]);

    Livewire::actingAs($user)
        ->test(CartSummary::class)
        ->dispatch('cart-updated')
        ->assertSet('itemCount', 0);
});
