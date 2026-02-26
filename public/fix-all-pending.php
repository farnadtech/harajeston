<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre style='direction:rtl;font-family:Tahoma;'>";

echo "=== رفع مشکل آگهی‌های بدون تایید ===\n\n";

// 1. Enable approval
echo "1. فعال‌سازی تایید دستی...\n";
\App\Models\SiteSetting::set('require_listing_approval', true, 'boolean');
\Illuminate\Support\Facades\Cache::forget('site_setting_require_listing_approval');

$enabled = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "   وضعیت: " . ($enabled ? 'فعال ✓' : 'غیرفعال ✗') . "\n\n";

// 2. Find listings that should be pending
echo "2. بررسی آگهی‌های فعال که باید pending باشند...\n";
$activeListings = \App\Models\Listing::where('status', 'active')
    ->where('created_at', '>', \Carbon\Carbon::now()->subHours(24))
    ->get();

echo "   تعداد آگهی‌های فعال (24 ساعت اخیر): {$activeListings->count()}\n\n";

if ($activeListings->count() > 0) {
    echo "3. تغییر وضعیت به pending...\n";
    foreach ($activeListings as $listing) {
        $listing->update(['status' => 'pending']);
        echo "   ✓ {$listing->title} → pending\n";
    }
    echo "\n";
}

// 3. Summary
echo "4. خلاصه:\n";
echo "   ✓ تایید دستی فعال شد\n";
echo "   ✓ آگهی‌های اخیر به pending تغییر یافتند\n";
echo "   ✓ از این به بعد آگهی‌های جدید باید توسط ادمین تایید شوند\n\n";

echo "5. مراحل بعدی:\n";
echo "   - برو به /admin/listings/manage\n";
echo "   - آگهی‌های pending را بررسی کن\n";
echo "   - آگهی‌های مناسب را تایید کن\n";

echo "</pre>";
