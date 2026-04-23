<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MelipayamakSetting;

echo "<h2>تست ملی پیامک با SOAP</h2>";
echo "<pre style='background:#f4f4f4;padding:15px;font-size:13px'>";

$settings = MelipayamakSetting::get();

if (!$settings || !$settings->isConfigured()) {
    echo "✗ تنظیمات ملی پیامک موجود نیست.\n";
    exit;
}

echo "✓ تنظیمات یافت شد:\n";
echo "  Username: {$settings->username}\n";
echo "  From: " . ($settings->from_number ?: '(خالی)') . "\n\n";

// تست SOAP
echo "=== تست SOAP ===\n";
try {
    $sms = \Melipayamak::sms('soap');
    
    // گرفتن اعتبار
    echo "درحال دریافت اعتبار...\n";
    $credit = $sms->getCredit();
    echo "✓ اعتبار: {$credit} ریال\n\n";
    
    // گرفتن شماره‌های فرستنده
    echo "درحال دریافت شماره‌های فرستنده...\n";
    $numbers = $sms->getNumbers();
    echo "✓ شماره‌های موجود:\n";
    print_r($numbers);
    echo "\n";
    
} catch (\Throwable $e) {
    echo "✗ خطا: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>
