<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MelipayamakSetting;

echo "<pre style='font-size:13px;padding:15px;background:#f4f4f4'>";

$settings = MelipayamakSetting::get();
$username = $settings->username;
$password = $settings->getAuthPassword(); // ApiKey یا password

echo "Username: $username\n";
echo "Auth (ApiKey/Pass): " . substr($password, 0, 8) . "...\n\n";

$phone = $_GET['phone'] ?? '09356963201';
$code  = '123456';

// ─── تست 1: SOAP SendOtp ─────────────────────────────────────
echo "=== تست SOAP SendOtp ===\n";
try {
    ini_set("soap.wsdl_cache_enabled", "0");
    $client = new SoapClient(
        'http://api.payamak-panel.com/post/Send.asmx?wsdl',
        ['exceptions' => true, 'trace' => true]
    );

    // لیست متدهای موجود
    echo "متدهای موجود:\n";
    $functions = $client->__getFunctions();
    foreach ($functions as $f) {
        if (stripos($f, 'otp') !== false || stripos($f, 'send') !== false) {
            echo "  - $f\n";
        }
    }
    echo "\n";

    // فراخوانی SendOtp
    $result = $client->SendOtp([
        'username' => $username,
        'password' => $password,
        'to'       => $phone,
        'from'     => '',
        'code'     => (int)$code,
    ]);

    echo "نتیجه SendOtp:\n";
    print_r($result);

} catch (\Throwable $e) {
    echo "✗ SOAP خطا: " . $e->getMessage() . "\n";
}

echo "\n";

// ─── تست 2: SOAP SendOtp2 (با code به صورت string) ──────────
echo "=== تست SOAP SendOtp2 ===\n";
try {
    $client2 = new SoapClient(
        'http://api.payamak-panel.com/post/Send.asmx?wsdl',
        ['exceptions' => true]
    );

    $result2 = $client2->SendOtp2([
        'username' => $username,
        'password' => $password,
        'to'       => $phone,
        'from'     => '',
        'code'     => $code, // string
    ]);

    echo "نتیجه SendOtp2:\n";
    print_r($result2);

} catch (\Throwable $e) {
    echo "✗ SendOtp2 خطا: " . $e->getMessage() . "\n";
}

echo "\n";

// ─── تست 3: REST SendOtp ─────────────────────────────────────
echo "=== تست REST SendOtp ===\n";
$data = [
    'username' => $username,
    'password' => $password,
    'to'       => $phone,
    'from'     => '',
    'code'     => $code,
];

$ch = curl_init('https://rest.payamak-panel.com/api/SendSMS/SendOtp');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 15,
]);
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

echo "درخواست ارسالی:\n" . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
echo $err ? "✗ curl خطا: $err\n" : "پاسخ: $response\n";

echo "</pre>";
?>
