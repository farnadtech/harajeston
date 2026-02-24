<?php
// Debug wallet page
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>دیباگ کیف پول</title>";
echo "<style>
body { font-family: Tahoma, Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 دیباگ صفحه کیف پول</h1>";

try {
    // Check authentication
    echo "<div class='box'>";
    echo "<h2>1️⃣ بررسی احراز هویت</h2>";
    
    if (!auth()->check()) {
        echo "<p class='error'>❌ کاربر وارد نشده است</p>";
        echo "<p><a href='/login'>ورود به سیستم</a></p>";
        echo "</div></body></html>";
        exit;
    }
    
    $user = auth()->user();
    echo "<p class='success'>✅ کاربر وارد شده: {$user->name}</p>";
    echo "<p>ID: {$user->id} | Email: {$user->email} | Role: {$user->role}</p>";
    echo "</div>";
    
    // Check wallet
    echo "<div class='box'>";
    echo "<h2>2️⃣ بررسی کیف پول</h2>";
    
    $wallet = $user->wallet;
    if (!$wallet) {
        echo "<p class='error'>❌ کیف پول یافت نشد</p>";
        echo "<p>در حال ایجاد کیف پول...</p>";
        
        $wallet = \App\Models\Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'frozen' => 0,
        ]);
        
        echo "<p class='success'>✅ کیف پول ایجاد شد</p>";
    } else {
        echo "<p class='success'>✅ کیف پول موجود است</p>";
    }
    
    echo "<pre>";
    echo "ID: {$wallet->id}\n";
    echo "User ID: {$wallet->user_id}\n";
    echo "Balance: " . number_format($wallet->balance) . " تومان\n";
    echo "Frozen: " . number_format($wallet->frozen) . " تومان\n";
    echo "</pre>";
    echo "</div>";
    
    // Check transactions
    echo "<div class='box'>";
    echo "<h2>3️⃣ بررسی تراکنش‌ها</h2>";
    
    $transactionsCount = $wallet->transactions()->count();
    echo "<p>تعداد کل تراکنش‌ها: <strong>{$transactionsCount}</strong></p>";
    
    if ($transactionsCount > 0) {
        $recentTransactions = $wallet->transactions()->orderBy('created_at', 'desc')->limit(5)->get();
        echo "<p class='success'>✅ تراکنش‌ها یافت شدند</p>";
        echo "<table border='1' cellpadding='5' style='width:100%; border-collapse: collapse;'>";
        echo "<tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>توضیحات</th></tr>";
        foreach ($recentTransactions as $t) {
            echo "<tr>";
            echo "<td>" . $t->created_at->format('Y-m-d H:i') . "</td>";
            echo "<td>{$t->type}</td>";
            echo "<td>" . number_format($t->amount) . "</td>";
            echo "<td>{$t->description}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ هیچ تراکنشی وجود ندارد</p>";
        echo "<p>ایجاد یک تراکنش نمونه...</p>";
        
        \App\Models\WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'deposit',
            'amount' => 100000,
            'description' => 'شارژ اولیه تستی',
            'before_balance' => $wallet->balance,
            'after_balance' => $wallet->balance + 100000,
        ]);
        
        $wallet->balance += 100000;
        $wallet->save();
        
        echo "<p class='success'>✅ تراکنش نمونه ایجاد شد</p>";
    }
    echo "</div>";
    
    // Check view files
    echo "<div class='box'>";
    echo "<h2>4️⃣ بررسی فایل‌های View</h2>";
    
    $viewFiles = [
        'resources/views/wallet/show.blade.php',
        'resources/views/wallet/partials/content.blade.php',
        'resources/views/layouts/app.blade.php',
    ];
    
    foreach ($viewFiles as $file) {
        $fullPath = base_path($file);
        if (file_exists($fullPath)) {
            $size = filesize($fullPath);
            echo "<p class='success'>✅ {$file} ({$size} bytes)</p>";
        } else {
            echo "<p class='error'>❌ {$file} یافت نشد</p>";
        }
    }
    echo "</div>";
    
    // Check route
    echo "<div class='box'>";
    echo "<h2>5️⃣ بررسی Route</h2>";
    
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $walletRoute = $routes->getByName('wallet.show');
    
    if ($walletRoute) {
        echo "<p class='success'>✅ Route 'wallet.show' موجود است</p>";
        echo "<pre>";
        echo "URI: " . $walletRoute->uri() . "\n";
        echo "Method: " . implode(', ', $walletRoute->methods()) . "\n";
        echo "Action: " . $walletRoute->getActionName() . "\n";
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ Route 'wallet.show' یافت نشد</p>";
    }
    echo "</div>";
    
    // Try to render the view
    echo "<div class='box'>";
    echo "<h2>6️⃣ تست رندر View</h2>";
    
    try {
        $transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(20);
        
        echo "<p>در حال تست رندر view...</p>";
        
        // Check if partial exists and has content
        $partialPath = resource_path('views/wallet/partials/content.blade.php');
        if (file_exists($partialPath)) {
            $partialContent = file_get_contents($partialPath);
            $partialSize = strlen($partialContent);
            echo "<p class='success'>✅ Partial file موجود است ({$partialSize} کاراکتر)</p>";
            
            if ($partialSize < 100) {
                echo "<p class='error'>❌ فایل partial خیلی کوچک است یا خالی است!</p>";
            }
        } else {
            echo "<p class='error'>❌ فایل partial یافت نشد!</p>";
        }
        
        // Try to compile the view
        $viewContent = view('wallet.show', compact('wallet', 'transactions'))->render();
        $contentLength = strlen($viewContent);
        
        echo "<p class='success'>✅ View با موفقیت رندر شد ({$contentLength} کاراکتر)</p>";
        
        if ($contentLength < 500) {
            echo "<p class='error'>❌ محتوای رندر شده خیلی کوچک است!</p>";
            echo "<pre>" . htmlspecialchars(substr($viewContent, 0, 500)) . "</pre>";
        }
        
    } catch (\Exception $e) {
        echo "<p class='error'>❌ خطا در رندر view:</p>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    echo "</div>";
    
    // Final link
    echo "<div class='box'>";
    echo "<h2>✅ همه چیز آماده است</h2>";
    echo "<p><a href='/wallet' style='display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>رفتن به صفحه کیف پول</a></p>";
    echo "</div>";
    
} catch (\Exception $e) {
    echo "<div class='box'>";
    echo "<h2 class='error'>❌ خطای کلی</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body>";
echo "</html>";
