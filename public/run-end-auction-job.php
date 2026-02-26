<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Jobs\ProcessAuctionEnding;
use App\Services\AuctionService;

echo "=== Running ProcessAuctionEnding Job ===\n\n";

$auctionService = app(AuctionService::class);
$job = new ProcessAuctionEnding();

try {
    $job->handle($auctionService);
    echo "\n✓ Job executed successfully!\n";
} catch (\Exception $e) {
    echo "\n✗ Job failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Checking Listing 23 ===\n";
$listing = \App\Models\Listing::find(23);
echo "Status: {$listing->status}\n";
echo "Winner User ID: {$listing->winner_user_id}\n";
echo "Ends At: {$listing->ends_at}\n";
echo "Is Past: " . ($listing->ends_at->isPast() ? 'YES' : 'NO') . "\n";
