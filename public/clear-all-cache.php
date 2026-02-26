<?php
// Clear all Laravel caches
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>پاک کردن کش‌ها...</h2>";

// Clear route cache
Artisan::call('route:clear');
echo "<p>✓ Route cache cleared</p>";

// Clear config cache
Artisan::call('config:clear');
echo "<p>✓ Config cache cleared</p>";

// Clear view cache
Artisan::call('view:clear');
echo "<p>✓ View cache cleared</p>";

// Clear application cache
Artisan::call('cache:clear');
echo "<p>✓ Application cache cleared</p>";

// Clear compiled classes
Artisan::call('clear-compiled');
echo "<p>✓ Compiled classes cleared</p>";

// Optimize
Artisan::call('optimize:clear');
echo "<p>✓ Optimization cache cleared</p>";

echo "<h3 style='color: green;'>✓ همه کش‌ها پاک شدند!</h3>";
echo "<p><a href='/'>بازگشت به صفحه اصلی</a></p>";
