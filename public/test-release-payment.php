<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = \App\Models\Order::find(8);

echo "Order #" . $order->order_number . "\n";
echo "Status: " . $order->status . "\n";
echo "Created: " . $order->created_at . "\n";
echo "Updated: " . $order->updated_at . "\n";

// Change status to delivered and set updated_at to 8 days ago
echo "\nChanging status to delivered and setting date to 8 days ago...\n";

$order->status = 'delivered';
$order->updated_at = now()->subDays(8);
$order->save();

echo "New status: " . $order->status . "\n";
echo "New updated_at: " . $order->updated_at . "\n";

// Check wallet before
$buyer = $order->buyer;
$seller = $order->seller;

echo "\nBuyer wallet (user {$buyer->id}):\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\nSeller wallet (user {$seller->id}):\n";
echo "  Balance: " . $seller->wallet->balance . "\n";
echo "  Frozen: " . $seller->wallet->frozen . "\n";

// Get site user (ID 1)
$site = \App\Models\User::find(1);
echo "\nSite wallet (user 1):\n";
echo "  Balance: " . $site->wallet->balance . "\n";
echo "  Frozen: " . $site->wallet->frozen . "\n";

echo "\n--- Now run: php artisan auction:release-payments ---\n";
