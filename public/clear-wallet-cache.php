<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

Artisan::call('view:clear');
Artisan::call('cache:clear');

echo "<!DOCTYPE html><html dir='rtl'><head><meta charset='UTF-8'><title>پاک کردن کش</title></head><body style='font-family:Tahoma;padding:20px;'>";
echo "<h1>✅ کش پاک شد</h1>";
echo "<p><a href='/wallet'>رفتن به صفحه کیف پول</a></p>";
echo "</body></html>";
