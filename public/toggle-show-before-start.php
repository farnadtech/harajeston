<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slug = 'kmysyon-2-699aa862423f1';
$listing = \App\Models\Listing::where('slug', $slug)->first();

if ($listing) {
    echo "Listing: {$listing->title}\n";
    echo "Current show_before_start: " . ($listing->show_before_start ? 'true' : 'false') . "\n\n";
    
    // Toggle the value
    $newValue = !$listing->show_before_start;
    $listing->update(['show_before_start' => $newValue]);
    
    echo "Updated show_before_start to: " . ($newValue ? 'true' : 'false') . "\n";
} else {
    echo "Listing not found\n";
}
