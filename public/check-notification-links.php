<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Notification;
use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "=== بررسی لینک‌های نوتیفیکیشن ===\n\n";

// نمایش نوتیفیکیشن‌های اخیر
$notifications = DB::table('notifications')
    ->join('users', 'notifications.user_id', '=', 'users.id')
    ->select('notifications.id', 'notifications.title', 'notifications.link', 'users.name', 'users.role')
    ->orderBy('notifications.created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($notifications as $notif) {
    echo "ID: {$notif->id}\n";
    echo "کاربر: {$notif->name} (نقش: {$notif->role})\n";
    echo "عنوان: {$notif->title}\n";
    echo "لینک: {$notif->link}\n";
    echo "---\n";
}

echo "\n=== آمار لینک‌ها ===\n";
$adminLinks = DB::table('notifications')->where('link', 'like', '%/admin/listings/%')->count();
$normalLinks = DB::table('notifications')->where('link', 'like', '%/listings/%')->where('link', 'not like', '%/admin/%')->count();

echo "لینک‌های admin: {$adminLinks}\n";
echo "لینک‌های عادی: {$normalLinks}\n";

echo "</pre>";
