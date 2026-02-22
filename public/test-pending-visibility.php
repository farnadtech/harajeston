<?php
// Test pending auction visibility based on admin settings

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>تست نمایش حراجی‌های pending</h1>";
echo "<hr>";

// Get admin setting
$showPendingListings = \App\Models\SiteSetting::get('default_show_before_start', false);

echo "<h2>تنظیمات ادمین:</h2>";
echo "<p><strong>نمایش پیش‌فرض حراجی‌ها قبل از شروع:</strong> " . ($showPendingListings ? '✅ فعال' : '❌ غیرفعال') . "</p>";
echo "<hr>";

// Get all pending listings
$pendingListings = \App\Models\Listing::where('status', 'pending')->get();

echo "<h2>حراجی‌های pending در دیتابیس:</h2>";
echo "<p>تعداد: " . $pendingListings->count() . "</p>";

if ($pendingListings->count() > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>عنوان</th>";
    echo "<th>Slug</th>";
    echo "<th>وضعیت</th>";
    echo "<th>زمان شروع</th>";
    echo "<th>show_before_start (فیلد - نادیده گرفته می‌شود)</th>";
    echo "<th>آیا نمایش داده می‌شود؟</th>";
    echo "</tr>";
    
    foreach ($pendingListings as $listing) {
        $willBeShown = $showPendingListings ? 'بله (بر اساس تنظیمات ادمین)' : 'خیر (بر اساس تنظیمات ادمین)';
        
        echo "<tr>";
        echo "<td>" . $listing->id . "</td>";
        echo "<td>" . $listing->title . "</td>";
        echo "<td>" . $listing->slug . "</td>";
        echo "<td>" . $listing->status . "</td>";
        echo "<td>" . $listing->starts_at . "</td>";
        echo "<td>" . ($listing->show_before_start ? 'true' : 'false') . "</td>";
        echo "<td><strong>" . $willBeShown . "</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr>";
echo "<h2>تست لینک‌ها:</h2>";

foreach ($pendingListings as $listing) {
    $url = url('/listings/' . $listing->slug);
    echo "<p><a href='{$url}' target='_blank'>{$listing->title}</a></p>";
}

echo "<hr>";
echo "<h2>توضیحات:</h2>";
echo "<ul>";
echo "<li>✅ اگر تنظیمات ادمین فعال باشد: همه حراجی‌های pending برای همه کاربران نمایش داده می‌شوند</li>";
echo "<li>❌ اگر تنظیمات ادمین غیرفعال باشد: حراجی‌های pending فقط برای ادمین و صاحب آگهی نمایش داده می‌شوند</li>";
echo "<li>⚠️ فیلد show_before_start در دیتابیس دیگر استفاده نمی‌شود و نادیده گرفته می‌شود</li>";
echo "<li>🎯 فقط تنظیمات ادمین (default_show_before_start) کنترل می‌کند که آیا pending ها نمایش داده شوند یا نه</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>تغییر تنظیمات:</h2>";
echo "<p><a href='" . url('/admin/settings') . "' target='_blank'>رفتن به تنظیمات ادمین</a></p>";
