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
    echo "Order total: " . $order->total . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit;
}

// Check wallets after finalize
$buyer = \App\Models\User::find(40);
$seller = \App\Models\User::find(31);
$site = \App\Models\User::find(1);

echo "\nBuyer wallet after finalize:\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\nSeller wallet after finalize:\n";
echo "  Balance: " . $seller->wallet->balance . "\n";
echo "  Frozen: " . $seller->wallet->frozen . "\n";

echo "\nSite wallet after finalize:\n";
echo "  Balance: " . $site->wallet->balance . "\n";
echo "  Frozen: " . $site->wallet->frozen . "\n";

// Check transactions
echo "\nTransactions:\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $buyer->id)
    ->where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', 19)
    ->orderBy('id', 'desc')
    ->get();

foreach ($transactions as $tx) {
    echo "  ID: {$tx->id} | Type: {$tx->type} | Amount: {$tx->amount} | Frozen: {$tx->frozen_before} -> {$tx->frozen_after}\n";
}

echo "\n=== STEP 2: Mark as Delivered (8 days ago) ===\n";

$order->status = 'delivered';
$order->updated_at = now()->subDays(8);
$order->save();

echo "Order status: " . $order->status . "\n";
echo "Order updated_at: " . $order->updated_at . "\n";

echo "\n=== STEP 3: Release Payment ===\n";

try {
    $auctionService->releasePaymentToSeller($order);
    echo "Payment released successfully\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit;
}

// Check wallets after release
$buyer->refresh();
$seller->refresh();
$site->refresh();

echo "\nBuyer wallet after release:\n";
echo "  Balance: " . $buyer->wallet->balance . "\n";
echo "  Frozen: " . $buyer->wallet->frozen . "\n";

echo "\nSeller wallet after release:\n";
echo "  Balance: " . $seller->wallet->balance . "\n";
echo "  Frozen: " . $seller->wallet->frozen . "\n";

echo "\nSite wallet after release:\n";
echo "  Balance: " . $site->wallet->balance . "\n";
echo "  Frozen: " . $site->wallet->frozen . "\n";

// Calculate expected values
$total = 40000;
$commission = $total * 0.05; // 5%
$sellerAmount = $total - $commission;

echo "\n=== Expected Values ===\n";
echo "Total: {$total}\n";
echo "Commission (5%): {$commission}\n";
echo "Seller amount: {$sellerAmount}\n";

echo "\n=== Actual Values ===\n";
echo "Seller received: " . ($seller->wallet->balance - 120000) . "\n";
echo "Site received: " . ($site->wallet->balance - 1470000) . "\n";

// Check all transactions
echo "\n=== All Transactions ===\n";
$allTransactions = \App\Models\WalletTransaction::whereIn('user_id', [40, 31, 1])
    ->where('created_at', '>', now()->subMinutes(5))
    ->orderBy('id', 'desc')
    ->get();

foreach ($allTransactions as $tx) {
    echo "User {$tx->user_id} | Type: {$tx->type} | Amount: {$tx->amount} | Desc: {$tx->description}\n";
}
