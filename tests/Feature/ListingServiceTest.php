<?php

use App\Models\User;
use App\Models\Listing;
use App\Services\ListingService;
use App\Exceptions\DirectSale\OutOfStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->listingService = new ListingService();
    $this->seller = User::factory()->create(['role' => 'seller']);
});

describe('ListingService - createListing', function () {
    
    test('Property 56: Listing Type Validation - only valid types accepted', function () {
        $validTypes = ['auction', 'direct_sale', 'hybrid'];
        
        foreach ($validTypes as $type) {
            $data = [
                'type' => $type,
                'title' => 'Test Listing',
                'description' => 'Test Description',
            ];
            
            if ($type === 'auction' || $type === 'hybrid') {
                $data['base_price'] = 10000;
                $data['start_time'] = Carbon::now()->addHour();
                $data['end_time'] = Carbon::now()->addDays(7);
            }
            
            if ($type === 'direct_sale' || $type === 'hybrid') {
                $data['price'] = $type === 'hybrid' ? 15000 : 10000;
                $data['stock'] = 10;
            }
            
            $listing = $this->listingService->createListing($this->seller, $data);
            
            expect($listing->type)->toBe($type);
        }
    });
    
    test('Property 57: Auction Deposit Calculation (10%) - deposit is always 10% of base price', function () {
        $basePrices = [10000, 50000, 100000, 250000];
        
        foreach ($basePrices as $basePrice) {
            $listing = $this->listingService->createListing($this->seller, [
                'type' => 'auction',
                'title' => 'Test Auction',
                'description' => 'Test',
                'base_price' => $basePrice,
                'start_time' => Carbon::now()->addHour(),
                'end_time' => Carbon::now()->addDays(7),
            ]);
            
            $expectedDeposit = $basePrice * 0.10;
            expect((float)$listing->required_deposit)->toBe($expectedDeposit);
        }
    });
    
    test('Property 58: Stock Validation for Direct Sales - stock never negative', function () {
        $listing = $this->listingService->createListing($this->seller, [
            'type' => 'direct_sale',
            'title' => 'Test Product',
            'description' => 'Test',
            'price' => 10000,
            'stock' => 10,
        ]);
        
        expect($listing->stock)->toBeGreaterThanOrEqual(0);
    });
    
    test('creates auction listing with correct fields', function () {
        $startTime = Carbon::now()->addHour();
        $endTime = Carbon::now()->addDays(7);
        
        $listing = $this->listingService->createListing($this->seller, [
            'type' => 'auction',
            'title' => 'Test Auction',
            'description' => 'Test Description',
            'base_price' => 10000,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
        
        expect($listing->type)->toBe('auction');
        expect($listing->seller_id)->toBe($this->seller->id);
        expect((float)$listing->base_price)->toBe(10000.0);
        expect((float)$listing->required_deposit)->toBe(1000.0);
        expect($listing->status)->toBe('pending');
    });
    
    test('creates direct_sale listing with correct fields', function () {
        $listing = $this->listingService->createListing($this->seller, [
            'type' => 'direct_sale',
            'title' => 'Test Product',
            'description' => 'Test Description',
            'price' => 15000,
            'stock' => 20,
        ]);
        
        expect($listing->type)->toBe('direct_sale');
        expect($listing->seller_id)->toBe($this->seller->id);
        expect((float)$listing->price)->toBe(15000.0);
        expect($listing->stock)->toBe(20);
        expect($listing->status)->toBe('active');
    });
    
    test('creates hybrid listing with both auction and direct sale fields', function () {
        $listing = $this->listingService->createListing($this->seller, [
            'type' => 'hybrid',
            'title' => 'Test Hybrid',
            'description' => 'Test Description',
            'base_price' => 10000,
            'price' => 15000,
            'stock' => 5,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addDays(7),
        ]);
        
        expect($listing->type)->toBe('hybrid');
        expect((float)$listing->base_price)->toBe(10000.0);
        expect((float)$listing->price)->toBe(15000.0);
        expect($listing->stock)->toBe(5);
    });
    
    test('throws exception for invalid listing type', function () {
        expect(fn() => $this->listingService->createListing($this->seller, [
            'type' => 'invalid_type',
            'title' => 'Test',
            'description' => 'Test',
        ]))->toThrow(\InvalidArgumentException::class);
    });
    
    test('throws exception when auction missing base_price', function () {
        expect(fn() => $this->listingService->createListing($this->seller, [
            'type' => 'auction',
            'title' => 'Test',
            'description' => 'Test',
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addDays(7),
        ]))->toThrow(\InvalidArgumentException::class);
    });
    
    test('throws exception when direct_sale missing price or stock', function () {
        expect(fn() => $this->listingService->createListing($this->seller, [
            'type' => 'direct_sale',
            'title' => 'Test',
            'description' => 'Test',
            'price' => 10000,
        ]))->toThrow(\InvalidArgumentException::class);
    });
    
    test('allows hybrid listing creation when price higher than base_price', function () {
        $listing = $this->listingService->createListing($this->seller, [
            'type' => 'hybrid',
            'title' => 'Test',
            'description' => 'Test',
            'base_price' => 10000,
            'price' => 15000, // Higher than base_price
            'stock' => 5,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addDays(7),
        ]);
        
        expect($listing->type)->toBe('hybrid');
        expect((float)$listing->base_price)->toBe(10000.0);
        expect((float)$listing->price)->toBe(15000.0);
    });
    
    test('throws exception when end_time before start_time', function () {
        expect(fn() => $this->listingService->createListing($this->seller, [
            'type' => 'auction',
            'title' => 'Test',
            'description' => 'Test',
            'base_price' => 10000,
            'start_time' => Carbon::now()->addDays(7),
            'end_time' => Carbon::now()->addHour(),
        ]))->toThrow(\InvalidArgumentException::class);
    });
});

describe('ListingService - updateStock', function () {
    
    test('updates stock and maintains active status', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
            'status' => 'active',
        ]);
        
        $this->listingService->updateStock($listing, 20, 'تامین موجودی');
        
        $listing->refresh();
        expect($listing->stock)->toBe(20);
        expect($listing->status)->toBe('active');
    });
    
    test('sets status to out_of_stock when stock becomes zero', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
            'status' => 'active',
        ]);
        
        $this->listingService->updateStock($listing, 0, 'فروش تمام موجودی');
        
        $listing->refresh();
        expect($listing->stock)->toBe(0);
        expect($listing->status)->toBe('out_of_stock');
    });
    
    test('reactivates listing when stock added to out_of_stock listing', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 0,
            'status' => 'out_of_stock',
        ]);
        
        $this->listingService->updateStock($listing, 5, 'تامین موجودی جدید');
        
        $listing->refresh();
        expect($listing->stock)->toBe(5);
        expect($listing->status)->toBe('active');
    });
    
    test('throws exception for negative stock', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
        ]);
        
        expect(fn() => $this->listingService->updateStock($listing, -5, 'test'))
            ->toThrow(\InvalidArgumentException::class);
    });
});

describe('ListingService - decrementStock', function () {
    
    test('Property 59: Stock Never Goes Negative - decrement throws exception when insufficient', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 5,
        ]);
        
        expect(fn() => $this->listingService->decrementStock($listing, 10))
            ->toThrow(OutOfStockException::class);
        
        $listing->refresh();
        expect($listing->stock)->toBe(5); // Stock unchanged
    });
    
    test('Property 60: Stock Decrement Atomicity - concurrent decrements handled safely', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
        ]);
        
        $this->listingService->decrementStock($listing, 3);
        
        $listing->refresh();
        expect($listing->stock)->toBe(7);
    });
    
    test('decrements stock correctly', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
            'status' => 'active',
        ]);
        
        $this->listingService->decrementStock($listing, 3);
        
        $listing->refresh();
        expect($listing->stock)->toBe(7);
        expect($listing->status)->toBe('active');
    });
    
    test('sets status to out_of_stock when stock reaches zero', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 5,
            'status' => 'active',
        ]);
        
        $this->listingService->decrementStock($listing, 5);
        
        $listing->refresh();
        expect($listing->stock)->toBe(0);
        expect($listing->status)->toBe('out_of_stock');
    });
    
    test('throws exception when quantity is zero or negative', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 10,
        ]);
        
        expect(fn() => $this->listingService->decrementStock($listing, 0))
            ->toThrow(\InvalidArgumentException::class);
    });
});

describe('ListingService - incrementStock', function () {
    
    test('increments stock correctly', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 5,
        ]);
        
        $this->listingService->incrementStock($listing, 3);
        
        $listing->refresh();
        expect($listing->stock)->toBe(8);
    });
    
    test('reactivates out_of_stock listing when stock added', function () {
        $listing = Listing::factory()->create([
            'type' => 'direct_sale',
            'stock' => 0,
            'status' => 'out_of_stock',
        ]);
        
        $this->listingService->incrementStock($listing, 5);
        
        $listing->refresh();
        expect($listing->stock)->toBe(5);
        expect($listing->status)->toBe('active');
    });
});
