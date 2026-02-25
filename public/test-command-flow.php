<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STEP 1: Finalize Auction ===\n";

$listing = \App\Models\Listing::find(19);
$winner = \App\Models\User::find(40);

$auctionService = app(\App\Services\AuctionService::class);

try {
    $order = $auctionService->finalizeAuction($listing, $winner);
    echo "Order created: #" . $order->order_number . "\n";
    echo "Order ID: " . $order->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit;
}

// Check wallets
$buyer = \App\Models\User::find(40);
$seller = \App\Models\User::find(31);
$site = \App\Models\User::find(1);

echo "\nBuyer wallet:\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\n=== STEP 2: Mark as Delivered (8 days ago) ===\n";

$order->status = 'delivered';
$order->updated_at = now()->subDays(8);
$order->save();

echo "Order status: " . $order->status . "\n";
echo "Order updated_at: " . $order->updated_at . "\n";

echo "\n--- Now run: D:\\xamp8.1\\php\\php.exe artisan auction:release-payments ---\n";
