<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('Form Request Validation', function () {
    
    test('CreateListingRequest validates auction fields', function () {
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/listings', [
                'type' => 'auction',
                'title' => 'Test Auction',
                'description' => 'Test Description',
                // Missing base_price, start_time, end_time
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['base_price', 'start_time', 'end_time']);
    });
    
    test('CreateListingRequest validates direct_sale fields', function () {
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/listings', [
                'type' => 'direct_sale',
                'title' => 'Test Product',
                'description' => 'Test Description',
                // Missing price, stock
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price', 'stock']);
    });
    
    test('CreateListingRequest validates hybrid price greater than base_price', function () {
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        // Test with price less than base_price (should fail)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/listings', [
                'type' => 'hybrid',
                'title' => 'Test Hybrid',
                'description' => 'Test Description',
                'base_price' => 10000,
                'price' => 9000, // Less than base_price - should fail
                'stock' => 5,
                'start_time' => now()->addHour()->toDateTimeString(),
                'end_time' => now()->addDays(7)->toDateTimeString(),
            ]);
        
        // Should return validation error
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    });
    
    test('PlaceBidRequest validates minimum bid amount', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        
        $listing = Listing::factory()->create([
            'type' => 'auction',
            'seller_id' => $seller->id,
            'base_price' => 10000,
            'current_highest_bid' => 15000,
            'status' => 'active',
        ]);
        
        $token = $buyer->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/listings/{$listing->id}/bids", [
                'amount' => 12000, // Less than current_highest_bid
            ]);
        
        $response->assertStatus(400); // Service will throw exception
    });
    
    test('AddToCartRequest validates quantity', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $token = $buyer->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/add', [
                'listing_id' => 1,
                'quantity' => 0, // Invalid quantity
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    });
    
    test('CheckoutRequest validates shipping address', function () {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $token = $buyer->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/checkout', [
                // Missing shipping_address
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shipping_address']);
    });
    
    test('UploadImageRequest validates image format', function () {
        Storage::fake('public');
        
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/listings/images', [
                'images' => [$invalidFile],
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['images.0']);
    });
    
    test('UploadImageRequest validates maximum images', function () {
        Storage::fake('public');
        
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        $images = [];
        for ($i = 0; $i < 6; $i++) {
            $images[] = UploadedFile::fake()->image("image{$i}.jpg");
        }
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/listings/images', [
                'images' => $images,
            ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['images']);
    });
    
    test('CreateShippingMethodRequest requires admin role', function () {
        $seller = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $token = $seller->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/shipping-methods', [
                'name' => 'Test Shipping',
                'base_cost' => 5000,
            ]);
        
        $response->assertStatus(403); // Forbidden
    });
    
    test('UpdateStoreRequest validates authorization', function () {
        $seller1 = User::factory()->create(['role' => 'seller', 'username' => 'seller1']);
        $seller2 = User::factory()->create(['role' => 'seller', 'username' => 'seller2']);
        
        $store = Store::factory()->create(['user_id' => $seller1->id]);
        
        $token = $seller2->createToken('test')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/stores/{$store->id}", [
                'store_name' => 'Hacked Store',
            ]);
        
        $response->assertStatus(403); // Forbidden
    });
});
