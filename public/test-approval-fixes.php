<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== تست تمام تغییرات سیستم تایید آگهی ===\n\n";

// 1. Test: Listings needing approval
echo "1. آگهی‌های نیاز به تایید (pending + approved_at = null):\n";
$needsApproval = \App\Models\Listing::where('status', 'pending')
    ->whereNull('approved_at')
    ->get();
echo "تعداد: " . $needsApproval->count() . "\n";
foreach ($needsApproval as $listing) {
    echo "  - ID: {$listing->id}, عنوان: {$listing->title}, وضعیت: {$listing->status}, تایید شده: " . ($listing->approved_at ? 'بله' : 'خیر') . "\n";
}
echo "\n";

// 2. Test: Approved but not started
echo "2. آگهی‌های تایید شده ولی هنوز شروع نشده:\n";
$approvedPending = \App\Models\Listing::where('status', 'pending')
    ->whereNotNull('approved_at')
    ->where('starts_at', '>', now())
    ->get();
echo "تعداد: " . $approvedPending->count() . "\n";
foreach ($approvedPending as $listing) {
    echo "  - ID: {$listing->id}, عنوان: {$listing->title}, زمان شروع: {$listing->starts_at}, تایید شده توسط: {$listing->approved_by}\n";
}
echo "\n";

// 3. Test: Active listings
echo "3. آگهی‌های فعال:\n";
$active = \App\Models\Listing::where('status', 'active')->get();
echo "تعداد: " . $active->count() . "\n";
foreach ($active as $listing) {
    echo "  - ID: {$listing->id}, عنوان: {$listing->title}, تایید شده: " . ($listing->approved_at ? 'بله' : 'خیر') . "\n";
}
echo "\n";

// 4. Test: Check if require_listing_approval is enabled
echo "4. تنظیم نیاز به تایید دستی:\n";
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "وضعیت: " . ($requireApproval ? 'فعال' : 'غیرفعال') . "\n\n";

// 5. Test: Rejected listings
echo "5. آگهی‌های رد شده:\n";
$rejected = \App\Models\Listing::where('status', 'rejected')->get();
echo "تعداد: " . $rejected->count() . "\n";
foreach ($rejected as $listing) {
    echo "  - ID: {$listing->id}, عنوان: {$listing->title}, دلیل رد: {$listing->rejection_reason}\n";
}
echo "\n";

// 6. Test: Check ProcessAuctionStarting logic
echo "6. آگهی‌هایی که باید توسط scheduler فعال شوند:\n";
echo "   (pending + approved_at NOT NULL + starts_at <= now)\n";
$shouldActivate = \App\Models\Listing::where('status', 'pending')
    ->whereNotNull('approved_at')
    ->where('starts_at', '<=', now())
    ->get();
echo "تعداد: " . $shouldActivate->count() . "\n";
foreach ($shouldActivate as $listing) {
    echo "  - ID: {$listing->id}, عنوان: {$listing->title}, زمان شروع: {$listing->starts_at}\n";
}
echo "\n";

echo "=== پایان تست ===\n";
