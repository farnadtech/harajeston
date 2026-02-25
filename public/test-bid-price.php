<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a listing
$listing = \App\Models\Listing::where('status', 'active')->first();

if (!$listing) {
    echo "No active listing found\n";
    exit;
}

echo "Listing ID: {$listing->id}\n";
echo "Title: {$listing->title}\n";
echo "Starting Price: " . number_format($listing->starting_price) . "\n";
echo "Current Highest Bid (from column): " . number_format($listing->current_highest_bid ?? 0) . "\n";

// Get highest bid from bids table
$highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
echo "Highest Bid (from bids table): " . ($highestBid ? number_format($highestBid->amount) : 'No bids') . "\n";

// Get all bids
$bids = $listing->bids()->orderBy('amount', 'desc')->get();
echo "\nAll Bids:\n";
foreach ($bids as $bid) {
    echo "  - User {$bid->user_id}: " . number_format($bid->amount) . " (created: {$bid->created_at})\n";
}

// What Livewire component would show
$currentHighestBid = $highestBid ? $highestBid->amount : $listing->starting_price;
echo "\nLivewire would show: " . number_format($currentHighestBid) . "\n";
