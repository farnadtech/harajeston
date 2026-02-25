<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = \App\Models\Order::find(8);

if (!$order) {
    echo "Order not found\n";
    exit;
}

echo "Order ID: " . $order->id . "\n";
echo "Order Number: " . $order->order_number . "\n";
echo "Total: " . $order->total . "\n";
echo "Subtotal: " . $order->subtotal . "\n";
echo "Shipping Cost: " . $order->shipping_cost . "\n";
echo "Buyer ID: " . $order->buyer_id . "\n";
echo "Seller ID: " . $order->seller_id . "\n";
echo "\nOrder Items:\n";

foreach ($order->items as $item) {
    echo "  - Listing ID: " . $item->listing_id . "\n";
    echo "    Price: " . $item->price_snapshot . "\n";
    echo "    Quantity: " . $item->quantity . "\n";
    echo "    Subtotal: " . $item->subtotal . "\n";
}

// Check wallet transactions
echo "\nWallet Transactions for buyer (user " . $order->buyer_id . "):\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $order->buyer_id)
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($transactions as $tx) {
    echo "  ID: {$tx->id} | Type: {$tx->type} | Amount: {$tx->amount} | Frozen Before: {$tx->frozen_before} | Frozen After: {$tx->frozen_after} | Desc: {$tx->description}\n";
}
