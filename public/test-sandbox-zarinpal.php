<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>تست Sandbox زرین‌پال</h2>";

// تست با sandbox
$config = [
    'merchant_id' => '00000000-0000-0000-0000-000000000000',
    'sandbox' => true,
];

echo "<h3>Config:</h3>";
echo "<pre>";
print_r($config);
echo "</pre>";

try {
    $larapay = app('larapay')->gateway('zarinpal', $config);
    
    echo "<h3>Gateway Object:</h3>";
    echo "<pre>";
    var_dump($larapay);
    echo "</pre>";
    
    // تست request
    $result = $larapay->request(
        'TEST-' . time(),
        10000, // 1000 تومان = 10000 ریال
        '',
        '09123456789',
        'http://localhost/test-callback',
        []
    );
    
    echo "<h3>Result:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if (isset($result['token'])) {
        $redirectUrl = 'https://sandbox.zarinpal.com/pg/StartPay/' . $result['token'];
        echo "<h3>Redirect URL:</h3>";
        echo "<a href='$redirectUrl' target='_blank'>$redirectUrl</a>";
    }
    
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>خطا:</h3>";
    echo "<pre style='color: red;'>";
    echo $e->getMessage();
    echo "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
