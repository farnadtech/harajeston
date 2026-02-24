<?php
// Test wallet access
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Start session
session_start();

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>تست کیف پول</title>";
echo "<style>body { font-family: Tahoma, Arial; padding: 20px; } .success { color: green; } .error { color: red; }</style>";
echo "</head>";
echo "<body>";

echo "<h1>تست دسترسی به کیف پول</h1>";

// Check if user is logged in
if (auth()->check()) {
    $user = auth()->user();
    echo "<p class='success'>✓ کاربر وارد شده است: {$user->name}</p>";
    echo "<p>ایمیل: {$user->email}</p>";
    echo "<p>نقش: {$user->role}</p>";
    
    // Check wallet
    if ($user->wallet) {
        echo "<p class='success'>✓ کیف پول یافت شد</p>";
        echo "<p>موجودی: " . number_format($user->wallet->balance) . " تومان</p>";
        echo "<p>مسدود شده: " . number_format($user->wallet->frozen) . " تومان</p>";
        
        // Check transactions
        $transactionCount = $user->wallet->transactions()->count();
        echo "<p>تعداد تراکنش‌ها: {$transactionCount}</p>";
        
        echo "<hr>";
        echo "<p><a href='/wallet'>رفتن به صفحه کیف پول</a></p>";
    } else {
        echo "<p class='error'>✗ کیف پول یافت نشد!</p>";
        echo "<p>در حال ایجاد کیف پول...</p>";
        
        try {
            $wallet = \App\Models\Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'frozen' => 0,
            ]);
            echo "<p class='success'>✓ کیف پول ایجاد شد</p>";
            echo "<p><a href='/wallet'>رفتن به صفحه کیف پول</a></p>";
        } catch (Exception $e) {
            echo "<p class='error'>خطا در ایجاد کیف پول: " . $e->getMessage() . "</p>";
        }
    }
} else {
    echo "<p class='error'>✗ کاربر وارد نشده است</p>";
    echo "<p><a href='/login'>ورود به سیستم</a></p>";
}

echo "</body>";
echo "</html>";
