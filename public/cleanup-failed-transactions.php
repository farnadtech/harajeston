<?php
/**
 * پاک کردن تراکنش‌های ناموفق و در انتظار
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\WalletTransaction;

echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";
echo "<h1>پاک کردن تراکنش‌های ناموفق</h1>";

// پیدا کردن تراکنش‌های ناموفق و در انتظار
$failedTransactions = WalletTransaction::whereIn('status', ['failed', 'pending'])->get();

echo "<p class='info'>تعداد تراکنش‌های ناموفق/در انتظار: {$failedTransactions->count()}</p>";

if ($failedTransactions->count() > 0) {
    echo "<h2>لیست تراکنش‌ها:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse;width:100%;'>";
    echo "<tr style='background:#f0f0f0;'>";
    echo "<th>ID</th><th>نوع</th><th>مبلغ</th><th>وضعیت</th><th>درگاه</th><th>تاریخ</th>";
    echo "</tr>";
    
    foreach ($failedTransactions as $transaction) {
        echo "<tr>";
        echo "<td>{$transaction->id}</td>";
        echo "<td>{$transaction->type}</td>";
        echo "<td>" . number_format($transaction->amount) . "</td>";
        echo "<td>{$transaction->status}</td>";
        echo "<td>" . ($transaction->gateway ?? '-') . "</td>";
        echo "<td>{$transaction->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>آیا می‌خواهید این تراکنش‌ها را حذف کنید؟</h2>";
    echo "<form method='POST'>";
    echo "<button type='submit' name='confirm' value='yes' style='background:red;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;font-size:16px;'>بله، حذف شوند</button>";
    echo "</form>";
    
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $count = WalletTransaction::whereIn('status', ['failed', 'pending'])->delete();
        echo "<p class='success'>✓ {$count} تراکنش با موفقیت حذف شد!</p>";
        echo "<p><a href='?'>بازگشت</a></p>";
    }
} else {
    echo "<p class='success'>✓ هیچ تراکنش ناموفقی یافت نشد!</p>";
}

echo "<hr>";
echo "<p><a href='/haraj/public/wallet'>بازگشت به کیف پول</a></p>";
