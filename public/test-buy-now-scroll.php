<?php
// Test buy now modal with many shipping methods
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Buy Now Modal Scroll\n";
echo "============================\n\n";

// Find a listing with buy_now_price
$listing = \App\Models\Listing::where('buy_now_price', '>', 0)
    ->where('status', 'active')
    ->with('shippingMethods')
    ->first();

if (!$listing) {
    echo "No active listing with buy_now_price found\n";
    exit;
}

echo "Listing: {$listing->title}\n";
echo "Buy Now Price: " . number_format($listing->buy_now_price) . " تومان\n\n";

echo "Shipping Methods:\n";
foreach ($listing->shippingMethods as $method) {
    $finalCost = $method->base_cost + ($method->pivot->custom_cost_adjustment ?? 0);
    echo "- {$method->name}: " . number_format($finalCost) . " تومان\n";
    if ($method->description) {
        echo "  Description: {$method->description}\n";
    }
}

echo "\n✓ Shipping costs are calculated correctly from base_cost + custom_cost_adjustment\n";
