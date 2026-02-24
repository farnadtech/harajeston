<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

if (!auth()->check()) {
    die('Login first');
}

$store = auth()->user()->store;

echo "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'><title>بررسی تصاویر</title></head><body style='font-family:Tahoma;padding:20px;'>";
echo "<h1>بررسی تصاویر فروشگاه</h1>";

if (!$store) {
    echo "<p>فروشگاه یافت نشد</p>";
    exit;
}

echo "<h2>اطلاعات دیتابیس:</h2>";
echo "<p>Logo: <code>" . ($store->logo_image ?? 'NULL') . "</code></p>";
echo "<p>Banner: <code>" . ($store->banner_image ?? 'NULL') . "</code></p>";

echo "<h2>مسیرهای کامل:</h2>";
if ($store->logo_image) {
    $logoPath = storage_path('app/public/' . $store->logo_image);
    echo "<p>Logo Path: <code>$logoPath</code></p>";
    echo "<p>Exists: " . (file_exists($logoPath) ? '✅' : '❌') . "</p>";
    echo "<p>URL: <code>" . asset('storage/' . $store->logo_image) . "</code></p>";
    if (file_exists($logoPath)) {
        echo "<img src='" . asset('storage/' . $store->logo_image) . "' style='max-width:200px;border:1px solid #ccc;'>";
    }
}

echo "<hr>";

if ($store->banner_image) {
    $bannerPath = storage_path('app/public/' . $store->banner_image);
    echo "<p>Banner Path: <code>$bannerPath</code></p>";
    echo "<p>Exists: " . (file_exists($bannerPath) ? '✅' : '❌') . "</p>";
    echo "<p>URL: <code>" . asset('storage/' . $store->banner_image) . "</code></p>";
    if (file_exists($bannerPath)) {
        echo "<img src='" . asset('storage/' . $store->banner_image) . "' style='max-width:400px;border:1px solid #ccc;'>";
    }
}

echo "</body></html>";
