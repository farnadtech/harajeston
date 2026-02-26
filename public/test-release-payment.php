<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get order
$orderId = 15;
$order = \App\Models\Order::with(['buyer.wallet', 'seller.wallet', 'items.listing'])->find($orderId);

if (!$order) {
    die("Order not found\n");
}

echo "=== Before Release ===\n";
echo "Order: #{$order->order_number}\n";
echo "Total: " . number_format($order->total) . " تومان\n";
echo "\n";

echo "Buyer Wallet:\n";
echo "- Balance: " . number_format($order->buyer->wallet->balance) . "\n";
echo "- Frozen: " . number_format($order->buyer->wallet->frozen) . "\n";
echo "\n";

echo "Seller Wallet:\n";
echo "- Balance: " . number_format($order->seller->wallet->balance) . "\n";
echo "- Frozen: " . number_format($order->seller->wallet->frozen) . "\n";
echo "\n";

// Get listing
$listing = $order->items->first()->listing;
echo "Listing Category: {$listing->category_id}\n";

// Calculate commission
$commissionService = app(\App\Services\CommissionService::class);
$commission = $commissionService->calculateCommission($order->total, $listing->category_id);
$sellerAmount = $order->total - $commission;

echo "\nCommission Calculation:\n";
echo "- Total: " . number_format($order->total) . "\n";
echo "- Commission: " . number_format($commission) . "\n";
echo "- Seller Amount: " . number_format($sellerAmount) . "\n";
echo "\n";

// Count transactions before
$buyerTxBefore = \App\Models\WalletTransaction::where('user_id', $order->buyer_id)->count();
$sellerTxBefore = \App\Models\WalletTransaction::where('user_id', $order->seller_id)->count();

echo "Transactions Before:\n";
echo "- Buyer: {$buyerTxBefore}\n";
echo "- Seller: {$sellerTxBefore}\n";
echo "\n";

// Release payment
try {
    $auctionService = app(\App\Services\AuctionService::class);
    $auctionService->releasePaymentToSeller($order);
    echo "✓ Payment released successfully\n\n";
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

// Refresh
$order->refresh();
$order->buyer->wallet->refresh();
$order->seller->wallet->refresh();

echo "=== After Release ===\n";
echo "Buyer Wallet:\n";
echo "- Balance: " . number_format($order->buyer->wallet->balance) . "\n";
echo "- Frozen: " . number_format($order->buyer->wallet->frozen) . "\n";
echo "\n";

echo "Seller Wallet:\n";
echo "- Balance: " . number_format($order->seller->wallet->balance) . "\n";
echo "- Frozen: " . number_format($order->seller->wallet->frozen) . "\n";
echo "\n";

// Count transactions after
$buyerTxAfter = \App\Models\WalletTransaction::where('user_id', $order->buyer_id)->count();
$sellerTxAfter = \App\Models\WalletTransaction::where('user_id', $order->seller_id)->count();

echo "Transactions After:\n";
echo "- Buyer: {$buyerTxAfter} (+" . ($buyerTxAfter - $buyerTxBefore) . ")\n";
echo "- Seller: {$sellerTxAfter} (+" . ($sellerTxAfter - $sellerTxBefore) . ")\n";
echo "\n";

// Show new transactions
echo "=== New Buyer Transactions ===\n";
$buyerNewTx = \App\Models\WalletTransaction::where('user_id', $order->buyer_id)
    ->orderBy('id', 'desc')
    ->limit($buyerTxAfter - $buyerTxBefore)
    ->get();

foreach ($buyerNewTx as $tx) {
    echo "- {$tx->type}: " . number_format($tx->amount) . " - {$tx->description}\n";
}
echo "\n";

echo "=== New Seller Transactions ===\n";
$sellerNewTx = \App\Models\WalletTransaction::where('user_id', $order->seller_id)
    ->orderBy('id', 'desc')
    ->limit($sellerTxAfter - $sellerTxBefore)
    ->get();

foreach ($sellerNewTx as $tx) {
    echo "- {$tx->type}: " . number_format($tx->amount) . " - {$tx->description}\n";
}
