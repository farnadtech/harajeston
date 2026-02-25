<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPLETE AUCTION FLOW TEST ===\n\n";

$listing = \App\Models\Listing::find(19);
$winner = \App\Models\User::find(40);
$auctionService = app(\App\Services\AuctionService::class);

// Step 1: Finalize auction
echo "Step 1: Finalize Auction\n";
try {
    $order = $auctionService->finalizeAuction($listing, $winner);
    echo "✓ Order created: #" . $order->order_number . "\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit;
}

// Check wallet
$winner->refresh();
echo "  Buyer wallet: Balance={$winner->wallet->balance}, Frozen={$winner->wallet->frozen}\n";

// Check transactions
echo "  Transactions:\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $winner->id)
    ->where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', 19)
    ->orderBy('id', 'asc')
    ->get();

foreach ($transactions as $tx) {
    echo "    - Type: {$tx->type} | Amount: {$tx->amount} | Desc: {$tx->description}\n";
}

// Step 2: Mark as delivered
echo "\nStep 2: Mark as Delivered\n";
$order->status = 'delivered';
$order->updated_at = now();
$order->save();
echo "✓ Order marked as delivered\n";

// Step 3: Release payment (early release by buyer)
echo "\nStep 3: Release Payment (Early Release)\n";
try {
    $auctionService->releasePaymentToSeller($order);
    echo "✓ Payment released\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit;
}

// Check final wallets
$winner->refresh();
$seller = \App\Models\User::find(31);
$site = \App\Models\User::find(1);

echo "\n=== FINAL WALLETS ===\n";
echo "Buyer: Balance={$winner->wallet->balance}, Frozen={$winner->wallet->frozen}\n";
echo "Seller: Balance={$seller->wallet->balance}, Frozen={$seller->wallet->frozen}\n";
echo "Site: Balance={$site->wallet->balance}, Frozen={$site->wallet->frozen}\n";

// Check all transactions
echo "\n=== ALL TRANSACTIONS ===\n";
$allTransactions = \App\Models\WalletTransaction::whereIn('user_id', [40, 31, 1])
    ->where('created_at', '>', now()->subMinutes(5))
    ->orderBy('id', 'asc')
    ->get();

foreach ($allTransactions as $tx) {
    echo "User {$tx->user_id} | Type: {$tx->type} | Amount: {$tx->amount} | Desc: {$tx->description}\n";
}

echo "\n=== VERIFICATION ===\n";
$expectedCommission = 40000 * 0.05;
$expectedSellerAmount = 40000 - $expectedCommission;
$actualSellerReceived = $seller->wallet->balance - 120000;
$actualSiteReceived = $site->wallet->balance - 1470000;

echo "Expected commission: {$expectedCommission}\n";
echo "Expected seller amount: {$expectedSellerAmount}\n";
echo "Actual seller received: {$actualSellerReceived}\n";
echo "Actual site received: {$actualSiteReceived}\n";

if ($actualSellerReceived == $expectedSellerAmount && $actualSiteReceived == $expectedCommission) {
    echo "\n✓ ALL TESTS PASSED!\n";
} else {
    echo "\n✗ TESTS FAILED!\n";
}
