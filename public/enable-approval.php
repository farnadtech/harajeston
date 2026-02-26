<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre style='direction:rtl;font-family:Tahoma;'>";

echo "=== فعال‌سازی تایید دستی آگهی‌ها ===\n\n";

// Check current
$current = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "وضعیت فعلی: " . ($current ? 'فعال' : 'غیرفعال') . "\n\n";

// Enable it
\App\Models\SiteSetting::set('require_listing_approval', true, 'boolean');
\Illuminate\Support\Facades\Cache::forget('site_setting_require_listing_approval');

// Verify
$new = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "وضعیت جدید: " . ($new ? 'فعال ✓' : 'غیرفعال ✗') . "\n\n";

if ($new) {
    echo "✓ تایید دستی فعال شد\n";
    echo "✓ از این به بعد آگهی‌های جدید باید توسط ادمین تایید شوند\n";
    echo "✓ ویرایش آگهی‌ها هم نیاز به تایید مجدد دارد\n";
} else {
    echo "✗ خطا در فعال‌سازی!\n";
}

echo "\n<a href='/admin/settings'>رفتن به تنظیمات</a>";
echo "</pre>";
