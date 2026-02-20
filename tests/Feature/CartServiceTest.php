<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CartService;
use App\Exceptions\DirectSale\OutOfStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->cartService = new CartService();
    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->seller = User::factory()->create(['role' => 'seller']);
});

describe('CartService - addToCart', function () {
    
    test('Property 61: Cart Quantity Validation - cannot add more than available stock', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 5,
            'status' => 'active',
        ]);
        
        expect(fn() => $this->cartService->addToCart($this->buyer, $listing, 10))
            ->toThrow(OutOfStockException::class);
    });
    
    test('adds item to cart successfully', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
            'status' => 'active',
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 2);
        
        expect($cartItem)->toBeInstanceOf(CartItem::class);
        expect($cartItem->quantity)->toBe(2);
        expect((float)$cartItem->price_snapshot)->toBe(10000.0);
    });
    
    test('updates existing cart item quantity', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
            'status' => 'active',
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 3);
        
        expect($cartItem->quantity)->toBe(5);
    });
    
    test('throws exception for auction listings', function () {
        $listing = Listing::factory()->create([
            'type' => 'auction',
            'seller_id' => $this->seller->id,
            'base_price' => 10000,
            'status' => 'pending',
        ]);
        
        expect(fn() => $this->cartService->addToCart($this->buyer, $listing, 1))
            ->toThrow(\InvalidArgumentException::class);
    });
    
    test('allows hybrid listings to be added', function () {
        $listing = Listing::factory()->create([
            'type' => 'hybrid',
            'seller_id' => $this->seller->id,
            'base_price' => 10000,
            'price' => 15000,
            'stock' => 5,
            'status' => 'active',
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 1);
        
        expect($cartItem)->toBeInstanceOf(CartItem::class);
    });
});

describe('CartService - updateCartItem', function () {
    
    test('updates cart item quantity', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 2);
        $updatedItem = $this->cartService->updateCartItem($cartItem, 5);
        
        expect($updatedItem->quantity)->toBe(5);
    });
    
    test('throws exception when new quantity exceeds stock', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 5,
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 2);
        
        expect(fn() => $this->cartService->updateCartItem($cartItem, 10))
            ->toThrow(OutOfStockException::class);
    });
    
    test('throws exception for zero or negative quantity', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 2);
        
        expect(fn() => $this->cartService->updateCartItem($cartItem, 0))
            ->toThrow(\InvalidArgumentException::class);
    });
});

describe('CartService - removeFromCart', function () {
    
    test('removes item from cart', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $cartItem = $this->cartService->addToCart($this->buyer, $listing, 2);
        $this->cartService->removeFromCart($cartItem);
        
        expect(CartItem::find($cartItem->id))->toBeNull();
    });
});

describe('CartService - getCartWithTotals', function () {
    
    test('Property 62: Cart Total Calculation - totals calculated correctly', function () {
        $listing1 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $listing2 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 5000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing1, 2); // 20000
        $this->cartService->addToCart($this->buyer, $listing2, 3); // 15000
        
        $cartData = $this->cartService->getCartWithTotals($this->buyer);
        
        expect($cartData['subtotal'])->toBe(35000.0);
        expect($cartData['total'])->toBe(35000.0);
    });
    
    test('returns empty cart for user with no cart', function () {
        $cartData = $this->cartService->getCartWithTotals($this->buyer);
        
        expect($cartData['items'])->toBeArray();
        expect($cartData['items'])->toBeEmpty();
        expect($cartData['subtotal'])->toBe(0);
        expect($cartData['total'])->toBe(0);
    });
    
    test('includes cart items in response', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        
        $cartData = $this->cartService->getCartWithTotals($this->buyer);
        
        expect($cartData['items'])->toHaveCount(1);
    });
});

describe('CartService - clearCart', function () {
    
    test('clears all items from cart', function () {
        $listing1 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $listing2 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 5000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing1, 2);
        $this->cartService->addToCart($this->buyer, $listing2, 3);
        
        $cart = Cart::where('user_id', $this->buyer->id)->first();
        $this->cartService->clearCart($cart);
        
        expect($cart->items()->count())->toBe(0);
    });
});
