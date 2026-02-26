<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Test order status update
$orderId = 15; // Replace with your order ID

$order = \App\Models\Order::find($orderId);

if (!$order) {
    die("Order not found");
}

echo "Order #" . $order->order_number . "\n";
echo "Status: " . $order->status . "\n";
echo "Buyer ID: " . $order->buyer_id . "\n";
echo "Seller ID: " . $order->seller_id . "\n";
echo "\n";

// Test with buyer user
$buyer = \App\Models\User::find($order->buyer_id);
auth()->login($buyer);

echo "Logged in as buyer: " . $buyer->name . "\n";
echo "Can view order: " . (auth()->user()->can('view', $order) ? 'YES' : 'NO') . "\n";
echo "Can update status: " . (auth()->user()->can('updateStatus', $order) ? 'YES' : 'NO') . "\n";
echo "\n";

// Check route
$route = route('orders.updateStatus', $order);
echo "Route: " . $route . "\n";
echo "\n";

// Check if order is shipped
if ($order->status === 'shipped') {
    echo "Order is shipped - buyer can confirm delivery\n";
} else {
    echo "Order status is '{$order->status}' - not shipped yet\n";
}
