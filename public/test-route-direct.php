<?php
// تست مستقیم روت Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head><meta charset='utf-8'><title>تست روت</title>";
echo "<style>body{font-family:Tahoma;padding:20px;background:#f5f5f5;}.box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}.success{color:#22c55e;}.error{color:#ef4444;}</style>";
echo "</head><body>";

echo "<h2>🔍 تست روت listings.create</h2>";

// تست 1: بررسی روت‌ها
echo "<div class='box'>";
echo "<h3>1. بررسی روت‌های Laravel</h3>";
try {
    $routes = Route::getRoutes();
    $found = false;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'listings.create') {
            $found = true;
            echo "<div class='success'>✅ روت listings.create پیدا شد</div>";
            echo "URI: " . $route->uri() . "<br>";
            echo "Method: " . implode(', ', $route->methods()) . "<br>";
            echo "Action: " . $route->getActionName() . "<br>";
            
            $middleware = $route->middleware();
            echo "Middleware: " . (empty($middleware) ? 'ندارد' : implode(', ', $middleware)) . "<br>";
            break;
        }
    }
    
    if (!$found) {
        echo "<div class='error'>❌ روت listings.create پیدا نشد</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ خطا: " . $e->getMessage() . "</div>";
}
echo "</div>";

// تست 2: بررسی کاربر
echo "<div class='box'>";
echo "<h3>2. بررسی وضعیت کاربر</h3>";
if (auth()->check()) {
    $user = auth()->user();
    echo "<div class='success'>✅ لاگین هستید</div>";
    echo "نام: " . $user->name . "<br>";
    echo "نقش: " . $user->role . "<br>";
    echo "وضعیت فروشنده: " . ($user->seller_status ?? 'ندارد') . "<br>";
    
    if ($user->role === 'seller' && $user->seller_status === 'active') {
        echo "<div class='success'>✅ دسترسی کامل دارید</div>";
    } else {
        echo "<div class='error'>❌ دسترسی ندارید</div>";
    }
} else {
    echo "<div class='error'>❌ لاگین نیستید</div>";
}
echo "</div>";

// تست 3: تست URL
echo "<div class='box'>";
echo "<h3>3. تست URL ها</h3>";
try {
    echo "URL با route helper: <code>" . route('listings.create') . "</code><br>";
    echo "URL با url helper: <code>" . url('/listings/create') . "</code><br>";
} catch (Exception $e) {
    echo "<div class='error'>خطا: " . $e->getMessage() . "</div>";
}
echo "</div>";

// تست 4: لینک‌های مستقیم
echo "<div class='box'>";
echo "<h3>4. لینک‌های تست</h3>";
echo "<a href='/listings/create' style='background:#135bec;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;display:inline-block;margin:5px;'>تست با /listings/create</a><br><br>";
echo "<a href='/haraj/public/listings/create' style='background:#f97316;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;display:inline-block;margin:5px;'>تست با /haraj/public/listings/create</a><br><br>";

try {
    echo "<a href='" . route('listings.create') . "' style='background:#22c55e;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;display:inline-block;margin:5px;'>تست با route helper</a>";
} catch (Exception $e) {
    echo "<div class='error'>نمی‌توان route helper را استفاده کرد</div>";
}
echo "</div>";

echo "</body></html>";
?>
