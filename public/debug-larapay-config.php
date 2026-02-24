<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Debug Larapay Config</h2>";

// چک کردن gateway از دیتابیس
$gateway = \App\Models\PaymentGateway::where('name', 'zarinpal')->first();

echo "<h3>Gateway از دیتابیس:</h3>";
echo "<pre>";
print_r([
    'name' => $gateway->name,
    'is_active' => $gateway->is_active,
    'sandbox_mode' => $gateway->sandbox_mode,
    'credentials' => $gateway->credentials,
]);
echo "</pre>";

// تست config که به larapay ارسال میشه
$service = new \App\Services\PaymentGatewayService();

// استفاده از reflection برای دسترسی به متد private
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('getGatewayConfig');
$method->setAccessible(true);

$config = $method->invoke($service, $gateway);

echo "<h3>Config که به Larapay ارسال میشه:</h3>";
echo "<pre>";
print_r($config);
echo "</pre>";

// تست مستقیم با larapay
echo "<h3>تست مستقیم با Larapay:</h3>";

try {
    $larapay = app('larapay')->gateway('zarinpal', $config);
    echo "<p style='color: green;'>✓ Gateway object ساخته شد</p>";
    
    // تست request
    $result = $larapay->request(
        'TEST-' . time(),
        10000,
        '',
        '09123456789',
        'http://localhost/test',
        []
    );
    
    echo "<p style='color: green;'>✓ Request موفق بود</p>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>✗ خطا: " . $e->getMessage() . "</p>";
    echo "<pre style='color: red;'>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
