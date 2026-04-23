<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

echo "<h1>رفع مشکل کیف پول الهه میرخرازی</h1>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:right;} th{background:#f2f2f2;}</style>";

// پیدا کردن کاربر الهه میرخرازی
$user = User::where('name', 'LIKE', '%الهه%')
    ->orWhere('name', 'LIKE', '%میرخرازی%')
    ->with('wallet')
    ->first();

if (!$user) {
    echo "<p class='error'>کاربر الهه میرخرازی یافت نشد!</p>";
    echo "<p>لیست کاربران:</p>";
    $users = User::where('role', '!=', 'admin')->get();
    foreach ($users as $u) {
        echo "<p>- {$u->name} (ID: {$u->id})</p>";
    }
    exit;
}

echo "<h2>کاربر: {$user->name}</h2>";
echo "<p>ID: {$user->id}</p>";
echo "<p>ایمیل: {$user->email}</p>";

$wallet = $user->wallet;
if (!$wallet) {
    echo "<p class='error'>کیف پول یافت نشد!</p>";
    exit;
}

echo "<hr>";
echo "<h3>وضعیت فعلی کیف پول</h3>";
echo "<p>موجودی: " . number_format($wallet->balance) . " تومان</p>";
echo "<p>موجودی بلاک شده: " . number_format($wallet->frozen) . " تومان</p>";

echo "<hr>";
echo "<h3>آخرین تراکنش‌ها</h3>";

$transactions = WalletTransaction::where('wallet_id', $wallet->id)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($transactions->isEmpty()) {
    echo "<p class='info'>هیچ تراکنشی یافت نشد.</p>";
} else {
    echo "<table>";
    echo "<tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>موجودی قبل</th><th>موجودی بعد</th><th>بلاک قبل</th><th>بلاک بعد</th><th>توضیحات</th></tr>";
    
    foreach ($transactions as $tx) {
        echo "<tr>";
        echo "<td>" . \App\Services\JalaliDateService::toJalali($tx->created_at, 'Y/m/d H:i') . "</td>";
        echo "<td>{$tx->type}</td>";
        echo "<td>" . number_format($tx->amount) . "</td>";
        echo "<td>" . number_format($tx->balance_before) . "</td>";
        echo "<td>" . number_format($tx->balance_after) . "</td>";
        echo "<td>" . number_format($tx->frozen_before) . "</td>";
        echo "<td>" . number_format($tx->frozen_after) . "</td>";
        echo "<td>{$tx->description}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr>";
echo "<h3>بررسی مشکل</h3>";

// پیدا کردن تراکنش‌های freeze_deposit که مشکل دارند
$problemTransactions = WalletTransaction::where('wallet_id', $wallet->id)
    ->where('type', 'freeze_deposit')
    ->where('description', 'LIKE', '%خرید فوری%')
    ->orderBy('created_at', 'desc')
    ->get();

if ($problemTransactions->isEmpty()) {
    echo "<p class='info'>هیچ تراکنش مشکل‌داری یافت نشد.</p>";
} else {
    echo "<p class='error'>تراکنش‌های مشکل‌دار یافت شد:</p>";
    echo "<table>";
    echo "<tr><th>تاریخ</th><th>مبلغ</th><th>توضیحات</th><th>عملیات</th></tr>";
    
    foreach ($problemTransactions as $tx) {
        echo "<tr>";
        echo "<td>" . \App\Services\JalaliDateService::toJalali($tx->created_at, 'Y/m/d H:i') . "</td>";
        echo "<td>" . number_format($tx->amount) . "</td>";
        echo "<td>{$tx->description}</td>";
        echo "<td><a href='?fix={$tx->id}'>برگشت این تراکنش</a></td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

// اگر درخواست برگشت تراکنش داده شده
if (isset($_GET['fix']) && !empty($_GET['fix'])) {
    $txId = (int)$_GET['fix'];
    
    echo "<hr>";
    echo "<h3>برگشت تراکنش #{$txId}</h3>";
    
    $tx = WalletTransaction::find($txId);
    
    if (!$tx) {
        echo "<p class='error'>تراکنش یافت نشد!</p>";
    } else if ($tx->wallet_id != $wallet->id) {
        echo "<p class='error'>این تراکنش متعلق به این کاربر نیست!</p>";
    } else {
        try {
            DB::transaction(function() use ($tx, $wallet) {
                // برگشت مبلغ از frozen به balance
                $wallet->frozen -= $tx->amount;
                $wallet->balance += $tx->amount;
                $wallet->save();
                
                // ثبت تراکنش برگشت
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'type' => 'refund',
                    'amount' => $tx->amount,
                    'final_amount' => $tx->amount,
                    'balance_before' => $wallet->balance - $tx->amount,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen + $tx->amount,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => $tx->reference_type,
                    'reference_id' => $tx->reference_id,
                    'status' => 'completed',
                    'description' => 'برگشت تراکنش اشتباه: ' . $tx->description,
                ]);
                
                echo "<p class='success'>✓ تراکنش با موفقیت برگشت داده شد!</p>";
                echo "<p>مبلغ " . number_format($tx->amount) . " تومان از بلاک به موجودی آزاد منتقل شد.</p>";
            });
            
            // رفرش صفحه
            echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
            
        } catch (\Exception $e) {
            echo "<p class='error'>خطا: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<h3>عملیات دستی</h3>";
echo "<form method='POST'>";
echo "<p><label>مبلغ برای آزادسازی از بلاک (تومان): <input type='number' name='unfreeze_amount' value='250000' step='1000'></label></p>";
echo "<p><button type='submit' name='action' value='unfreeze'>آزادسازی مبلغ</button></p>";
echo "</form>";

if (isset($_POST['action']) && $_POST['action'] === 'unfreeze') {
    $amount = (int)$_POST['unfreeze_amount'];
    
    if ($amount <= 0) {
        echo "<p class='error'>مبلغ نامعتبر است!</p>";
    } else if ($amount > $wallet->frozen) {
        echo "<p class='error'>مبلغ بیشتر از موجودی بلاک شده است!</p>";
    } else {
        try {
            DB::transaction(function() use ($amount, $wallet) {
                // آزادسازی مبلغ
                $wallet->frozen -= $amount;
                $wallet->balance += $amount;
                $wallet->save();
                
                // ثبت تراکنش
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user_id,
                    'type' => 'unfreeze_refund',
                    'amount' => $amount,
                    'final_amount' => $amount,
                    'balance_before' => $wallet->balance - $amount,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen + $amount,
                    'frozen_after' => $wallet->frozen,
                    'status' => 'completed',
                    'description' => 'آزادسازی دستی موجودی بلاک شده',
                ]);
                
                echo "<p class='success'>✓ مبلغ " . number_format($amount) . " تومان با موفقیت آزاد شد!</p>";
            });
            
            // رفرش صفحه
            echo "<script>setTimeout(function(){ window.location.href = window.location.pathname; }, 2000);</script>";
            
        } catch (\Exception $e) {
            echo "<p class='error'>خطا: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<p><a href='/haraj/public/test-buy-now-with-deposit.php'>بازگشت به صفحه تست</a></p>";
