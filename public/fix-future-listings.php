<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Finding listings with status=active but starts_at in future...\n\n";

$listings = \App\Models\Listing::where('status', 'active')
    ->where('starts_at', '>', now())
    ->get();

echo "Found {$listings->count()} listings\n\n";

foreach ($listings as $listing) {
    echo "Fixing: {$listing->title} (slug: {$listing->slug})\n";
    echo "  Old status: {$listing->status}\n";
    echo "  Starts at: {$listing->starts_at}\n";
    
    $listing->update(['status' => 'pending']);
    
    echo "  New status: pending\n\n";
}

echo "Done!\n";
