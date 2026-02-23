<?php
// تست مستقیم دسترسی به صفحه ساخت حراجی
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// ایجاد request برای /listings/create
$request = Illuminate\Http\Request::create('/listings/create', 'GET');

// اضافه کردن session
$request->setLaravelSession(app('session.store'));

// Handle request
try {
    $response = $kernel->handle($request);
    
    echo "<!DOCTYPE html>";
    echo "<html dir='rtl' lang='fa'>";
    echo "<head><meta charset='utf-8'><title>تست مستقیم</title>";
    echo "<style>body{font-family:Tahoma;padding:20px;background:#f5f5f5;}.box{background:white;padding:20px;margin:10px 0;border-radius:8px;}</style>";
    echo "</head><body>";
    
    echo "<div class='box'>";
    echo "<h2>نتیجه تست مستقیم</h2>";
    echo "<p>Status Code: <strong>" . $response->getStatusCode() . "</strong></p>";
    
    if ($response->getStatusCode() === 200) {
        echo "<p style='color:green;'>✅ روت کار می‌کند!</p>";
        echo "<hr>";
        echo $response->getContent();
    } elseif ($response->getStatusCode() === 302) {
        echo "<p style='color:orange;'>⚠ Redirect به: " . $response->headers->get('Location') . "</p>";
        echo "<p>احتمالاً به دلیل عدم احراز هویت یا عدم دسترسی</p>";
    } else {
        echo "<p style='color:red;'>❌ خطا!</p>";
        echo "<pre>" . $response->getContent() . "</pre>";
    }
    echo "</div>";
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>";
    echo "<html dir='rtl'><head><meta charset='utf-8'></head><body>";
    echo "<h2 style='color:red;'>خطا در اجرا</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</body></html>";
}

$kernel->terminate($request, $response);
?>
