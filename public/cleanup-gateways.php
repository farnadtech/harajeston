<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "حذف درگاه‌های قدیمی...\n";

$deleted = DB::table('payment_gateways')
    ->whereNotIn('name', ['zarinpal', 'zibal', 'vandar', 'payping'])
    ->delete();

echo "تعداد درگاه‌های حذف شده: {$deleted}\n\n";

echo "لیست درگاه‌های باقی‌مانده:\n";
$gateways = App\Models\PaymentGateway::all();
foreach ($gateways as $gateway) {
    echo "- {$gateway->display_name} ({$gateway->name})\n";
}

echo "\nتمام!\n";
