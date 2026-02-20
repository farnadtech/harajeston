<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Listing;
use App\Services\StoreService;
use App\Exceptions\Image\ImageSizeTooLargeException;
use App\Exceptions\Image\InvalidImageFormatException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->storeService = new StoreService();
    Storage::fake('public');
});

describe('StoreService - createStore', function () {
    
    test('creates store for new seller with unique slug', function () {
        $seller = User::factory()->create([
            'role' => 'seller',
            'username' => 'testshop',
        ]);
        
        $store = $this->storeService->createStore($seller, 'testshop');
        
        expect($store)->toBeInstanceOf(Store::class);
        expect($store->user_id)->toBe($seller->id);
        expect($store->slug)->toBe('testshop');
        expect($store->store_name)->toBe($seller->name);
        expect($store->is_active)->toBeTrue();
    });
    
    test('generates unique slug when username already exists', function () {
        $seller1 = User::factory()->create(['role' => 'seller', 'username' => 'shop1']);
        $seller2 = User::factory()->create(['role' => 'seller', 'username' => 'shop2']);
        
        $store1 = $this->storeService->createStore($seller1, 'shop');
        $store2 = $this->storeService->createStore($seller2, 'shop');
        
        expect($store1->slug)->toBe('shop');
        expect($store2->slug)->toBe('shop-1');
    });
    
    test('store is active by default', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        
        $store = $this->storeService->createStore($seller, 'mystore');
        
        expect($store->is_active)->toBeTrue();
    });
});

describe('StoreService - updateStoreProfile', function () {
    
    test('updates store name and description', function () {
        $store = Store::factory()->create([
            'store_name' => 'Old Name',
            'description' => 'Old Description',
        ]);
        
        $updatedStore = $this->storeService->updateStoreProfile($store, [
            'store_name' => 'New Name',
            'description' => 'New Description',
        ]);
        
        expect($updatedStore->store_name)->toBe('New Name');
        expect($updatedStore->description)->toBe('New Description');
    });
    
    test('uploads and stores banner image', function () {
        $store = Store::factory()->create();
        
        $banner = UploadedFile::fake()->image('banner.jpg', 1920, 400)->size(1024);
        
        $updatedStore = $this->storeService->updateStoreProfile($store, [
            'banner' => $banner,
        ]);
        
        expect($updatedStore->banner_image)->not->toBeNull();
        Storage::disk('public')->assertExists($updatedStore->banner_image);
    });
    
    test('uploads and stores logo image', function () {
        $store = Store::factory()->create();
        
        $logo = UploadedFile::fake()->image('logo.jpg', 300, 300)->size(512);
        
        $updatedStore = $this->storeService->updateStoreProfile($store, [
            'logo' => $logo,
        ]);
        
        expect($updatedStore->logo_image)->not->toBeNull();
        Storage::disk('public')->assertExists($updatedStore->logo_image);
    });
    
    test('deletes old banner when uploading new one', function () {
        $store = Store::factory()->create([
            'banner_image' => 'stores/banners/old-banner.jpg',
        ]);
        
        // Create the old file
        Storage::disk('public')->put('stores/banners/old-banner.jpg', 'old content');
        
        $newBanner = UploadedFile::fake()->image('new-banner.jpg', 1920, 400)->size(1024);
        
        $updatedStore = $this->storeService->updateStoreProfile($store, [
            'banner' => $newBanner,
        ]);
        
        Storage::disk('public')->assertMissing('stores/banners/old-banner.jpg');
        Storage::disk('public')->assertExists($updatedStore->banner_image);
    });
    
    test('throws exception when banner size exceeds limit', function () {
        $store = Store::factory()->create();
        
        // Create a real file that exceeds 2MB
        // Note: UploadedFile::fake()->size() doesn't actually create files of that size
        // So we'll test the validation logic directly
        $this->expectException(ImageSizeTooLargeException::class);
        
        // Create a mock file with size > 2MB (2048 KB = 2097152 bytes)
        $largeBanner = UploadedFile::fake()->create('large-banner.jpg', 2500); // 2.5 MB
        
        $this->storeService->updateStoreProfile($store, [
            'banner' => $largeBanner,
        ]);
    });
    
    test('throws exception when logo size exceeds limit', function () {
        $store = Store::factory()->create();
        
        // Create a real file that exceeds 1MB
        $this->expectException(ImageSizeTooLargeException::class);
        
        // Create a mock file with size > 1MB (1024 KB = 1048576 bytes)
        $largeLogo = UploadedFile::fake()->create('large-logo.jpg', 1500); // 1.5 MB
        
        $this->storeService->updateStoreProfile($store, [
            'logo' => $largeLogo,
        ]);
    });
    
    test('throws exception for invalid image format', function () {
        $store = Store::factory()->create();
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);
        
        expect(fn() => $this->storeService->updateStoreProfile($store, [
            'banner' => $invalidFile,
        ]))->toThrow(InvalidImageFormatException::class);
    });
});

describe('StoreService - getStoreBySlug', function () {
    
    test('retrieves store by slug with active listings', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::factory()->create([
            'user_id' => $seller->id,
            'slug' => 'test-store',
        ]);
        
        $activeListing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);
        
        $endedListing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'status' => 'ended',
        ]);
        
        $retrievedStore = $this->storeService->getStoreBySlug('test-store');
        
        expect($retrievedStore)->not->toBeNull();
        expect($retrievedStore->id)->toBe($store->id);
        expect($retrievedStore->listings)->toHaveCount(1);
        expect($retrievedStore->listings->first()->id)->toBe($activeListing->id);
    });
    
    test('returns null for inactive store', function () {
        $store = Store::factory()->inactive()->create([
            'slug' => 'inactive-store',
        ]);
        
        $retrievedStore = $this->storeService->getStoreBySlug('inactive-store');
        
        expect($retrievedStore)->toBeNull();
    });
    
    test('returns null for non-existent slug', function () {
        $retrievedStore = $this->storeService->getStoreBySlug('non-existent');
        
        expect($retrievedStore)->toBeNull();
    });
    
    test('includes user relationship', function () {
        $seller = User::factory()->create(['role' => 'seller']);
        $store = Store::factory()->create([
            'user_id' => $seller->id,
            'slug' => 'test-store',
        ]);
        
        $retrievedStore = $this->storeService->getStoreBySlug('test-store');
        
        expect($retrievedStore->user)->not->toBeNull();
        expect($retrievedStore->user->id)->toBe($seller->id);
    });
});
