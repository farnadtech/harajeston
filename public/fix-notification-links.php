<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اصلاح لینک‌های نوتیفیکیشن ===\n\n";

// پیدا کردن نوتیفیکیشن‌های سفارش که لینک admin دارند
$notifications = \App\Models\Notification::where('type', 'order')
    ->orWhere('type', 'order_status')
    ->get();

echo "تعداد کل نوتیفیکیشن‌های سفارش: {$notifications->count()}\n\n";

$fixed = 0;
foreach ($notifications as $notification) {
    $user = \App\Models\User::find($notification->user_id);
    if (!$user) continue;
    
    // اگر کاربر ادمین نیست و لینک admin داره، درستش کن
    if ($user->role !== 'admin' && strpos($notification->link, '/admin/orders/') !== false) {
        // استخراج order ID از لینک
        preg_match('/\/admin\/orders\/(\d+)/', $notification->link, $matches);
        if (isset($matches[1])) {
            $orderId = $matches[1];
            $newLink = route('orders.show', $orderId);
            
            echo "نوتیفیکیشن #{$notification->id} (User #{$user->id} - {$user->role}):\n";
            echo "  قبل: {$notification->link}\n";
            echo "  بعد: {$newLink}\n\n";
            
            $notification->link = $newLink;
            $notification->save();
            $fixed++;
        }
    }
}

echo "\n✓ تعداد نوتیفیکیشن‌های اصلاح شده: {$fixed}\n";
