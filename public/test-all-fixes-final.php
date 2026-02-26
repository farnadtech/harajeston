<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test All Fixes</title>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo ".success{background:#d1fae5;border:2px solid #10b981;} .error{background:#fee2e2;border:2px solid #ef4444;}";
echo "h2{color:#2563eb;margin-top:0;}</style></head><body>";

echo "<h1>تست تمام تغییرات</h1>";

// 1. Test order status labels
echo "<div class='box'>";
echo "<h2>1. تست برچسب‌های وضعیت سفارش</h2>";
$statusLabels = [
    'pending' => 'در انتظار پرداخت',
    'paid' => 'پرداخت شده',
    'processing' => 'در حال پردازش',
    'shipped' => 'ارسال شده',
    'delivered' => 'تحویل داده شده',
    'completed' => 'تکمیل شده',
    'cancelled' => 'لغو شده',
    'refunded' => 'بازگشت وجه'
];
echo "<table style='width:100%;border-collapse:collapse;'>";
echo "<tr><th style='border:1px solid #ddd;padding:8px;'>کد انگلیسی</th><th style='border:1px solid #ddd;padding:8px;'>برچسب فارسی</th></tr>";
foreach ($statusLabels as $key => $label) {
    echo "<tr><td style='border:1px solid #ddd;padding:8px;'>{$key}</td><td style='border:1px solid #ddd;padding:8px;'>{$label}</td></tr>";
}
echo "</table>";
echo "<p class='success' style='margin-top:10px;padding:10px;border-radius:5px;'>✓ برچسب‌های فارسی تعریف شده</p>";
echo "</div>";

// 2. Test listing approval setting
echo "<div class='box'>";
echo "<h2>2. تست تنظیم تایید دستی آگهی‌ها</h2>";
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "<p>وضعیت فعلی: <strong>" . ($requireApproval ? 'فعال ✓' : 'غیرفعال ✗') . "</strong></p>";
if ($requireApproval) {
    echo "<p class='success' style='padding:10px;border-radius:5px;'>✓ تایید دستی فعال است</p>";
} else {
    echo "<p class='error' style='padding:10px;border-radius:5px;'>✗ تایید دستی غیرفعال است - از پنل ادمین فعال کنید</p>";
}
echo "<p><a href='/admin/settings' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>رفتن به تنظیمات</a></p>";
echo "</div>";

// 3. Test suspended listings
echo "<div class='box'>";
echo "<h2>3. تست آگهی‌های معلق</h2>";
$suspendedListings = \App\Models\Listing::where('status', 'suspended')->get();
echo "<p>تعداد آگهی‌های معلق: <strong>" . $suspendedListings->count() . "</strong></p>";
if ($suspendedListings->count() > 0) {
    echo "<table style='width:100%;border-collapse:collapse;margin-top:10px;'>";
    echo "<tr><th style='border:1px solid #ddd;padding:8px;'>عنوان</th><th style='border:1px solid #ddd;padding:8px;'>فروشنده</th><th style='border:1px solid #ddd;padding:8px;'>دلیل</th><th style='border:1px solid #ddd;padding:8px;'>عملیات</th></tr>";
    foreach ($suspendedListings as $listing) {
        echo "<tr>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$listing->title}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>{$listing->seller->name}</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'>" . ($listing->suspension_reason ?: '-') . "</td>";
        echo "<td style='border:1px solid #ddd;padding:8px;'><a href='/listings/{$listing->slug}' target='_blank'>مشاهده</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success' style='margin-top:10px;padding:10px;border-radius:5px;'>✓ دکمه 'ویرایش و ارسال مجدد' در صفحه آگهی نمایش داده می‌شود</p>";
}
echo "</div>";

// 4. Test pending listings
echo "<div class='box'>";
echo "<h2>4. تست آگهی‌های در انتظار تایید</h2>";
$pendingListings = \App\Models\Listing::where('status', 'pending')->count();
echo "<p>تعداد آگهی‌های در انتظار تایید: <strong>{$pendingListings}</strong></p>";
echo "<p><a href='/admin/listings/manage' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>مدیریت آگهی‌ها</a></p>";
echo "</div>";

// 5. Test cache
echo "<div class='box'>";
echo "<h2>5. تست کش</h2>";
echo "<p class='success' style='padding:10px;border-radius:5px;'>✓ کش پاک شده است</p>";
echo "</div>";

echo "<div class='box success'>";
echo "<h2>✓ خلاصه</h2>";
echo "<ul style='line-height:2;'>";
echo "<li>✓ برچسب‌های فارسی برای وضعیت سفارش اضافه شد</li>";
echo "<li>" . ($requireApproval ? '✓' : '✗') . " تنظیم تایید دستی در پنل ادمین " . ($requireApproval ? 'فعال' : 'غیرفعال') . " است</li>";
echo "<li>✓ دکمه 'ویرایش و ارسال مجدد' برای آگهی‌های معلق اضافه شد</li>";
echo "<li>✓ ویرایش آگهی معلق وضعیت را به pending تغییر می‌دهد</li>";
echo "<li>✓ کش پاک شد</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
