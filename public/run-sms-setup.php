<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1 style='font-family:sans-serif'>راه‌اندازی سیستم پیامک OTP</h1>";
echo "<pre style='background:#f4f4f4;padding:20px;font-size:14px'>";

// 1. Migration
echo "=== اجرای Migration ===\n";
try {
    $kernel->call('migrate', [
        '--path'  => 'database/migrations/2026_04_20_000001_create_sms_gateways_table.php',
        '--force' => true,
    ]);
    echo "✓ جداول melipayamak_settings و otp_codes ساخته شدند\n\n";
} catch (Exception $e) {
    echo "✗ خطا در migration: " . $e->getMessage() . "\n\n";
}

// 2. Seeder
echo "=== اجرای Seeder ===\n";
try {
    $kernel->call('db:seed', [
        '--class' => 'SmsGatewaySeeder',
        '--force' => true,
    ]);
    echo "✓ درگاه‌های پیامکی پیش‌فرض اضافه شدند\n\n";
} catch (Exception $e) {
    echo "✗ خطا در seeder: " . $e->getMessage() . "\n\n";
}

// 3. Cache clear
echo "=== پاک‌سازی Cache ===\n";
try {
    $kernel->call('config:clear');
    $kernel->call('route:clear');
    $kernel->call('view:clear');
    echo "✓ Cache پاک شد\n\n";
} catch (Exception $e) {
    echo "✗ خطا در cache clear: " . $e->getMessage() . "\n\n";
}

// 4. نمایش وضعیت
echo "=== وضعیت تنظیمات ===\n";
try {
    $setting = \App\Models\MelipayamakSetting::get();
    if ($setting) {
        echo "✓ تنظیمات موجود است | username: {$setting->username}\n";
    } else {
        echo "○ هنوز تنظیم نشده — از پنل ادمین اقدام کنید\n";
    }
} catch (Exception $e) {
    echo "✗ خطا: " . $e->getMessage() . "\n";
}

echo "\n=== راه‌اندازی کامل شد ===\n";
echo "</pre>";
echo '<p style="font-family:sans-serif">برای تنظیم درگاه پیامک به <a href="/haraj/public/admin/sms-gateways">پنل ادمین</a> بروید.</p>';
?>
