<?php
// بررسی وضعیت کاربر برای ساخت حراجی
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='utf-8'>";
echo "<title>بررسی وضعیت کاربر</title>";
echo "<style>
body { font-family: Tahoma, Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; border-radius: 8px; margin: 10px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { color: #22c55e; }
.error { color: #ef4444; }
.warning { color: #f59e0b; }
h2 { color: #333; border-bottom: 2px solid #135bec; padding-bottom: 10px; }
</style>";
echo "</head>";
echo "<body>";

echo "<h2>🔍 بررسی وضعیت کاربر برای ساخت حراجی</h2>";

if (!auth()->check()) {
    echo "<div class='box error'>";
    echo "❌ شما لاگین نکرده‌اید<br>";
    echo "<a href='/login'>برای ورود کلیک کنید</a>";
    echo "</div>";
} else {
    $user = auth()->user();
    
    echo "<div class='box'>";
    echo "<h3>اطلاعات کاربر:</h3>";
    echo "نام: " . $user->name . "<br>";
    echo "ایمیل: " . $user->email . "<br>";
    echo "نقش: " . $user->role . "<br>";
    echo "وضعیت فروشنده: " . ($user->seller_status ?? 'ندارد') . "<br>";
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h3>بررسی دسترسی:</h3>";
    
    // چک نقش
    if ($user->role === 'seller') {
        echo "<div class='success'>✅ نقش فروشنده دارید</div>";
    } else {
        echo "<div class='error'>❌ نقش فروشنده ندارید (نقش فعلی: {$user->role})</div>";
        echo "<div class='warning'>💡 برای تبدیل شدن به فروشنده <a href='/become-seller'>اینجا کلیک کنید</a></div>";
    }
    
    // چک وضعیت فروشنده
    if ($user->seller_status === 'active') {
        echo "<div class='success'>✅ حساب فروشندگی شما فعال است</div>";
    } elseif ($user->seller_status === 'pending') {
        echo "<div class='warning'>⏳ حساب فروشندگی شما در انتظار تایید است</div>";
    } elseif ($user->seller_status === 'rejected') {
        echo "<div class='error'>❌ درخواست فروشندگی شما رد شده است</div>";
    } elseif ($user->seller_status === 'suspended') {
        echo "<div class='error'>❌ حساب فروشندگی شما معلق شده است</div>";
    } else {
        echo "<div class='error'>❌ وضعیت فروشنده ندارید</div>";
    }
    
    echo "</div>";
    
    echo "<div class='box'>";
    echo "<h3>لینک‌های مفید:</h3>";
    
    if ($user->role === 'seller' && $user->seller_status === 'active') {
        echo "<div class='success'>";
        echo "✅ شما می‌توانید حراجی ایجاد کنید<br><br>";
        echo "<a href='/listings/create' style='background: #135bec; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;'>➕ ایجاد حراجی جدید</a>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "⚠ شما نمی‌توانید حراجی ایجاد کنید<br>";
        echo "ابتدا باید فروشنده شوید و حساب شما تایید شود.";
        echo "</div>";
    }
    
    echo "<br><br>";
    echo "<a href='/dashboard'>🏠 داشبورد</a> | ";
    echo "<a href='/my-listings'>📋 حراجی‌های من</a> | ";
    echo "<a href='/'>🏪 صفحه اصلی</a>";
    echo "</div>";
}

echo "</body>";
echo "</html>";
?>
