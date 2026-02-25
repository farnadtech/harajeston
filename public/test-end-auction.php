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
echo "Current time: " . now() . "\n";
echo "Has ended: " . ($listing->ends_at < now() ? 'YES' : 'NO') . "\n\n";

if ($listing->status === 'active' && $listing->ends_at < now()) {
    echo "Manually ending auction...\n";
    
    try {
        $auctionService = app(AuctionService::class);
        $auctionService->endAuction($listing);
        
        echo "✓ Auction ended successfully!\n\n";
        
        // Reload listing
        $listing->refresh();
        
        echo "After ending:\n";
        echo "Status: {$listing->status}\n";
        echo "Winner ID: {$listing->current_winner_id}\n";
        echo "Finalization deadline: {$listing->finalization_deadline}\n\n";
        
        // Check participations
        echo "Checking participations:\n";
        $participations = $listing->participations()->with('user')->get();
        foreach ($participations as $p) {
            echo "User: {$p->user->name} | Status: {$p->deposit_status} | Amount: {$p->deposit_amount}\n";
        }
        
        echo "\nChecking wallets:\n";
        foreach ($participations as $p) {
            $wallet = $p->user->wallet;
            echo "User: {$p->user->name} | Balance: {$wallet->balance} | Frozen: {$wallet->frozen}\n";
        }
        
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "Auction is not ready to end.\n";
}
