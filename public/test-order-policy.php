<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Login as buyer (user 40)
$buyer = \App\Models\User::find(40);
if (!$buyer) {
    die("User 40 not found");
}

auth()->login($buyer);

echo "=== تست Order Policy ===\n\n";
echo "کاربر: {$buyer->name} (ID: {$buyer->id})\n";
echo "نقش: {$buyer->role}\n\n";

// Get order 15
$order = \App\Models\Order::find(15);
if (!$order) {
    die("Order 15 not found");
}

echo "سفارش #{$order->id}\n";
echo "خریدار: User #{$order->buyer_id}\n";
echo "فروشنده: User #{$order->seller_id}\n";
echo "وضعیت: {$order->status}\n\n";

// Test policy
$policy = new \App\Policies\OrderPolicy();

echo "=== تست Policy Methods ===\n";
echo "view: " . ($policy->view($buyer, $order) ? 'YES' : 'NO') . "\n";
echo "updateStatus: " . ($policy->updateStatus($buyer, $order) ? 'YES' : 'NO') . "\n";
echo "cancel: " . ($policy->cancel($buyer, $order) ? 'YES' : 'NO') . "\n";

echo "\n=== شرایط updateStatus ===\n";
echo "آیا کاربر خریدار است؟ " . ($buyer->id === $order->buyer_id ? 'YES' : 'NO') . "\n";
echo "آیا وضعیت shipped است؟ " . ($order->status === 'shipped' ? 'YES' : 'NO') . "\n";
echo "آیا کاربر فروشنده است؟ " . ($buyer->id === $order->seller_id ? 'YES' : 'NO') . "\n";
