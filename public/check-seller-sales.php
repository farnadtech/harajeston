<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;
use App\Models\Order;

$store = Store::where('slug', 'froshgah-frzad')->first();

if (!$store) {
    echo "فروشگاه پیدا نشد.\n";
    exit;
}

$seller = $store->user;

echo "=== اطلاعات فروشنده ===\n\n";
echo "نام: {$seller->name}\n";
echo "ID: {$seller->id}\n\n";

echo "=== سفارشات فروشنده ===\n\n";

$allOrders = Order::where('seller_id', $seller->id)->get();
echo "کل سفارشات: " . $allOrders->count() . "\n\n";

$statusCounts = Order::where('seller_id', $seller->id)
    ->selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();

echo "تفکیک بر اساس وضعیت:\n";
foreach ($statusCounts as $status) {
    echo "  - {$status->status}: {$status->count}\n";
}

echo "\n";

$completedOld = Order::where('seller_id', $seller->id)
    ->where('status', 'completed')
    ->count();

$completedNew = Order::where('seller_id', $seller->id)
    ->whereIn('status', ['delivered', 'completed'])
    ->count();

echo "فروش موفق (روش قبلی - فقط completed): {$completedOld}\n";
echo "فروش موفق (روش جدید - delivered + completed): {$completedNew}\n\n";

echo "=== جزئیات سفارشات delivered ===\n\n";
$deliveredOrders = Order::where('seller_id', $seller->id)
    ->where('status', 'delivered')
    ->with('listing')
    ->get();

if ($deliveredOrders->count() > 0) {
    foreach ($deliveredOrders as $order) {
        echo "سفارش #{$order->id}:\n";
        echo "  - محصول: " . ($order->listing ? $order->listing->title : 'حذف شده') . "\n";
        echo "  - مبلغ: " . number_format($order->total) . " تومان\n";
        echo "  - تاریخ: {$order->created_at}\n";
        echo "  - وضعیت: {$order->status}\n\n";
    }
} else {
    echo "هیچ سفارش delivered وجود ندارد.\n";
}
