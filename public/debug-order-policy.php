<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get order
$orderId = 15;
$order = \App\Models\Order::with(['buyer', 'seller'])->find($orderId);

if (!$order) {
    die("Order not found\n");
}

echo "=== Order Info ===\n";
echo "Order ID: {$order->id}\n";
echo "Order Number: {$order->order_number}\n";
echo "Status: {$order->status}\n";
echo "Buyer ID: {$order->buyer_id}\n";
echo "Seller ID: {$order->seller_id}\n";
echo "\n";

// Test with buyer
$buyer = $order->buyer;
echo "=== Testing as Buyer (ID: {$buyer->id}) ===\n";
echo "Buyer Name: {$buyer->name}\n";

// Create policy instance
$policy = new \App\Policies\OrderPolicy();

echo "\nPolicy Tests:\n";
echo "- can view: " . ($policy->view($buyer, $order) ? 'YES' : 'NO') . "\n";
echo "- can updateStatus: " . ($policy->updateStatus($buyer, $order) ? 'YES' : 'NO') . "\n";

// Check conditions
echo "\nConditions:\n";
echo "- Is buyer: " . ($buyer->id === $order->buyer_id ? 'YES' : 'NO') . "\n";
echo "- Is seller: " . ($buyer->id === $order->seller_id ? 'YES' : 'NO') . "\n";
echo "- Order status: {$order->status}\n";
echo "- Status is 'shipped': " . ($order->status === 'shipped' ? 'YES' : 'NO') . "\n";

// Test with seller
echo "\n=== Testing as Seller (ID: {$order->seller_id}) ===\n";
$seller = $order->seller;
echo "Seller Name: {$seller->name}\n";
echo "- can view: " . ($policy->view($seller, $order) ? 'YES' : 'NO') . "\n";
echo "- can updateStatus: " . ($policy->updateStatus($seller, $order) ? 'YES' : 'NO') . "\n";
