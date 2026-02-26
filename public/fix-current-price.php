<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Bid;

echo "Updating current_price for all listings...\n\n";

$listings = Listing::all();

echo "Found " . $listings->count() . " listings\n\n";

$updated = 0;
$noChange = 0;

foreach ($listings as $listing) {
    echo "Listing ID: {$listing->id} - {$listing->title}\n";
    echo "  Status: {$listing->status}\n";
    echo "  Starting Price: " . number_format($listing->starting_price) . "\n";
    echo "  Current Price (old): " . number_format($listing->current_price ?? 0) . "\n";
    
    // Get highest bid
    $highestBid = Bid::where('listing_id', $listing->id)
        ->orderBy('amount', 'desc')
        ->first();
    
    $newPrice = $highestBid ? $highestBid->amount : $listing->starting_price;
    
    echo "  Highest Bid: " . ($highestBid ? number_format($highestBid->amount) : 'None') . "\n";
    echo "  New Price: " . number_format($newPrice) . "\n";
    
    if ($listing->current_price != $newPrice) {
        $listing->current_price = $newPrice;
        
        // Also update current_winner_id if there's a highest bid
        if ($highestBid) {
            $listing->current_winner_id = $highestBid->user_id;
        }
        
        $listing->save();
        echo "  ✓ Updated!\n\n";
        $updated++;
    } else {
        echo "  - No change needed\n\n";
        $noChange++;
    }
}

echo "Summary:\n";
echo "  Updated: {$updated} listings\n";
echo "  No change: {$noChange} listings\n";
echo "\nDone!\n";
