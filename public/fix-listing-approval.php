<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Fix Listing Approval</title>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "h2{color:#2563eb;}</style></head><body>";

echo "<h1>تنظیم تایید دستی آگهی‌ها</h1>";

// Check current setting
$currentSetting = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "<div class='box'>";
echo "<h2>وضعیت فعلی</h2>";
echo "<p>تایید دستی آگهی‌ها: <strong>" . ($currentSetting ? 'فعال' : 'غیرفعال') . "</strong></p>";
echo "</div>";

// Enable approval
\App\Models\SiteSetting::set('require_listing_approval', true);
echo "<div class='box' style='background:#d1fae5;'>";
echo "<h2>✓ تایید دستی فعال شد</h2>";
echo "<p>از این به بعد تمام آگهی‌های جدید نیاز به تایید ادمین دارند.</p>";
echo "</div>";

// Check pending listings
$pendingCount = \App\Models\Listing::where('status', 'pending')->count();
echo "<div class='box'>";
echo "<h2>آگهی‌های در انتظار تایید</h2>";
echo "<p>تعداد: <strong>{$pendingCount}</strong></p>";
echo "</div>";

echo "</body></html>";
