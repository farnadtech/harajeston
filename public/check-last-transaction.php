<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\WalletTransaction;

$transaction = WalletTransaction::latest()->first();

if ($transaction) {
    echo "<pre style='direction:rtl;font-family:Tahoma;'>";
    echo "آخرین تراکنش:\n\n";
    echo "ID: {$transaction->id}\n";
    echo "وضعیت: {$transaction->status}\n";
    echo "مبلغ: " . number_format($transaction->amount) . " تومان\n";
    echo "مبلغ نهایی: " . number_format($transaction->final_amount) . " تومان\n";
    echo "درگاه: {$transaction->gateway}\n";
    echo "Transaction ID (token): " . ($transaction->transaction_id ?? 'NULL') . "\n";
    echo "Reference ID (کد پیگیری): " . ($transaction->reference_id ?? 'NULL') . "\n";
    echo "توضیحات: {$transaction->description}\n";
    echo "تاریخ: {$transaction->created_at}\n";
    echo "</pre>";
} else {
    echo "هیچ تراکنشی یافت نشد";
}
