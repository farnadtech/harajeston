<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get order
$orderId = 15;
$order = \App\Models\Order::find($orderId);

if (!$order) {
    die("Order not found\n");
}

echo "Order ID: {$order->id}\n";
echo "Status: {$order->status}\n";
echo "Buyer ID: {$order->buyer_id}\n";
echo "\n";

// Create request mock
$request = new \App\Http\Requests\UpdateOrderStatusRequest();
$request->merge(['status' => 'delivered']);
$request->setRouteResolver(function () use ($order) {
    $route = new \Illuminate\Routing\Route('PUT', 'orders/{order}/status', []);
    $route->bind(new \Illuminate\Http\Request());
    $route->setParameter('order', $order);
    return $route;
});

// Set user
$buyer = \App\Models\User::find($order->buyer_id);
$request->setUserResolver(function () use ($buyer) {
    return $buyer;
});

echo "Testing authorization as buyer...\n";
try {
    $authorized = $request->authorize();
    echo "Authorization result: " . ($authorized ? 'ALLOWED' : 'DENIED') . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nConditions:\n";
echo "- User is buyer: " . ($buyer->id === $order->buyer_id ? 'YES' : 'NO') . "\n";
echo "- Order status is 'shipped': " . ($order->status === 'shipped' ? 'YES' : 'NO') . "\n";
echo "- Request status is 'delivered': " . ($request->status === 'delivered' ? 'YES' : 'NO') . "\n";
