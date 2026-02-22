<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slug = 'kmysyon-2-699aa862423f1';
$listing = \App\Models\Listing::where('slug', $slug)->first();

if ($listing) {
    echo "Listing found: {$listing->title}\n";
    echo "Status: {$listing->status}\n";
    echo "Starts at: {$listing->starts_at}\n";
    echo "Ends at: {$listing->ends_at}\n";
    echo "Show before start: " . ($listing->show_before_start ? 'true' : 'false') . "\n";
    echo "Is future: " . ($listing->starts_at && $listing->starts_at->isFuture() ? 'yes' : 'no') . "\n";
} else {
    echo "Listing not found\n";
}

echo "\n\nAll pending listings:\n";
$pending = \App\Models\Listing::where('status', 'pending')->get();
foreach ($pending as $p) {
    echo "- {$p->slug} (starts: {$p->starts_at}, show_before_start: " . ($p->show_before_start ? 'true' : 'false') . ")\n";
}
