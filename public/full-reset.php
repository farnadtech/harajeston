<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Full Reset ===\n";

// Delete all orders for listing 19
$orders = \App\Models\Order::whereHas('items', function($q) {
    $q->where('listing_id', 19);
})->get();

foreach ($orders as $order) {
    echo "Deleting order #" . $order->order_number . "\n";
    $order->items()->delete();
    $order->delete();
}

// Delete all transactions for listing 19
\App\Models\WalletTransaction::where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', 19)
    ->delete();

echo "Deleted transactions\n";

// Reset listing
$listing = \App\Models\Listing::find(19);
$listing->status = 'ended';
$listing->save();

echo "Reset listing to ended\n";

// Reset wallets
$buyer = \App\Models\User::find(40);
$seller = \App\Models\User::find(31);
$site = \App\Models\User::find(1);

$buyer->wallet->balance = 150000;
$buyer->wallet->frozen = 3000;
$buyer->wallet->save();

$seller->wallet->balance = 120000;
$seller->wallet->frozen = 0;
$seller->wallet->save();

$site->wallet->balance = 1470000;
$site->wallet->frozen = 0;
$site->wallet->save();

echo "\nWallets reset:\n";
echo "Buyer: Balance={$buyer->wallet->balance}, Frozen={$buyer->wallet->frozen}\n";
echo "Seller: Balance={$seller->wallet->balance}, Frozen={$seller->wallet->frozen}\n";
echo "Site: Balance={$site->wallet->balance}, Frozen={$site->wallet->frozen}\n";

echo "\n=== Ready for fresh test ===\n";
