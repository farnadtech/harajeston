<?php

use App\Models\{User, Listing};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Property 51: Search Functionality
test('search finds listings by title', function () {
    $seller = User::factory()->create();
    
    $listing1 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'لپ تاپ ایسوس',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);
    
    $listing2 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'موبایل سامسونگ',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings?search=لپ تاپ');
    
    $response->assertSee('لپ تاپ ایسوس');
    $response->assertDontSee('موبایل سامسونگ');
});

test('search finds listings by description', function () {
    $seller = User::factory()->create();
    
    $listing1 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'محصول A',
        'description' => 'این یک لپ تاپ عالی است',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);
    
    $listing2 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'محصول B',
        'description' => 'این یک موبایل عالی است',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings?search=لپ تاپ');
    
    $response->assertSee('محصول A');
    $response->assertDontSee('محصول B');
});

test('search is case insensitive', function () {
    $seller = User::factory()->create();
    
    $listing = Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'LAPTOP ASUS',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings?search=laptop');
    
    $response->assertSee('LAPTOP ASUS');
});

// Property 52: Auction Filtering
test('filter by type shows only matching listings', function () {
    $seller = User::factory()->create();
    
    $auction = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'title' => 'Auction Item',
    ]);
    
    $directSale = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'status' => 'active',
        'title' => 'Direct Sale Item',
    ]);

    $response = $this->get('/listings?type=auction');
    
    $response->assertSee('Auction Item');
    $response->assertDontSee('Direct Sale Item');
});

test('filter by status shows only matching listings', function () {
    $seller = User::factory()->create();
    
    $active = Listing::factory()->create([
        'seller_id' => $seller->id,
        'status' => 'active',
        'type' => 'direct_sale',
        'title' => 'Active Item',
    ]);
    
    $ended = Listing::factory()->create([
        'seller_id' => $seller->id,
        'status' => 'ended',
        'type' => 'auction',
        'title' => 'Ended Item',
    ]);

    $response = $this->get('/listings?status=active');
    
    $response->assertSee('Active Item');
    $response->assertDontSee('Ended Item');
});

test('multiple filters work together', function () {
    $seller = User::factory()->create();
    
    $match = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
        'title' => 'Active Auction',
    ]);
    
    $noMatch1 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'status' => 'active',
        'title' => 'Active Direct Sale',
    ]);
    
    $noMatch2 = Listing::factory()->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'ended',
        'title' => 'Ended Auction',
    ]);

    $response = $this->get('/listings?type=auction&status=active');
    
    $response->assertSee('Active Auction');
    $response->assertDontSee('Active Direct Sale');
    $response->assertDontSee('Ended Auction');
});

// Property 53: Pagination Consistency
test('pagination preserves filters', function () {
    $seller = User::factory()->create();
    
    // Create 25 auction listings
    Listing::factory()->count(25)->create([
        'seller_id' => $seller->id,
        'type' => 'auction',
        'status' => 'active',
    ]);
    
    // Create 5 direct sale listings
    Listing::factory()->count(5)->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings?type=auction&page=2');
    
    // Should see page 2 of auctions (items 21-25)
    $response->assertStatus(200);
    
    // Verify pagination links contain the filter
    $response->assertSee('type=auction');
});

test('pagination shows correct number of items per page', function () {
    $seller = User::factory()->create();
    
    Listing::factory()->count(25)->create([
        'seller_id' => $seller->id,
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings');
    
    $response->assertStatus(200);
    
    // Should show 20 items on first page (default pagination)
    $listings = $response->viewData('listings');
    expect($listings)->toHaveCount(20);
});

test('search with no results shows empty message', function () {
    $seller = User::factory()->create();
    
    Listing::factory()->create([
        'seller_id' => $seller->id,
        'title' => 'Test Item',
        'type' => 'direct_sale',
        'status' => 'active',
    ]);

    $response = $this->get('/listings?search=nonexistent');
    
    $response->assertSee('هیچ آگهی فعالی یافت نشد');
});
