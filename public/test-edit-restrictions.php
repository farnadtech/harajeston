<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== تست محدودیت‌های ویرایش آگهی ===\n\n";

// 1. Test bid_increment setting
echo "1. تست تنظیم bid_increment:\n";
$bidIncrement = \App\Models\SiteSetting::get('default_bid_increment', 10000);
echo "مقدار فعلی: " . number_format($bidIncrement) . " تومان\n\n";

// 2. Test hasActiveBids method
echo "2. تست متد hasActiveBids:\n";
$listingWithBids = \App\Models\Listing::whereHas('bids')->first();
if ($listingWithBids) {
    echo "آگهی با پیشنهاد: ID {$listingWithBids->id}\n";
    echo "  - تعداد پیشنهادها: " . $listingWithBids->bids()->count() . "\n";
    echo "  - hasActiveBids(): " . ($listingWithBids->hasActiveBids() ? 'true' : 'false') . "\n";
} else {
    echo "آگهی با پیشنهاد یافت نشد\n";
}
echo "\n";

$listingWithoutBids = \App\Models\Listing::whereDoesntHave('bids')->first();
if ($listingWithoutBids) {
    echo "آگهی بدون پیشنهاد: ID {$listingWithoutBids->id}\n";
    echo "  - تعداد پیشنهادها: " . $listingWithoutBids->bids()->count() . "\n";
    echo "  - hasActiveBids(): " . ($listingWithoutBids->hasActiveBids() ? 'true' : 'false') . "\n";
} else {
    echo "آگهی بدون پیشنهاد یافت نشد\n";
}
echo "\n";

// 3. Test pending changes
echo "3. تست pending changes:\n";
$pendingChanges = \App\Models\ListingPendingChange::where('status', 'pending')->get();
echo "تعداد تغییرات در انتظار: " . $pendingChanges->count() . "\n";
foreach ($pendingChanges as $change) {
    echo "  - Listing ID: {$change->listing_id}\n";
    echo "    وضعیت: {$change->status}\n";
    echo "    تاریخ: {$change->created_at}\n";
}
echo "\n";

// 4. Test hasPendingChanges method
echo "4. تست متد hasPendingChanges:\n";
$listingWithPendingChanges = \App\Models\Listing::whereHas('pendingChanges', function($q) {
    $q->where('status', 'pending');
})->first();

if ($listingWithPendingChanges) {
    echo "آگهی با تغییرات pending: ID {$listingWithPendingChanges->id}\n";
    echo "  - hasPendingChanges(): " . ($listingWithPendingChanges->hasPendingChanges() ? 'true' : 'false') . "\n";
} else {
    echo "آگهی با تغییرات pending یافت نشد\n";
}
echo "\n";

// 5. Test new listing creation with default bid_increment
echo "5. تست ایجاد آگهی جدید با bid_increment پیش‌فرض:\n";
$seller = \App\Models\User::where('role', 'seller')->first();
if ($seller) {
    $testListing = \App\Models\Listing::create([
        'seller_id' => $seller->id,
        'title' => 'تست bid_increment - ' . now()->format('Y-m-d H:i:s'),
        'slug' => 'test-bid-increment-' . uniqid(),
        'description' => 'تست',
        'category_id' => 1,
        'condition' => 'new',
        'starting_price' => 100000,
        'current_price' => 100000,
        'bid_increment' => \App\Models\SiteSetting::get('default_bid_increment', 10000),
        'starts_at' => now()->addHours(1),
        'ends_at' => now()->addDays(7),
        'status' => 'pending',
        'tags' => ['test'],
    ]);
    
    echo "آگهی تستی ایجاد شد:\n";
    echo "  - ID: {$testListing->id}\n";
    echo "  - bid_increment: " . number_format($testListing->bid_increment) . " تومان\n";
    echo "  - مطابقت با تنظیمات: " . ($testListing->bid_increment == $bidIncrement ? 'بله' : 'خیر') . "\n";
    
    // Cleanup
    $testListing->delete();
    echo "  - آگهی تستی حذف شد\n";
} else {
    echo "فروشنده‌ای یافت نشد\n";
}
echo "\n";

echo "=== پایان تست ===\n";
