<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::with('store')->find(31);

if (!$user) {
    echo "کاربر با ID 31 پیدا نشد.\n";
    exit;
}

echo "=== اطلاعات کاربر 31 ===\n\n";
echo "نام: {$user->name}\n";
echo "ایمیل: {$user->email}\n";
echo "نقش: {$user->role}\n";
echo "وضعیت فروشنده: " . ($user->seller_status ?? 'null') . "\n\n";

echo "=== اطلاعات فروشگاه ===\n\n";

if ($user->store) {
    echo "فروشگاه وجود دارد:\n";
    echo "ID: {$user->store->id}\n";
    echo "نام: " . ($user->store->name ?? 'null') . "\n";
    echo "توضیحات: " . ($user->store->description ?? 'null') . "\n";
    echo "Slug: " . ($user->store->slug ?? 'null') . "\n";
} else {
    echo "فروشگاه وجود ندارد!\n";
    echo "\nبررسی مستقیم در دیتابیس:\n";
    
    $store = DB::table('stores')->where('user_id', 31)->first();
    if ($store) {
        echo "فروشگاه در دیتابیس پیدا شد:\n";
        echo "ID: {$store->id}\n";
        echo "User ID: {$store->user_id}\n";
        echo "نام: " . ($store->name ?? 'null') . "\n";
        echo "توضیحات: " . ($store->description ?? 'null') . "\n";
    } else {
        echo "فروشگاه در دیتابیس هم پیدا نشد.\n";
    }
}
