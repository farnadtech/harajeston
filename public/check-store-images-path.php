<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Store;

$store = Store::where('slug', 'froshgah-frzad')->first();

if (!$store) {
    echo "فروشگاه پیدا نشد.\n";
    exit;
}

echo "=== اطلاعات تصاویر فروشگاه ===\n\n";
echo "نام فروشگاه: {$store->store_name}\n";
echo "Slug: {$store->slug}\n\n";

echo "بنر:\n";
echo "  - مقدار در دیتابیس: " . ($store->banner_image ?? 'null') . "\n";
if ($store->banner_image) {
    $bannerPath = storage_path('app/public/' . $store->banner_image);
    echo "  - مسیر کامل: {$bannerPath}\n";
    echo "  - فایل وجود دارد: " . (file_exists($bannerPath) ? 'بله' : 'خیر') . "\n";
    echo "  - URL در ویو: " . asset('storage/' . $store->banner_image) . "\n";
}

echo "\nلوگو:\n";
echo "  - مقدار در دیتابیس: " . ($store->logo_image ?? 'null') . "\n";
if ($store->logo_image) {
    $logoPath = storage_path('app/public/' . $store->logo_image);
    echo "  - مسیر کامل: {$logoPath}\n";
    echo "  - فایل وجود دارد: " . (file_exists($logoPath) ? 'بله' : 'خیر') . "\n";
    echo "  - URL در ویو: " . asset('storage/' . $store->logo_image) . "\n";
}

echo "\n=== بررسی ساختار دایرکتوری ===\n\n";

$storagePublic = storage_path('app/public');
echo "مسیر storage/app/public: {$storagePublic}\n";
echo "وجود دارد: " . (is_dir($storagePublic) ? 'بله' : 'خیر') . "\n\n";

$publicStorage = public_path('storage');
echo "مسیر public/storage: {$publicStorage}\n";
echo "وجود دارد: " . (is_dir($publicStorage) ? 'بله' : 'خیر') . "\n";
echo "نوع: " . (is_link($publicStorage) ? 'symlink' : (is_dir($publicStorage) ? 'directory' : 'نامشخص')) . "\n";

if (is_link($publicStorage)) {
    echo "لینک به: " . readlink($publicStorage) . "\n";
}

echo "\n=== فایل‌های موجود در stores ===\n\n";

$storesDir = storage_path('app/public/stores');
if (is_dir($storesDir)) {
    echo "دایرکتوری stores وجود دارد.\n";
    $files = scandir($storesDir);
    echo "فایل‌ها:\n";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - {$file}\n";
        }
    }
} else {
    echo "دایرکتوری stores وجود ندارد!\n";
}
