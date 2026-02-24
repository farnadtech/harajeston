<?php
// Test wallet controller directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>تست کنترلر کیف پول</title>";
echo "<style>
body { font-family: Tahoma; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
.success { color: green; }
.error { color: red; }
pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
</style>";
echo "</head>";
echo "<body>";

echo "<h1>تست کنترلر کیف پول</h1>";

try {
    if (!auth()->check()) {
        echo "<p class='error'>کاربر وارد نشده است. <a href='/login'>ورود</a></p>";
        exit;
    }
    
    $user = auth()->user();
    echo "<div class='box'>";
    echo "<h2>کاربر: {$user->name}</h2>";
    echo "</div>";
    
    // Create controller instance
    $walletService = app(\App\Services\WalletService::class);
    $controller = new \App\Http\Controllers\WalletController($walletService);
    
    echo "<div class='box'>";
    echo "<h2>کنترلر ایجاد شد ✓</h2>";
    echo "</div>";
    
    // Create request
    $request = \Illuminate\Http\Request::create('/wallet', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    echo "<div class='box'>";
    echo "<h2>در حال فراخوانی متد show...</h2>";
    
    // Call controller method
    $response = $controller->show($request);
    
    echo "<p class='success'>✓ متد show با موفقیت اجرا شد</p>";
    echo "<p>نوع پاسخ: " . get_class($response) . "</p>";
    
    if ($response instanceof \Illuminate\View\View) {
        echo "<p class='success'>✓ View برگردانده شد</p>";
        echo "<p>نام View: " . $response->name() . "</p>";
        
        $data = $response->getData();
        echo "<p>داده‌های ارسال شده به View:</p>";
        echo "<ul>";
        foreach (array_keys($data) as $key) {
            echo "<li>{$key}</li>";
        }
        echo "</ul>";
        
        // Try to render
        echo "<h3>تلاش برای رندر View...</h3>";
        try {
            $html = $response->render();
            $length = strlen($html);
            echo "<p class='success'>✓ View رندر شد ({$length} کاراکتر)</p>";
            
            if ($length < 1000) {
                echo "<p class='error'>⚠️ محتوا خیلی کوچک است!</p>";
                echo "<pre>" . htmlspecialchars(substr($html, 0, 500)) . "</pre>";
            } else {
                echo "<p class='success'>✓ محتوا به نظر کامل است</p>";
                echo "<a href='/wallet' style='display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-top: 10px;'>مشاهده صفحه واقعی</a>";
            }
        } catch (\Exception $e) {
            echo "<p class='error'>✗ خطا در رندر:</p>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    } else {
        echo "<p class='error'>✗ پاسخ یک View نیست</p>";
    }
    
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div class='box'>";
    echo "<h2 class='error'>خطا</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
