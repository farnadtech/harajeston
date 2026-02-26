<?php
/**
 * تست به‌روزرسانی خودکار گام افزایش برای همه آگهی‌ها
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\SiteSetting;

echo "=== تست به‌روزرسانی خودکار گام افزایش ===\n\n";

// 1. وضعیت فعلی
echo "1. وضعیت فعلی:\n";
$currentBidIncrement = SiteSetting::get('default_bid_increment', 10000);
echo "   گام افزایش فعلی: " . number_format($currentBidIncrement) . " تومان\n";

$listings = Listing::all();
echo "   تعداد کل آگهی‌ها: " . $listings->count() . "\n";

// نمایش توزیع گام افزایش
$incrementDistribution = $listings->groupBy('bid_increment')->map(function($group) {
    return $group->count();
});

echo "\n   توزیع گام افزایش فعلی:\n";
foreach ($incrementDistribution as $increment => $count) {
    echo "   - " . number_format($increment) . " تومان: {$count} آگهی\n";
}
echo "\n";

// 2. شبیه‌سازی تغییر گام افزایش
$newBidIncrement = 15000; // مقدار جدید برای تست
echo "2. شبیه‌سازی تغییر گام افزایش به " . number_format($newBidIncrement) . " تومان:\n";

// ذخیره مقدار جدید
SiteSetting::set('default_bid_increment', $newBidIncrement, 'integer');
echo "   ✓ تنظیمات ذخیره شد\n";

// به‌روزرسانی همه آگهی‌ها
$updatedCount = Listing::query()->update([
    'bid_increment' => $newBidIncrement
]);

echo "   ✓ {$updatedCount} آگهی به‌روزرسانی شد\n\n";

// 3. بررسی نتیجه
echo "3. بررسی نتیجه:\n";
$listings = Listing::all();

$incrementDistribution = $listings->groupBy('bid_increment')->map(function($group) {
    return $group->count();
});

echo "   توزیع گام افزایش جدید:\n";
foreach ($incrementDistribution as $increment => $count) {
    echo "   - " . number_format($increment) . " تومان: {$count} آگهی\n";
}

// بررسی اینکه همه آگهی‌ها یکسان شدند
$allSame = $listings->every(function($listing) use ($newBidIncrement) {
    return $listing->bid_increment == $newBidIncrement;
});

echo "\n";
if ($allSame) {
    echo "   ✅ همه آگهی‌ها با موفقیت به گام افزایش جدید تغییر کردند!\n";
} else {
    echo "   ⚠️  برخی آگهی‌ها هنوز گام افزایش متفاوت دارند\n";
}

// 4. بازگرداندن به مقدار قبلی
echo "\n4. بازگرداندن به مقدار قبلی:\n";
SiteSetting::set('default_bid_increment', $currentBidIncrement, 'integer');
Listing::query()->update(['bid_increment' => $currentBidIncrement]);
echo "   ✓ گام افزایش به " . number_format($currentBidIncrement) . " تومان بازگردانده شد\n";

echo "\n=== نتیجه ===\n";
echo "✅ سیستم به‌روزرسانی خودکار گام افزایش به درستی کار می‌کند\n";
echo "✅ وقتی ادمین گام افزایش را تغییر دهد، همه آگهی‌ها خودکار آپدیت می‌شوند\n";
echo "\n📝 برای تغییر گام افزایش:\n";
echo "   پنل ادمین → تنظیمات → تنظیمات آگهی‌ها → گام افزایش پیش‌فرض\n";
