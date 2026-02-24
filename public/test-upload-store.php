<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Login as seller
$seller = \App\Models\User::where('role', 'seller')->first();
if (!$seller) {
    echo "❌ فروشنده‌ای یافت نشد<br>";
    exit;
}

auth()->login($seller);
echo "✅ وارد شدید: {$seller->name}<br><br>";

$store = $seller->store;
if (!$store) {
    echo "❌ فروشگاه یافت نشد<br>";
    exit;
}

echo "<h3>اطلاعات فروشگاه:</h3>";
echo "ID: {$store->id}<br>";
echo "Name: {$store->store_name}<br>";
echo "Logo: " . ($store->logo_image ?? 'NULL') . "<br>";
echo "Banner: " . ($store->banner_image ?? 'NULL') . "<br><br>";

echo "<h3>تست آپلود:</h3>";
echo "<form action='/haraj/public/stores/upload-logo' method='POST' enctype='multipart/form-data'>";
echo csrf_field();
echo "<label>لوگو (حداکثر 1MB):</label><br>";
echo "<input type='file' name='logo' accept='image/*' required><br><br>";
echo "<button type='submit'>آپلود لوگو</button>";
echo "</form><br>";

echo "<form action='/haraj/public/stores/upload-banner' method='POST' enctype='multipart/form-data'>";
echo csrf_field();
echo "<label>بنر (حداکثر 2MB):</label><br>";
echo "<input type='file' name='banner' accept='image/*' required><br><br>";
echo "<button type='submit'>آپلود بنر</button>";
echo "</form>";
