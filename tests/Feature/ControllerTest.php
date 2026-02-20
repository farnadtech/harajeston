<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Controllers', function () {
    test('controllers exist and are instantiable', function () {
        // Test that all controllers can be instantiated
        $controllers = [
            \App\Http\Controllers\ListingController::class,
            \App\Http\Controllers\BidController::class,
            \App\Http\Controllers\PaymentController::class,
            \App\Http\Controllers\WalletController::class,
            \App\Http\Controllers\StoreController::class,
            \App\Http\Controllers\CartController::class,
            \App\Http\Controllers\CheckoutController::class,
            \App\Http\Controllers\OrderController::class,
            \App\Http\Controllers\DashboardController::class,
            \App\Http\Controllers\Admin\ListingController::class,
            \App\Http\Controllers\Admin\ShippingMethodController::class,
            \App\Http\Controllers\Admin\OrderController::class,
        ];

        foreach ($controllers as $controller) {
            expect(class_exists($controller))->toBeTrue();
        }
    });

    test('ListingController uses ListingService', function () {
        $controller = app(\App\Http\Controllers\ListingController::class);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('listingService');
        $property->setAccessible(true);
        
        expect($property->getValue($controller))->toBeInstanceOf(\App\Services\ListingService::class);
    });

    test('BidController uses BidService', function () {
        $controller = app(\App\Http\Controllers\BidController::class);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('bidService');
        $property->setAccessible(true);
        
        expect($property->getValue($controller))->toBeInstanceOf(\App\Services\BidService::class);
    });

    test('WalletController uses WalletService', function () {
        $controller = app(\App\Http\Controllers\WalletController::class);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('walletService');
        $property->setAccessible(true);
        
        expect($property->getValue($controller))->toBeInstanceOf(\App\Services\WalletService::class);
    });

    test('CartController uses CartService', function () {
        $controller = app(\App\Http\Controllers\CartController::class);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('cartService');
        $property->setAccessible(true);
        
        expect($property->getValue($controller))->toBeInstanceOf(\App\Services\CartService::class);
    });

    test('OrderController uses OrderService', function () {
        $controller = app(\App\Http\Controllers\OrderController::class);
        
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('orderService');
        $property->setAccessible(true);
        
        expect($property->getValue($controller))->toBeInstanceOf(\App\Services\OrderService::class);
    });
});
