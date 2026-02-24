<?php
/**
 * تست سریع درگاه‌های پرداخت
 * 
 * این فایل برای تست سریع نصب و پیکربندی درگاه‌های پرداخت است
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تست درگاه‌های پرداخت</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">تست درگاه‌های پرداخت</h1>
            <p class="text-gray-600 mb-6">بررسی وضعیت نصب و پیکربندی درگاه‌های پرداخت</p>

            <?php
            try {
                // بررسی نصب پکیج
                echo '<div class="mb-6">';
                echo '<h2 class="text-xl font-bold text-gray-800 mb-3">1. بررسی نصب پکیج Larapay</h2>';
                
                if (class_exists('Farayaz\Larapay\Facades\Larapay')) {
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">';
                    echo '✅ پکیج Larapay با موفقیت نصب شده است';
                    echo '</div>';
                } else {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
                    echo '❌ پکیج Larapay نصب نشده است';
                    echo '</div>';
                }
                echo '</div>';

                // بررسی جداول
                echo '<div class="mb-6">';
                echo '<h2 class="text-xl font-bold text-gray-800 mb-3">2. بررسی جداول دیتابیس</h2>';
                
                $tables = [
                    'payment_gateways' => 'جدول درگاه‌های پرداخت',
                    'wallet_transactions' => 'جدول تراکنش‌های کیف پول'
                ];
                
                foreach ($tables as $table => $label) {
                    $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
                    if ($exists) {
                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-2">';
                        echo "✅ {$label} ({$table}) موجود است";
                        echo '</div>';
                    } else {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2">';
                        echo "❌ {$label} ({$table}) موجود نیست";
                        echo '</div>';
                    }
                }
                echo '</div>';

                // بررسی درگاه‌ها
                echo '<div class="mb-6">';
                echo '<h2 class="text-xl font-bold text-gray-800 mb-3">3. لیست درگاه‌های پرداخت</h2>';
                
                $gateways = \App\Models\PaymentGateway::orderBy('sort_order')->get();
                
                if ($gateways->count() > 0) {
                    echo '<div class="overflow-x-auto">';
                    echo '<table class="min-w-full bg-white border border-gray-300">';
                    echo '<thead class="bg-gray-100">';
                    echo '<tr>';
                    echo '<th class="px-4 py-2 border">نام</th>';
                    echo '<th class="px-4 py-2 border">نام نمایشی</th>';
                    echo '<th class="px-4 py-2 border">وضعیت</th>';
                    echo '<th class="px-4 py-2 border">تنظیمات</th>';
                    echo '<th class="px-4 py-2 border">ترتیب</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($gateways as $gateway) {
                        $statusClass = $gateway->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                        $statusText = $gateway->is_active ? 'فعال' : 'غیرفعال';
                        
                        $hasCredentials = !empty(array_filter($gateway->credentials ?? []));
                        $credClass = $hasCredentials ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800';
                        $credText = $hasCredentials ? 'تنظیم شده' : 'نیاز به تنظیم';
                        
                        echo '<tr>';
                        echo "<td class='px-4 py-2 border'>{$gateway->name}</td>";
                        echo "<td class='px-4 py-2 border'>{$gateway->display_name}</td>";
                        echo "<td class='px-4 py-2 border'><span class='px-2 py-1 rounded {$statusClass}'>{$statusText}</span></td>";
                        echo "<td class='px-4 py-2 border'><span class='px-2 py-1 rounded {$credClass}'>{$credText}</span></td>";
                        echo "<td class='px-4 py-2 border text-center'>{$gateway->sort_order}</td>";
                        echo '</tr>';
                    }
                    
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">';
                    echo '⚠️ هیچ درگاه پرداختی یافت نشد. لطفا Seeder را اجرا کنید:';
                    echo '<br><code class="bg-yellow-200 px-2 py-1 rounded mt-2 inline-block">php artisan db:seed --class=PaymentGatewaySeeder</code>';
                    echo '</div>';
                }
                echo '</div>';

                // بررسی Routes
                echo '<div class="mb-6">';
                echo '<h2 class="text-xl font-bold text-gray-800 mb-3">4. بررسی Routes</h2>';
                
                $routes = [
                    'admin.payment-gateways.index' => 'لیست درگاه‌ها (ادمین)',
                    'admin.payment-gateways.edit' => 'ویرایش درگاه (ادمین)',
                    'wallet.add-funds' => 'شارژ کیف پول',
                    'wallet.payment.callback' => 'بازگشت از درگاه پرداخت'
                ];
                
                foreach ($routes as $routeName => $label) {
                    try {
                        $url = route($routeName, ['gateway' => 1], false);
                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-2">';
                        echo "✅ {$label}: <code class='bg-green-200 px-2 py-1 rounded'>{$url}</code>";
                        echo '</div>';
                    } catch (\Exception $e) {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-2">';
                        echo "❌ {$label}: مسیر یافت نشد";
                        echo '</div>';
                    }
                }
                echo '</div>';

                // دستورالعمل‌ها
                echo '<div class="mb-6">';
                echo '<h2 class="text-xl font-bold text-gray-800 mb-3">5. مراحل بعدی</h2>';
                echo '<div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">';
                echo '<ol class="list-decimal list-inside space-y-2">';
                echo '<li>وارد پنل ادمین شوید</li>';
                echo '<li>به بخش "درگاه‌های پرداخت" بروید</li>';
                echo '<li>درگاه مورد نظر را ویرایش کنید</li>';
                echo '<li>اطلاعات احراز هویت را وارد کنید</li>';
                echo '<li>درگاه را فعال کنید</li>';
                echo '<li>از بخش کیف پول تست کنید</li>';
                echo '</ol>';
                echo '</div>';
                echo '</div>';

            } catch (\Exception $e) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
                echo '<strong>خطا:</strong> ' . $e->getMessage();
                echo '</div>';
            }
            ?>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-3">لینک‌های مفید</h2>
            <div class="space-y-2">
                <a href="/admin/payment-gateways" class="block text-blue-600 hover:text-blue-800">
                    → پنل مدیریت درگاه‌های پرداخت
                </a>
                <a href="/wallet" class="block text-blue-600 hover:text-blue-800">
                    → کیف پول
                </a>
                <a href="https://github.com/farayaz/larapay" target="_blank" class="block text-blue-600 hover:text-blue-800">
                    → مستندات Larapay
                </a>
            </div>
        </div>
    </div>
</body>
</html>
