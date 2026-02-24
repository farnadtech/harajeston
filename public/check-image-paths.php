<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>بررسی مسیرهای تصاویر فروشگاه</h2>";

$store = \App\Models\Store::first();
if (!$store) {
    echo "❌ فروشگاهی یافت نشد<br>";
    exit;
}

echo "<h3>اطلاعات دیتابیس:</h3>";
echo "Logo Image: " . ($store->logo_image ?? 'NULL') . "<br>";
echo "Banner Image: " . ($store->banner_image ?? 'NULL') . "<br><br>";

echo "<h3>مسیرهای کامل:</h3>";

if ($store->logo_image) {
    $logoPath = storage_path('app/public/' . $store->logo_image);
    $logoUrl = asset('storage/' . $store->logo_image);
    echo "Logo Path: $logoPath<br>";
    echo "Exists: " . (file_exists($logoPath) ? '✅' : '❌') . "<br>";
    echo "URL: $logoUrl<br>";
    echo "<img src='$logoUrl' style='max-width:200px;border:2px solid red;'><br><br>";
}

if ($store->banner_image) {
    $bannerPath = storage_path('app/public/' . $store->banner_image);
    $bannerUrl = asset('storage/' . $store->banner_image);
    echo "Banner Path: $bannerPath<br>";
    echo "Exists: " . (file_exists($bannerPath) ? '✅' : '❌') . "<br>";
    echo "URL: $bannerUrl<br>";
    echo "<img src='$bannerUrl' style='max-width:400px;border:2px solid red;'><br><br>";
}

echo "<h3>بررسی Symlink:</h3>";
$symlinkPath = public_path('storage');
echo "Symlink Path: $symlinkPath<br>";
echo "Exists: " . (file_exists($symlinkPath) ? '✅' : '❌') . "<br>";
echo "Is Link: " . (is_link($symlinkPath) ? '✅' : '❌') . "<br>";
if (is_link($symlinkPath)) {
    echo "Target: " . readlink($symlinkPath) . "<br>";
}

echo "<h3>تست مستقیم تصویر:</h3>";
if ($store->logo_image) {
    $directPath = '/haraj/public/storage/' . $store->logo_image;
    echo "Direct URL: <a href='$directPath' target='_blank'>$directPath</a><br>";
    echo "<img src='$directPath' style='max-width:200px;border:2px solid blue;'><br>";
}
