<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;

echo "Testing listing card data...\n\n";

// Get some active listings
$listings = Listing::whereIn('status', ['active', 'pending'])
    ->with('seller', 'images')
    ->withCount('bids')
    ->take(5)
    ->get();

echo "Found " . $listings->count() . " listings\n\n";

foreach ($listings as $listing) {
    echo "Listing: {$listing->title}\n";
    echo "  ID: {$listing->id}\n";
    echo "  Status: {$listing->status}\n";
    echo "  Starting Price: " . number_format($listing->starting_price) . " تومان\n";
    echo "  Current Price: " . number_format($listing->current_price ?? 0) . " تومان\n";
    echo "  Bids Count: {$listing->bids_count}\n";
    echo "  Views: {$listing->views}\n";
    echo "  Images: {$listing->images->count()}\n";
    echo "  Seller: {$listing->seller->name}\n";
    echo "\n";
}

echo "✓ All data loaded successfully!\n";
