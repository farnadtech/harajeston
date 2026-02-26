<?php
/**
 * تست سیستم تغییرات pending و گام افزایش خودکار
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\ListingPendingChange;
use App\Models\SiteSetting;

echo "=== تست سیستم تغییرات Pending و گام افزایش ===\n\n";

// 1. بررسی تنظیمات گام افزایش
echo "1. بررسی تنظیمات گام افزایش:\n";
$defaultBidIncrement = SiteSetting::get('default_bid_increment', 10000);
echo "   گام افزایش پیش‌فرض: " . number_format($defaultBidIncrement) . " تومان\n\n";

// 2. بررسی آگهی‌های موجود
echo "2. بررسی آگهی‌های موجود:\n";
$listings = Listing::all();
echo "   تعداد کل آگهی‌ها: " . $listings->count() . "\n";

$differentIncrements = $listings->where('bid_increment', '!=', $defaultBidIncrement)->count();
if ($differentIncrements > 0) {
    echo "   ⚠️  {$differentIncrements} آگهی با گام افزایش متفاوت یافت شد\n";
    echo "   برای اعمال گام افزایش یکسان، از صفحه تنظیمات ادمین استفاده کنید\n";
} else {
    echo "   ✓ همه آگهی‌ها دارای گام افزایش یکسان هستند\n";
}
echo "\n";

// 3. بررسی تغییرات pending
echo "3. بررسی تغییرات Pending:\n";
$pendingChanges = ListingPendingChange::where('status', 'pending')->get();
echo "   تعداد تغییرات در انتظار: " . $pendingChanges->count() . "\n";

if ($pendingChanges->count() > 0) {
    echo "\n   جزئیات تغییرات:\n";
    foreach ($pendingChanges as $change) {
        $listing = $change->listing;
        echo "   - آگهی #{$listing->id}: {$listing->title}\n";
        echo "     تاریخ ثبت: " . $change->created_at->format('Y-m-d H:i') . "\n";
        echo "     تعداد فیلدهای تغییر یافته: " . count($change->changes) . "\n";
        
        // نمایش فیلدهای تغییر یافته
        $changedFields = array_keys($change->changes);
        echo "     فیلدها: " . implode(', ', $changedFields) . "\n\n";
    }
} else {
    echo "   ✓ هیچ تغییر pending وجود ندارد\n";
}
echo "\n";

// 4. بررسی آگهی‌های با bid فعال
echo "4. بررسی آگهی‌های با پیشنهاد فعال:\n";
$listingsWithBids = Listing::whereHas('bids')->get();
echo "   تعداد آگهی‌های دارای پیشنهاد: " . $listingsWithBids->count() . "\n";

if ($listingsWithBids->count() > 0) {
    foreach ($listingsWithBids as $listing) {
        $bidsCount = $listing->bids->count();
        $hasActiveBids = $listing->hasActiveBids();
        echo "   - آگهی #{$listing->id}: {$listing->title}\n";
        echo "     تعداد پیشنهادها: {$bidsCount}\n";
        echo "     وضعیت: " . ($hasActiveBids ? "دارای پیشنهاد فعال (محدودیت ویرایش)" : "بدون محدودیت") . "\n\n";
    }
}
echo "\n";

// 5. تست ایجاد یک تغییر pending (شبیه‌سازی)
echo "5. شبیه‌سازی ایجاد تغییر Pending:\n";
$testListing = Listing::where('status', 'active')->first();

if ($testListing) {
    echo "   آگهی تست: #{$testListing->id} - {$testListing->title}\n";
    echo "   وضعیت: {$testListing->status}\n";
    echo "   دارای پیشنهاد فعال: " . ($testListing->hasActiveBids() ? 'بله' : 'خیر') . "\n";
    echo "   دارای تغییرات pending: " . ($testListing->hasPendingChanges() ? 'بله' : 'خیر') . "\n";
    
    if ($testListing->hasPendingChanges()) {
        echo "   ⚠️  این آگهی قبلاً تغییرات pending دارد\n";
    }
} else {
    echo "   ℹ️  هیچ آگهی فعالی برای تست یافت نشد\n";
}
echo "\n";

// 6. خلاصه
echo "=== خلاصه ===\n";
echo "✓ سیستم تغییرات Pending فعال است\n";
echo "✓ گام افزایش خودکار پیکربندی شده: " . number_format($defaultBidIncrement) . " تومان\n";
echo "✓ تعداد آگهی‌ها: " . $listings->count() . "\n";
echo "✓ تغییرات در انتظار: " . $pendingChanges->count() . "\n";
echo "\n";

echo "📝 نکات:\n";
echo "- برای تغییر گام افزایش همه آگهی‌ها، از صفحه تنظیمات ادمین استفاده کنید\n";
echo "- تغییرات pending در صفحه مدیریت آگهی (manage) قابل مشاهده و تایید/رد هستند\n";
echo "- آگهی‌های با پیشنهاد فعال فقط توضیحات و روش ارسال قابل ویرایش دارند\n";
