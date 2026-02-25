<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = \App\Models\Order::find(8);
$buyer = $order->buyer;
$seller = $order->seller;
$site = \App\Models\User::find(1);

echo "Order #" . $order->order_number . "\n";
echo "Total: " . $order->total . "\n";

echo "\nBuyer wallet (user {$buyer->id}):\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\nSeller wallet (user {$seller->id}):\n";
echo "  Balance: " . $seller->wallet->balance . "\n";
echo "  Frozen: " . $seller->wallet->frozen . "\n";

echo "\nSite wallet (user 1):\n";
echo "  Balance: " . $site->wallet->balance . "\n";
echo "  Frozen: " . $site->wallet->frozen . "\n";

// Calculate expected values
$total = 40000;
$commission = $total * 0.05; // 5% default commission
$sellerAmount = $total - $commission;

echo "\n--- Expected ---\n";
echo "Total: {$total}\n";
echo "Commission (5%): {$commission}\n";
echo "Seller amount: {$sellerAmount}\n";

echo "\n--- Transactions ---\n";
$transactions = \App\Models\WalletTransaction::whereIn('user_id', [$buyer->id, $seller->id, 1])
    ->where('created_at', '>', now()->subMinutes(5))
    ->orderBy('id', 'desc')
    ->get();

foreach ($transactions as $tx) {
    echo "User {$tx->user_id} | Type: {$tx->type} | Amount: {$tx->amount} | Desc: {$tx->description}\n";
}
