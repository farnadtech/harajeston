<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Services\AuctionService;

$slug = 'tst-frnad-1';

$listing = Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found\n");
}

echo "Listing: {$listing->title}\n";
echo "Status: {$listing->status}\n";
echo "Ends at: {$listing->ends_at}\n";
echo "Required deposit: {$listing->required_deposit}\n";
echo "\n";

// Force end the auction
$listing->ends_at = now()->subMinute();
$listing->save();

echo "Forced ends_at to past\n";
echo "Running endAuction...\n\n";

$auctionService = app(AuctionService::class);

try {
    $auctionService->endAuction($listing);
    echo "✓ Auction ended successfully\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Reload listing
$listing = Listing::where('slug', $slug)->first();

echo "After ending:\n";
echo "Status: {$listing->status}\n";
echo "Winner ID: {$listing->current_winner_id}\n";
echo "Finalization deadline: {$listing->finalization_deadline}\n";
echo "\n";

// Check deposits
echo "Checking deposits:\n";
$participations = $listing->participations()->with('user')->get();
foreach ($participations as $p) {
    $wallet = $p->user->wallet;
    echo "- User {$p->user->name}: Balance={$wallet->balance}, Frozen={$wallet->frozen}\n";
}
