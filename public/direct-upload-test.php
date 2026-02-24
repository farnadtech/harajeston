<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>تست مستقیم آپلود تصویر</h2>";

// Login as seller
$seller = \App\Models\User::where('role', 'seller')->first();
if (!$seller) {
    echo "❌ فروشنده‌ای یافت نشد<br>";
    exit;
}

auth()->login($seller);
echo "✅ وارد شدید: {$seller->name}<br>";

$store = $seller->store;
if (!$store) {
    echo "❌ فروشگاه یافت نشد<br>";
    exit;
}

echo "✅ فروشگاه: {$store->store_name}<br><br>";

// Create a test image
$testImagePath = storage_path('app/test-logo.jpg');
if (!file_exists($testImagePath)) {
    // Create a simple test image
    $img = imagecreatetruecolor(300, 300);
    $bgColor = imagecolorallocate($img, 66, 135, 245);
    imagefill($img, 0, 0, $bgColor);
    $textColor = imagecolorallocate($img, 255, 255, 255);
    imagestring($img, 5, 100, 140, 'TEST LOGO', $textColor);
    imagejpeg($img, $testImagePath);
    imagedestroy($img);
    echo "✅ تصویر تست ساخته شد<br>";
}

// Create UploadedFile instance
$uploadedFile = new \Illuminate\Http\UploadedFile(
    $testImagePath,
    'test-logo.jpg',
    'image/jpeg',
    null,
    true
);

echo "✅ فایل آپلود آماده شد<br>";

// Use StoreService to upload
$storeService = app(\App\Services\StoreService::class);

try {
    echo "<br><h3>آپلود لوگو...</h3>";
    $storeService->updateStoreProfile($store, [
        'logo' => $uploadedFile,
    ]);
    
    $store->refresh();
    echo "✅ لوگو آپلود شد!<br>";
    echo "Logo Path: {$store->logo_image}<br>";
    
    $fullPath = storage_path('app/public/' . $store->logo_image);
    echo "Full Path: $fullPath<br>";
    echo "Exists: " . (file_exists($fullPath) ? '✅' : '❌') . "<br>";
    
    $url = asset('storage/' . $store->logo_image);
    echo "URL: <a href='$url' target='_blank'>$url</a><br>";
    echo "<img src='$url' style='max-width:200px;border:2px solid green;'><br>";
    
} catch (\Exception $e) {
    echo "❌ خطا: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
