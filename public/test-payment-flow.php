<?php
/**
 * Test Payment Gateway Flow
 * This script tests the payment gateway configuration and API
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentGateway;
use App\Models\User;
use App\Services\PaymentGatewayService;

echo "<h1>تست سیستم درگاه پرداخت</h1>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// 1. Check active gateways
echo "<h2>1. درگاه‌های فعال</h2>";
$gateways = PaymentGateway::active()->get();
if ($gateways->count() > 0) {
    echo "<p class='success'>✓ تعداد درگاه‌های فعال: {$gateways->count()}</p>";
    echo "<ul>";
    foreach ($gateways as $gateway) {
        echo "<li>{$gateway->display_name} ({$gateway->name})";
        $credentials = json_decode($gateway->credentials, true);
        echo " - تنظیمات: " . (empty($credentials) ? "خالی" : "تنظیم شده");
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>✗ هیچ درگاه فعالی یافت نشد</p>";
}

// 2. Check Larapay service
echo "<h2>2. سرویس Larapay</h2>";
try {
    $larapay = app('larapay');
    echo "<p class='success'>✓ سرویس Larapay بارگذاری شد</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ خطا در بارگذاری Larapay: {$e->getMessage()}</p>";
}

// 3. Check PaymentGatewayService
echo "<h2>3. سرویس PaymentGatewayService</h2>";
try {
    $service = app(PaymentGatewayService::class);
    $activeGateways = $service->getActiveGateways();
    echo "<p class='success'>✓ سرویس PaymentGatewayService کار می‌کند</p>";
    echo "<p class='info'>تعداد درگاه‌های فعال: {$activeGateways->count()}</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ خطا در PaymentGatewayService: {$e->getMessage()}</p>";
}

// 4. Test gateway configuration
echo "<h2>4. تست تنظیمات درگاه‌ها</h2>";
foreach ($gateways as $gateway) {
    echo "<h3>{$gateway->display_name}</h3>";
    
    $credentials = json_decode($gateway->credentials, true);
    
    switch ($gateway->name) {
        case 'zarinpal':
            $required = ['merchant_id'];
            break;
        case 'zibal':
            $required = ['merchant_id'];
            break;
        case 'vandar':
            $required = ['api_key'];
            break;
        case 'payping':
            $required = ['api_key'];
            break;
        default:
            $required = [];
    }
    
    $missing = [];
    foreach ($required as $field) {
        if (empty($credentials[$field])) {
            $missing[] = $field;
        }
    }
    
    if (empty($missing)) {
        echo "<p class='success'>✓ تمام فیلدهای مورد نیاز تنظیم شده‌اند</p>";
    } else {
        echo "<p class='error'>✗ فیلدهای خالی: " . implode(', ', $missing) . "</p>";
    }
}

// 5. Check routes
echo "<h2>5. مسیرهای پرداخت</h2>";
try {
    $addFundsRoute = route('wallet.add-funds');
    $callbackRoute = route('wallet.payment.callback');
    echo "<p class='success'>✓ مسیر افزایش موجودی: {$addFundsRoute}</p>";
    echo "<p class='success'>✓ مسیر بازگشت پرداخت: {$callbackRoute}</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ خطا در مسیرها: {$e->getMessage()}</p>";
}

// 6. Check test user
echo "<h2>6. کاربر تست</h2>";
$testUser = User::where('email', 'farnad25@gmail.com')->first();
if ($testUser) {
    echo "<p class='success'>✓ کاربر تست یافت شد: {$testUser->name}</p>";
    echo "<p class='info'>موجودی کیف پول: " . number_format($testUser->wallet->balance) . " تومان</p>";
    echo "<p class='info'>شماره تلفن: " . ($testUser->phone ?? 'تنظیم نشده') . "</p>";
} else {
    echo "<p class='error'>✗ کاربر تست یافت نشد</p>";
}

echo "<hr>";
echo "<h2>نتیجه</h2>";
echo "<p>برای تست کامل سیستم:</p>";
echo "<ol>";
echo "<li>وارد پنل ادمین شوید: <a href='/haraj/public/admin/payment-gateways'>/admin/payment-gateways</a></li>";
echo "<li>یک درگاه را فعال کنید و اطلاعات آن را وارد کنید</li>";
echo "<li>به صفحه کیف پول بروید: <a href='/haraj/public/wallet'>/wallet</a></li>";
echo "<li>مبلغی را برای شارژ وارد کنید و درگاه را انتخاب کنید</li>";
echo "</ol>";
