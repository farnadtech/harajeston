<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::where('slug', 'tst-frnad-1')->first();

if ($listing) {
    echo "Listing ID: " . $listing->id . "\n";
    echo "Title: " . $listing->title . "\n";
    echo "Required Deposit: " . $listing->required_deposit . "\n";
    echo "Starting Price: " . $listing->starting_price . "\n";
} else {
    echo "Listing not found!\n";
}
