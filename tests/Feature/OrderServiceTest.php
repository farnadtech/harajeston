<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\WalletService;
use App\Services\ListingService;
use App\Services\CartService;
use App\Exceptions\Cart\CartEmptyException;
use App\Exceptions\Order\InvalidOrderStatusException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->walletService = new WalletService();
    $this->listingService = new ListingService();
    $this->cartService = new CartService();
    $this->orderService = new OrderService(
        $this->walletService,
        $this->listingService,
        $this->cartService
    );
    
    $this->buyer = User::factory()->create(['role' => 'buyer']);
    $this->seller = User::factory()->create(['role' => 'seller']);
    
    // Create wallets
    \App\Models\Wallet::factory()->create([
        'user_id' => $this->buyer->id,
        'balance' => 100000,
        'frozen' => 0,
    ]);
    
    \App\Models\Wallet::factory()->create([
        'user_id' => $this->seller->id,
        'balance' => 0,
        'frozen' => 0,
    ]);
});

describe('OrderService - createOrderFromCart', function () {
    
    test('Property 63: Order Number Uniqueness - each order has unique number', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 1);
        
        $orders1 = $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        // Create another order
        $this->cartService->addToCart($this->buyer, $listing, 1);
        $orders2 = $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        expect($orders1->first()->order_number)->not->toBe($orders2->first()->order_number);
    });
    
    test('Property 64: Order Payment Atomicity - payment processed atomically', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        
        $buyerBalanceBefore = $this->buyer->wallet->balance;
        $sellerBalanceBefore = $this->seller->wallet->balance;
        
        $orders = $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        $this->buyer->wallet->refresh();
        $this->seller->wallet->refresh();
        
        $orderTotal = $orders->first()->total;
        
        expect((float)$this->buyer->wallet->balance)->toBe((float)$buyerBalanceBefore - $orderTotal);
        expect((float)$this->seller->wallet->balance)->toBe((float)$sellerBalanceBefore + $orderTotal);
    });
    
    test('creates order from cart successfully', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        
        $orders = $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        expect($orders)->toHaveCount(1);
        
        $order = $orders->first();
        expect($order->buyer_id)->toBe($this->buyer->id);
        expect($order->seller_id)->toBe($this->seller->id);
        expect($order->status)->toBe('pending');
        expect((float)$order->subtotal)->toBe(20000.0);
    });
    
    test('decrements stock after order creation', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 3);
        
        $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        $listing->refresh();
        expect($listing->stock)->toBe(7);
    });
    
    test('clears cart after order creation', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        
        $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        $cart = Cart::where('user_id', $this->buyer->id)->first();
        expect($cart->items)->toHaveCount(0);
    });
    
    test('throws exception for empty cart', function () {
        expect(fn() => $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]))->toThrow(CartEmptyException::class);
    });
    
    test('creates separate orders for different sellers', function () {
        $seller2 = User::factory()->create(['role' => 'seller']);
        \App\Models\Wallet::factory()->create([
            'user_id' => $seller2->id,
            'balance' => 0,
        ]);
        
        $listing1 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $listing2 = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $seller2->id,
            'price' => 5000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing1, 1);
        $this->cartService->addToCart($this->buyer, $listing2, 1);
        
        $orders = $this->orderService->createOrderFromCart($this->buyer, [
            'address' => 'Test Address',
        ]);
        
        expect($orders)->toHaveCount(2);
    });
});

describe('OrderService - updateOrderStatus', function () {
    
    test('updates order status successfully', function () {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => 'pending',
        ]);
        
        $updatedOrder = $this->orderService->updateOrderStatus($order, 'processing');
        
        expect($updatedOrder->status)->toBe('processing');
    });
    
    test('throws exception for invalid status', function () {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => 'pending',
        ]);
        
        expect(fn() => $this->orderService->updateOrderStatus($order, 'invalid_status'))
            ->toThrow(InvalidOrderStatusException::class);
    });
});

describe('OrderService - cancelOrder', function () {
    
    test('Property 65: Order Cancellation Refund - buyer refunded on cancellation', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 2);
        $orders = $this->orderService->createOrderFromCart($this->buyer, ['address' => 'Test']);
        
        $buyerBalanceBefore = $this->buyer->wallet->fresh()->balance;
        
        $this->orderService->cancelOrder($orders->first(), $this->buyer);
        
        $this->buyer->wallet->refresh();
        
        expect((float)$this->buyer->wallet->balance)->toBe((float)$buyerBalanceBefore + 20000.0);
    });
    
    test('cancels order and restores stock', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'seller_id' => $this->seller->id,
            'price' => 10000,
            'stock' => 10,
        ]);
        
        $this->cartService->addToCart($this->buyer, $listing, 3);
        $orders = $this->orderService->createOrderFromCart($this->buyer, ['address' => 'Test']);
        
        $this->orderService->cancelOrder($orders->first(), $this->buyer);
        
        $listing->refresh();
        expect($listing->stock)->toBe(10); // Restored
    });
    
    test('throws exception when cancelling after 1 hour', function () {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => 'pending',
            'created_at' => Carbon::now()->subHours(2),
        ]);
        
        expect(fn() => $this->orderService->cancelOrder($order, $this->buyer))
            ->toThrow(\InvalidArgumentException::class);
    });
    
    test('throws exception when order not pending', function () {
        $order = Order::factory()->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
            'status' => 'shipped',
        ]);
        
        expect(fn() => $this->orderService->cancelOrder($order, $this->buyer))
            ->toThrow(InvalidOrderStatusException::class);
    });
});

describe('OrderService - getOrdersByUser', function () {
    
    test('retrieves buyer orders', function () {
        Order::factory()->count(3)->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);
        
        $orders = $this->orderService->getOrdersByUser($this->buyer, 'buyer');
        
        expect($orders)->toHaveCount(3);
    });
    
    test('retrieves seller orders', function () {
        Order::factory()->count(2)->create([
            'buyer_id' => $this->buyer->id,
            'seller_id' => $this->seller->id,
        ]);
        
        $orders = $this->orderService->getOrdersByUser($this->seller, 'seller');
        
        expect($orders)->toHaveCount(2);
    });
});
