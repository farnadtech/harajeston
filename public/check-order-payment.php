<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$order = \App\Models\Order::with(['buyer.wallet'])->find(15);

echo "Order: #{$order->order_number}\n";
echo "Status: {$order->status}\n";
echo "Total: " . number_format($order->total) . "\n";
echo "Payment released: " . ($order->payment_released_at ? 'YES at ' . $order->payment_released_at : 'NO') . "\n";
echo "\n";

echo "Buyer Wallet:\n";
echo "- Balance: " . number_format($order->buyer->wallet->balance) . "\n";
echo "- Frozen: " . number_format($order->buyer->wallet->frozen) . "\n";
echo "\n";

// Check transactions
$txs = \App\Models\WalletTransaction::where('user_id', $order->buyer_id)
    ->where('reference_type', \App\Models\Order::class)
    ->where('reference_id', $order->id)
    ->orderBy('id', 'desc')
    ->get();

echo "Order-related transactions for buyer:\n";
foreach ($txs as $tx) {
    echo "- {$tx->type}: " . number_format($tx->amount) . " (frozen: {$tx->frozen_before} -> {$tx->frozen_after}) - {$tx->description}\n";
}
