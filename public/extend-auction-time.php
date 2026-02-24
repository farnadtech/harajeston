<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Extend all active auctions by 2 days
$updated = \App\Models\Listing::where('status', 'active')
    ->update([
        'ends_at' => \Carbon\Carbon::now()->addDays(2)
    ]);

echo "<h2>Extended Auction Times</h2>";
echo "<p>Updated {$updated} active listings</p>";
echo "<p>New end time: " . \Carbon\Carbon::now()->addDays(2)->format('Y-m-d H:i:s') . "</p>";

// Show updated listings
$listings = \App\Models\Listing::where('status', 'active')->get();
echo "<h3>Active Listings:</h3>";
foreach ($listings as $listing) {
    echo "<p>{$listing->title} - Ends at: {$listing->ends_at}</p>";
}
