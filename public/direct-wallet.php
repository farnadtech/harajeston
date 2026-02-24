<?php
// Direct wallet page render
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Check auth
if (!auth()->check()) {
    header('Location: /login');
    exit;
}

$user = auth()->user();
$wallet = $user->wallet;

// Create wallet if not exists
if (!$wallet) {
    $wallet = \App\Models\Wallet::create([
        'user_id' => $user->id,
        'balance' => 0,
        'frozen' => 0,
    ]);
}

// Get transactions
$transactions = $wallet->transactions()->orderBy('created_at', 'desc')->paginate(20);

// Render the view
try {
    echo view('wallet.show', compact('wallet', 'transactions'))->render();
} catch (\Exception $e) {
    echo "<!DOCTYPE html>";
    echo "<html dir='rtl' lang='fa'>";
    echo "<head><meta charset='UTF-8'><title>خطا</title></head>";
    echo "<body style='font-family: Tahoma; padding: 20px;'>";
    echo "<h1 style='color: red;'>خطا در نمایش صفحه کیف پول</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background: #f0f0f0; padding: 10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    
    // Check file existence
    echo "<h3>بررسی فایل‌ها:</h3>";
    $files = [
        'resources/views/wallet/show.blade.php',
        'resources/views/wallet/partials/content.blade.php',
        'resources/views/layouts/app.blade.php',
    ];
    
    foreach ($files as $file) {
        $path = base_path($file);
        if (file_exists($path)) {
            echo "<p style='color: green;'>✓ {$file} موجود است (" . filesize($path) . " bytes)</p>";
        } else {
            echo "<p style='color: red;'>✗ {$file} یافت نشد</p>";
        }
    }
    
    echo "</body></html>";
}
