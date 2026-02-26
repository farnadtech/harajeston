<?php
/**
 * اعمال گام افزایش تنظیم شده توسط ادمین به تمام آگهی‌های موجود
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\SiteSetting;

echo "=== اعمال گام افزایش به تمام آگهی‌ها ===\n\n";

// دریافت گام افزایش از تنظیمات سایت
$defaultBidIncrement = SiteSetting::get('default_bid_increment', 10000);

echo "گام افزایش پیش‌فرض: " . number_format($defaultBidIncrement) . " تومان\n\n";

// دریافت تمام آگهی‌ها
$listings = Listing::all();

echo "تعداد کل آگهی‌ها: " . $listings->count() . "\n";
echo "در حال به‌روزرسانی...\n\n";

$updated = 0;

foreach ($listings as $listing) {
    $oldIncrement = $listing->bid_increment;
    
    $listing->update([
        'bid_increment' => $defaultBidIncrement
    ]);
    
    $updated++;
    
    echo "✓ آگهی #{$listing->id} - {$listing->title}\n";
    echo "  قبلی: " . number_format($oldIncrement) . " تومان → جدید: " . number_format($defaultBidIncrement) . " تومان\n\n";
}

echo "\n=== خلاصه ===\n";
echo "تعداد آگهی‌های به‌روزرسانی شده: {$updated}\n";
echo "گام افزایش جدید: " . number_format($defaultBidIncrement) . " تومان\n";
echo "\n✅ عملیات با موفقیت انجام شد!\n";
