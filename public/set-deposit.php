<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::where('slug', 'tst-frnad-1')->first();

if ($listing) {
    $listing->required_deposit = 1000; // 1000 تومان سپرده
    $listing->save();
    
    echo "✓ Deposit set successfully!\n";
    echo "Listing ID: " . $listing->id . "\n";
    echo "Title: " . $listing->title . "\n";
    echo "Required Deposit: " . number_format($listing->required_deposit) . " تومان\n";
} else {
    echo "✗ Listing not found!\n";
}
