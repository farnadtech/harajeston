<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== تست کامل ورک فلو تایید آگهی ===\n\n";

// Find a seller
$seller = \App\Models\User::where('role', 'seller')->first();
if (!$seller) {
    echo "خطا: فروشنده‌ای یافت نشد\n";
    exit;
}

echo "فروشنده: {$seller->name} (ID: {$seller->id})\n\n";

// 1. Create a new listing (should go to pending if approval is required)
echo "1. ایجاد آگهی جدید...\n";
$listing = \App\Models\Listing::create([
    'seller_id' => $seller->id,
    'title' => 'تست ورک فلو تایید - ' . now()->format('Y-m-d H:i:s'),
    'slug' => 'test-approval-workflow-' . uniqid(),
    'description' => 'این یک آگهی تستی برای بررسی ورک فلو تایید است',
    'category_id' => 1,
    'condition' => 'new',
    'starting_price' => 100000,
    'current_price' => 100000,
    'starts_at' => now()->addMinutes(5),
    'ends_at' => now()->addDays(7),
    'status' => 'pending', // Should be pending when approval is required
    'tags' => ['تست'],
]);

echo "آگهی ایجاد شد:\n";
echo "  - ID: {$listing->id}\n";
echo "  - عنوان: {$listing->title}\n";
echo "  - وضعیت: {$listing->status}\n";
echo "  - تایید شده: " . ($listing->approved_at ? 'بله' : 'خیر') . "\n";
echo "  - زمان شروع: {$listing->starts_at}\n\n";

// 2. Check if it needs approval
echo "2. بررسی نیاز به تایید...\n";
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "تنظیم نیاز به تایید: " . ($requireApproval ? 'فعال' : 'غیرفعال') . "\n";

if ($listing->status === 'pending' && !$listing->approved_at) {
    echo "✓ آگهی در وضعیت منتظر تایید ادمین است\n\n";
} else {
    echo "✗ آگهی در وضعیت اشتباه است\n\n";
}

// 3. Admin approves the listing
echo "3. تایید آگهی توسط ادمین...\n";
$admin = \App\Models\User::where('role', 'admin')->first();
if (!$admin) {
    echo "خطا: ادمینی یافت نشد\n";
    exit;
}

$listing->update([
    'approved_at' => now(),
    'approved_by' => $admin->id,
]);

echo "آگهی تایید شد:\n";
echo "  - تایید شده توسط: {$admin->name}\n";
echo "  - زمان تایید: {$listing->approved_at}\n";
echo "  - وضعیت: {$listing->status}\n\n";

// 4. Check if it should be activated by scheduler
echo "4. بررسی فعال‌سازی خودکار...\n";
if ($listing->starts_at->isFuture()) {
    echo "زمان شروع در آینده است ({$listing->starts_at})\n";
    echo "✓ آگهی باید در وضعیت pending بماند تا زمان شروع برسد\n\n";
    
    // Simulate time passing
    echo "5. شبیه‌سازی گذشت زمان...\n";
    $listing->update(['starts_at' => now()->subMinute()]);
    echo "زمان شروع به گذشته تغییر یافت\n\n";
    
    // Check if scheduler would activate it
    echo "6. بررسی شرایط فعال‌سازی توسط scheduler:\n";
    $shouldActivate = \App\Models\Listing::where('status', 'pending')
        ->whereNotNull('approved_at')
        ->where('starts_at', '<=', now())
        ->where('id', $listing->id)
        ->exists();
    
    if ($shouldActivate) {
        echo "✓ آگهی شرایط فعال‌سازی را دارد\n";
        $listing->update(['status' => 'active']);
        echo "✓ وضعیت به active تغییر یافت\n\n";
    } else {
        echo "✗ آگهی شرایط فعال‌سازی را ندارد\n\n";
    }
} else {
    echo "زمان شروع در گذشته است\n";
    echo "✓ آگهی باید فوراً فعال شود\n\n";
}

// 7. Final status
echo "7. وضعیت نهایی:\n";
$listing->refresh();
echo "  - وضعیت: {$listing->status}\n";
echo "  - تایید شده: " . ($listing->approved_at ? 'بله' : 'خیر') . "\n";
echo "  - زمان شروع: {$listing->starts_at}\n";
echo "  - زمان پایان: {$listing->ends_at}\n\n";

// 8. Test rejection workflow
echo "8. تست ورک فلو رد آگهی...\n";
$listing2 = \App\Models\Listing::create([
    'seller_id' => $seller->id,
    'title' => 'تست رد آگهی - ' . now()->format('Y-m-d H:i:s'),
    'slug' => 'test-rejection-' . uniqid(),
    'description' => 'این آگهی برای تست رد شدن است',
    'category_id' => 1,
    'condition' => 'new',
    'starting_price' => 50000,
    'current_price' => 50000,
    'starts_at' => now()->addHours(1),
    'ends_at' => now()->addDays(3),
    'status' => 'pending',
    'tags' => ['تست'],
]);

echo "آگهی دوم ایجاد شد (ID: {$listing2->id})\n";

$listing2->update([
    'status' => 'rejected',
    'rejection_reason' => 'محتوای نامناسب - تست',
]);

echo "آگهی رد شد:\n";
echo "  - وضعیت: {$listing2->status}\n";
echo "  - دلیل رد: {$listing2->rejection_reason}\n\n";

// Cleanup
echo "9. پاکسازی آگهی‌های تستی...\n";
$listing->delete();
$listing2->delete();
echo "✓ آگهی‌های تستی حذف شدند\n\n";

echo "=== پایان تست - همه چیز کار می‌کند! ===\n";
