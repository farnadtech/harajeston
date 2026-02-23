<?php
echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head><meta charset='utf-8'><title>تست ساده</title>";
echo "<style>
body { font-family: Tahoma; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { color: #22c55e; font-weight: bold; }
.error { color: #ef4444; font-weight: bold; }
.warning { color: #f59e0b; font-weight: bold; }
a.btn { display: inline-block; padding: 12px 24px; margin: 10px 5px; border-radius: 6px; text-decoration: none; font-weight: bold; }
.btn-primary { background: #135bec; color: white; }
.btn-success { background: #22c55e; color: white; }
.btn-warning { background: #f59e0b; color: white; }
</style>";
echo "</head><body>";

echo "<h1>🔍 تست دسترسی به صفحه ساخت حراجی</h1>";

// بارگذاری Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

// بررسی وضعیت کاربر
echo "<div class='box'>";
echo "<h2>1️⃣ وضعیت کاربر</h2>";

if (!auth()->check()) {
    echo "<p class='error'>❌ شما لاگین نیستید</p>";
    echo "<a href='/haraj/public/login' class='btn btn-primary'>ورود به سیستم</a>";
} else {
    $user = auth()->user();
    echo "<p class='success'>✅ لاگین هستید</p>";
    echo "<p>👤 نام: <strong>{$user->name}</strong></p>";
    echo "<p>📧 ایمیل: <strong>{$user->email}</strong></p>";
    echo "<p>🎭 نقش: <strong>{$user->role}</strong></p>";
    echo "<p>📊 وضعیت فروشنده: <strong>" . ($user->seller_status ?? 'ندارد') . "</strong></p>";
    
    if ($user->role !== 'seller') {
        echo "<p class='error'>❌ شما نقش فروشنده ندارید</p>";
        echo "<a href='/haraj/public/become-seller' class='btn btn-warning'>درخواست فروشندگی</a>";
    } elseif ($user->seller_status !== 'active') {
        echo "<p class='warning'>⏳ حساب فروشندگی شما هنوز تایید نشده</p>";
    } else {
        echo "<p class='success'>✅ شما می‌توانید حراجی ایجاد کنید</p>";
    }
}
echo "</div>";

// بررسی روت
echo "<div class='box'>";
echo "<h2>2️⃣ بررسی روت</h2>";

try {
    $url = route('listings.create');
    echo "<p class='success'>✅ روت listings.create وجود دارد</p>";
    echo "<p>🔗 URL: <code>{$url}</code></p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ روت پیدا نشد: " . $e->getMessage() . "</p>";
}
echo "</div>";

// لینک‌های تست
echo "<div class='box'>";
echo "<h2>3️⃣ لینک‌های تست</h2>";
echo "<p>روی هر لینک کلیک کنید و ببینید کدام کار می‌کند:</p>";

$links = [
    '/haraj/public/listings/create' => 'مسیر کامل با public',
    '/listings/create' => 'مسیر بدون public',
];

if (auth()->check()) {
    try {
        $links[route('listings.create')] = 'با route helper';
    } catch (Exception $e) {}
}

foreach ($links as $url => $label) {
    echo "<a href='{$url}' class='btn btn-primary' target='_blank'>🔗 {$label}</a>";
}

echo "</div>";

// راهنمای عیب‌یابی
echo "<div class='box'>";
echo "<h2>4️⃣ راهنمای عیب‌یابی</h2>";
echo "<ul>";
echo "<li>اگر 404 می‌گیرید، ممکن است مشکل از <code>.htaccess</code> باشد</li>";
echo "<li>اگر به صفحه login هدایت می‌شوید، باید ابتدا لاگین کنید</li>";
echo "<li>اگر به dashboard هدایت می‌شوید، نقش یا وضعیت فروشنده ندارید</li>";
echo "<li>اگر صفحه سفید می‌بینید، خطای PHP وجود دارد - لاگ‌ها را چک کنید</li>";
echo "</ul>";
echo "</div>";

// اطلاعات سیستم
echo "<div class='box'>";
echo "<h2>5️⃣ اطلاعات سیستم</h2>";
echo "<p>📁 Document Root: <code>" . $_SERVER['DOCUMENT_ROOT'] . "</code></p>";
echo "<p>🌐 Request URI: <code>" . $_SERVER['REQUEST_URI'] . "</code></p>";
echo "<p>🔧 Script Name: <code>" . $_SERVER['SCRIPT_NAME'] . "</code></p>";
echo "<p>🏠 APP_URL: <code>" . config('app.url') . "</code></p>";
echo "</div>";

echo "</body></html>";
?>
