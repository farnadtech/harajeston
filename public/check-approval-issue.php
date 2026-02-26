<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

$slug = 'fkafka';
$listing = \App\Models\Listing::where('slug', $slug)->first();

echo "<pre style='direction:rtl;font-family:Tahoma;'>";
echo "=== بررسی مشکل تایید آگهی ===\n\n";

echo "1. تنظیمات:\n";
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "   require_listing_approval: " . ($requireApproval ? 'TRUE (فعال)' : 'FALSE (غیرفعال)') . "\n\n";

if ($listing) {
    echo "2. اطلاعات آگهی:\n";
    echo "   ID: {$listing->id}\n";
    echo "   عنوان: {$listing->title}\n";
    echo "   وضعیت: {$listing->status}\n";
    echo "   فروشنده: {$listing->seller->name} (ID: {$listing->seller_id})\n";
    echo "   نقش فروشنده: {$listing->seller->role}\n";
    echo "   زمان شروع: {$listing->starts_at}\n";
    echo "   زمان پایان: {$listing->ends_at}\n";
    echo "   ایجاد شده: {$listing->created_at}\n";
    echo "   آخرین ویرایش: {$listing->updated_at}\n\n";
    
    echo "3. بررسی منطق:\n";
    $startsAt = \Carbon\Carbon::parse($listing->starts_at);
    echo "   starts_at در آینده است؟ " . ($startsAt->isFuture() ? 'بله' : 'خیر') . "\n";
    echo "   وضعیت فعلی: {$listing->status}\n";
    
    if ($requireApproval) {
        echo "   ✓ تایید فعال است\n";
        if ($listing->status === 'pending') {
            echo "   ✓ وضعیت pending است - منتظر تایید ادمین\n";
        } else {
            echo "   ✗ وضعیت {$listing->status} است - باید pending باشد!\n";
        }
    } else {
        echo "   ✗ تایید غیرفعال است\n";
    }
} else {
    echo "آگهی با slug '{$slug}' پیدا نشد!\n";
}

echo "\n4. تست ایجاد آگهی جدید:\n";
echo "   اگر الان آگهی جدید بسازید:\n";
if ($requireApproval) {
    echo "   → status باید pending باشد (منتظر تایید)\n";
} else {
    echo "   → status بستگی به زمان شروع دارد\n";
}

echo "</pre>";
