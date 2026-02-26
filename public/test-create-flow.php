<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "<pre style='direction:rtl;font-family:Tahoma;'>";

echo "=== تست جریان ایجاد آگهی ===\n\n";

// Check setting
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "1. تنظیمات:\n";
echo "   require_listing_approval: " . ($requireApproval ? 'فعال ✓' : 'غیرفعال ✗') . "\n\n";

// Simulate what happens when creating
echo "2. شبیه‌سازی ایجاد آگهی:\n";

$futureDate = \Carbon\Carbon::now()->addDays(2);
$pastDate = \Carbon\Carbon::now()->subDays(1);

echo "\n   حالت A: زمان شروع در آینده ({$futureDate->format('Y-m-d H:i')})\n";
if ($requireApproval) {
    echo "   → status = pending (منتظر تایید ادمین)\n";
} else {
    echo "   → status = pending (منتظر شروع)\n";
}

echo "\n   حالت B: زمان شروع در گذشته ({$pastDate->format('Y-m-d H:i')})\n";
if ($requireApproval) {
    echo "   → status = pending (منتظر تایید ادمین)\n";
} else {
    echo "   → status = active (فعال)\n";
}

echo "\n3. بررسی آگهی‌های اخیر:\n";
$recentListings = \App\Models\Listing::orderBy('created_at', 'desc')->limit(5)->get();
foreach ($recentListings as $listing) {
    $startsAt = \Carbon\Carbon::parse($listing->starts_at);
    echo "   - {$listing->title}\n";
    echo "     status: {$listing->status}\n";
    echo "     starts_at: {$listing->starts_at} (" . ($startsAt->isFuture() ? 'آینده' : 'گذشته') . ")\n";
    echo "     created: {$listing->created_at->diffForHumans()}\n\n";
}

echo "\n4. توصیه:\n";
if (!$requireApproval) {
    echo "   ⚠️ تایید دستی غیرفعال است!\n";
    echo "   برای فعال‌سازی: /enable-approval.php\n";
} else {
    echo "   ✓ تایید دستی فعال است\n";
    echo "   ✓ آگهی‌های جدید باید توسط ادمین تایید شوند\n";
}

echo "</pre>";
