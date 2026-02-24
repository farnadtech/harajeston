<?php
// Test wallet for different roles
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>تست کیف پول بر اساس نقش</title>";
echo "<style>
body { font-family: Tahoma; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
.success { color: green; }
.error { color: red; }
.info { color: blue; }
</style>";
echo "</head>";
echo "<body>";

echo "<h1>تست کیف پول بر اساس نقش کاربر</h1>";

if (!auth()->check()) {
    echo "<p class='error'>کاربر وارد نشده است. <a href='/login'>ورود</a></p>";
    exit;
}

$user = auth()->user();

echo "<div class='box'>";
echo "<h2>اطلاعات کاربر</h2>";
echo "<p><strong>نام:</strong> {$user->name}</p>";
echo "<p><strong>نقش:</strong> {$user->role}</p>";
echo "<p><strong>وضعیت فروشندگی:</strong> {$user->seller_status}</p>";
echo "<p><strong>canSell():</strong> " . ($user->canSell() ? 'بله' : 'خیر') . "</p>";
echo "</div>";

echo "<div class='box'>";
echo "<h2>تشخیص View مناسب</h2>";

if ($user->role === 'admin') {
    echo "<p class='info'>✓ View: wallet.admin</p>";
    echo "<p>Layout: layouts.admin</p>";
} elseif ($user->canSell()) {
    echo "<p class='info'>✓ View: wallet.seller</p>";
    echo "<p>Layout: layouts.seller</p>";
} else {
    echo "<p class='info'>✓ View: wallet.show</p>";
    echo "<p>Layout: layouts.app</p>";
}
echo "</div>";

echo "<div class='box'>";
echo "<h2>بررسی فایل‌های View</h2>";

$views = [
    'wallet.admin' => 'resources/views/wallet/admin.blade.php',
    'wallet.seller' => 'resources/views/wallet/seller.blade.php',
    'wallet.show' => 'resources/views/wallet/show.blade.php',
    'wallet.show-content' => 'resources/views/wallet/show-content.blade.php',
];

foreach ($views as $name => $path) {
    $fullPath = base_path($path);
    if (file_exists($fullPath)) {
        $size = filesize($fullPath);
        $content = file_get_contents($fullPath);
        $firstLine = strtok($content, "\n");
        echo "<p class='success'>✓ {$name} ({$size} bytes)</p>";
        echo "<p style='margin-right: 20px; font-size: 12px; color: #666;'>اولین خط: " . htmlspecialchars($firstLine) . "</p>";
    } else {
        echo "<p class='error'>✗ {$name} یافت نشد</p>";
    }
}
echo "</div>";

echo "<div class='box'>";
echo "<h2>تست رندر View</h2>";

try {
    $wallet = $user->wallet;
    if (!$wallet) {
        echo "<p class='error'>کیف پول یافت نشد</p>";
    } else {
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(20);
        
        if ($user->role === 'admin') {
            $viewName = 'wallet.admin';
        } elseif ($user->canSell()) {
            $viewName = 'wallet.seller';
        } else {
            $viewName = 'wallet.show';
        }
        
        echo "<p class='info'>در حال رندر view: <strong>{$viewName}</strong></p>";
        
        $html = view($viewName, compact('wallet', 'transactions'))->render();
        $length = strlen($html);
        
        echo "<p class='success'>✓ View رندر شد ({$length} کاراکتر)</p>";
        
        // Check for key content
        $checks = [
            'موجودی' => strpos($html, 'موجودی') !== false,
            'تراکنش' => strpos($html, 'تراکنش') !== false,
            'افزایش موجودی' => strpos($html, 'افزایش موجودی') !== false,
            'برداشت' => strpos($html, 'برداشت') !== false,
        ];
        
        echo "<h3>بررسی محتوا:</h3>";
        echo "<ul>";
        foreach ($checks as $item => $exists) {
            $icon = $exists ? '✓' : '✗';
            $class = $exists ? 'success' : 'error';
            echo "<li class='{$class}'>{$icon} {$item}</li>";
        }
        echo "</ul>";
        
        if ($length < 1000) {
            echo "<h3 class='error'>⚠️ محتوا خیلی کوچک است!</h3>";
            echo "<pre style='background: #f0f0f0; padding: 10px; overflow-x: auto;'>" . htmlspecialchars(substr($html, 0, 500)) . "</pre>";
        }
    }
} catch (\Exception $e) {
    echo "<p class='error'>خطا: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background: #f0f0f0; padding: 10px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</div>";

echo "<div class='box'>";
echo "<h2>لینک‌ها</h2>";
echo "<p><a href='/wallet' style='display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>رفتن به صفحه کیف پول</a></p>";
echo "</div>";

echo "</body></html>";
