<?php
// Check wallet page response
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Start session
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>بررسی پاسخ کیف پول</title>";
echo "<style>
body { font-family: Tahoma; padding: 20px; }
.info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; }
.success { background: #e8f5e9; padding: 15px; margin: 10px 0; border-radius: 5px; }
.error { background: #ffebee; padding: 15px; margin: 10px 0; border-radius: 5px; }
iframe { width: 100%; height: 600px; border: 2px solid #ddd; margin-top: 20px; }
</style>";
echo "</head>";
echo "<body>";

echo "<h1>بررسی پاسخ صفحه کیف پول</h1>";

if (!auth()->check()) {
    echo "<div class='error'>";
    echo "<p>❌ کاربر وارد نشده است</p>";
    echo "<p><a href='/login'>ورود به سیستم</a></p>";
    echo "</div>";
} else {
    $user = auth()->user();
    
    echo "<div class='success'>";
    echo "<p>✅ کاربر وارد شده: {$user->name}</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h2>درخواست به /wallet</h2>";
    
    // Make internal request to /wallet
    $walletRequest = \Illuminate\Http\Request::create('/wallet', 'GET');
    $walletRequest->setLaravelSession(app('session')->driver());
    
    try {
        $walletResponse = $kernel->handle($walletRequest);
        
        echo "<p>✅ درخواست ارسال شد</p>";
        echo "<p>کد وضعیت: <strong>" . $walletResponse->getStatusCode() . "</strong></p>";
        echo "<p>نوع محتوا: <strong>" . $walletResponse->headers->get('Content-Type') . "</strong></p>";
        
        $content = $walletResponse->getContent();
        $contentLength = strlen($content);
        
        echo "<p>طول محتوا: <strong>" . number_format($contentLength) . " کاراکتر</strong></p>";
        
        if ($contentLength < 500) {
            echo "<div class='error'>";
            echo "<p>⚠️ محتوا خیلی کوچک است!</p>";
            echo "<h3>محتوای دریافتی:</h3>";
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
            echo "</div>";
        } else {
            echo "<p>✅ محتوا به نظر کامل است</p>";
            
            // Check for key elements
            $checks = [
                'کیف پول' => strpos($content, 'کیف پول') !== false,
                'موجودی' => strpos($content, 'موجودی') !== false,
                'تراکنش' => strpos($content, 'تراکنش') !== false,
                'Tailwind CSS' => strpos($content, 'tailwindcss') !== false,
            ];
            
            echo "<h3>بررسی عناصر کلیدی:</h3>";
            echo "<ul>";
            foreach ($checks as $item => $exists) {
                $icon = $exists ? '✅' : '❌';
                echo "<li>{$icon} {$item}</li>";
            }
            echo "</ul>";
            
            // Save to temp file and show in iframe
            $tempFile = 'temp-wallet-' . time() . '.html';
            file_put_contents(__DIR__ . '/' . $tempFile, $content);
            
            echo "<h3>پیش‌نمایش صفحه:</h3>";
            echo "<iframe src='/{$tempFile}'></iframe>";
            
            echo "<p style='margin-top: 10px;'>";
            echo "<a href='/wallet' style='display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>مشاهده صفحه واقعی</a>";
            echo "</p>";
        }
        
    } catch (\Exception $e) {
        echo "<div class='error'>";
        echo "<h3>❌ خطا در دریافت پاسخ</h3>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
    
    echo "</div>";
}

echo "</body></html>";
