<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::find(19);

echo "Current status: " . $listing->status . "\n";

// Reset to ended
$listing->status = 'ended';
$listing->save();

echo "New status: " . $listing->status . "\n";

// Delete order 8
$order = \App\Models\Order::find(8);
if ($order) {
    echo "\nDeleting order #" . $order->order_number . "...\n";
    
    // Delete order items
    $order->items()->delete();
    
    // Delete order
    $order->delete();
    
    echo "Order deleted\n";
}

// Reset wallet transactions
echo "\nResetting wallet transactions...\n";

// Delete transactions related to this listing
\App\Models\WalletTransaction::where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', 19)
    ->delete();

echo "Transactions deleted\n";

// Reset wallets
$buyer = \App\Models\User::find(40);
$seller = \App\Models\User::find(31);
$site = \App\Models\User::find(1);

echo "\nResetting wallets...\n";

// Buyer: balance = 150000, frozen = 3000 (only deposit)
$buyer->wallet->balance = 150000;
$buyer->wallet->frozen = 3000;
$buyer->wallet->save();

// Seller: balance = 120000, frozen = 0
$seller->wallet->balance = 120000;
$seller->wallet->frozen = 0;
$seller->wallet->save();

// Site: balance = 1470000, frozen = 0
$site->wallet->balance = 1470000;
$site->wallet->frozen = 0;
$site->wallet->save();

echo "Wallets reset\n";

echo "\nBuyer wallet:\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\nSeller wallet:\n";
echo "  Balance: " . $seller->wallet->balance . "\n";
echo "  Frozen: " . $seller->wallet->frozen . "\n";

echo "\nSite wallet:\n";
echo "  Balance: " . $site->wallet->balance . "\n";
echo "  Frozen: " . $site->wallet->frozen . "\n";

echo "\n--- Ready to test finalize again ---\n";
