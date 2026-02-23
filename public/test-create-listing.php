<?php
// تست دسترسی به صفحه ساخت حراجی
echo "<h2>تست روت ساخت حراجی</h2>";

// تست 1: بررسی فایل روت
echo "<h3>1. بررسی فایل routes/web.php</h3>";
$routeFile = file_get_contents(__DIR__ . '/../routes/web.php');
if (strpos($routeFile, "Route::get('/listings/create'") !== false) {
    echo "✅ روت listings.create در فایل وجود دارد<br>";
} else {
    echo "❌ روت listings.create در فایل یافت نشد<br>";
}

// تست 2: بررسی کنترلر
echo "<h3>2. بررسی کنترلر</h3>";
$controllerFile = __DIR__ . '/../app/Http/Controllers/ListingController.php';
if (file_exists($controllerFile)) {
    echo "✅ فایل کنترلر وجود دارد<br>";
    $content = file_get_contents($controllerFile);
    if (strpos($content, 'public function create()') !== false) {
        echo "✅ متد create در کنترلر وجود دارد<br>";
    } else {
        echo "❌ متد create در کنترلر یافت نشد<br>";
    }
} else {
    echo "❌ فایل کنترلر یافت نشد<br>";
}

// تست 3: بررسی view
echo "<h3>3. بررسی view</h3>";
$viewFile = __DIR__ . '/../resources/views/listings/create-new.blade.php';
if (file_exists($viewFile)) {
    echo "✅ فایل view وجود دارد<br>";
} else {
    echo "❌ فایل view یافت نشد<br>";
}

// تست 4: لینک مستقیم
echo "<h3>4. لینک‌های تست</h3>";
echo '<a href="/listings/create" target="_blank">🔗 باز کردن صفحه ساخت حراجی</a><br>';
echo '<a href="/login" target="_blank">🔗 صفحه ورود (اگر لاگین نیستید)</a><br>';

echo "<hr>";
echo "<p><strong>توجه:</strong> برای دسترسی به صفحه ساخت حراجی باید:</p>";
echo "<ul>";
echo "<li>✅ لاگین کرده باشید</li>";
echo "<li>✅ نقش فروشنده داشته باشید</li>";
echo "</ul>";
?>
