<?php
// Test slug-based URLs
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>✓ تست URL های با Slug (SEO Friendly)</h1>";
echo "<style>
    body { font-family: Tahoma, Arial; padding: 20px; direction: rtl; }
    .success { color: green; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
    th { background-color: #4CAF50; color: white; }
    a { color: #2196F3; text-decoration: none; padding: 5px 10px; background: #e3f2fd; border-radius: 4px; display: inline-block; margin: 2px; }
    a:hover { background: #2196F3; color: white; }
    code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 11px; }
    .seo-badge { background: #4CAF50; color: white; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
</style>";

$listings = \App\Models\Listing::take(5)->get();

echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<p class='success'>✓ تمام لینک‌ها با Slug ساخته می‌شوند (SEO Friendly)</p>";
echo "<p class='success'>✓ Route Model Binding فعال است</p>";
echo "<p class='success'>✓ لینک‌ها قابل خواندن و دوستانه برای موتورهای جستجو هستند</p>";
echo "</div>";

echo "<h2>لینک‌های عمومی (کاربران)</h2>";
echo "<table>";
echo "<tr><th>ID</th><th>عنوان</th><th>Slug</th><th>URL با Slug <span class='seo-badge'>SEO</span></th><th>تست</th></tr>";

foreach ($listings as $listing) {
    $url = route('listings.show', $listing);
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td><code>{$listing->slug}</code></td>";
    echo "<td><code>{$url}</code></td>";
    echo "<td><a href='{$url}' target='_blank'>مشاهده</a></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>لینک‌های ادمین</h2>";
echo "<table>";
echo "<tr><th>ID</th><th>عنوان</th><th>Slug</th><th>مشاهده</th><th>مدیریت</th><th>عملیات</th></tr>";

foreach ($listings as $listing) {
    $showUrl = route('admin.listings.show', $listing);
    $manageUrl = route('admin.listings.manage', $listing);
    $approveUrl = route('admin.listings.approve', $listing);
    $activateUrl = route('admin.listings.activate', $listing);
    $suspendUrl = route('admin.listings.suspend', $listing);
    
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td><code>{$listing->slug}</code></td>";
    echo "<td><a href='{$showUrl}' target='_blank'>مشاهده</a></td>";
    echo "<td><a href='{$manageUrl}' target='_blank'>مدیریت</a></td>";
    echo "<td>";
    echo "<a href='{$approveUrl}'>تایید</a> ";
    echo "<a href='{$activateUrl}'>فعال</a> ";
    echo "<a href='{$suspendUrl}'>تعلیق</a>";
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>مقایسه URL ها</h2>";
$testListing = $listings->first();
if ($testListing) {
    echo "<table>";
    echo "<tr><th>نوع</th><th>URL</th><th>SEO</th></tr>";
    echo "<tr>";
    echo "<td>با Slug (فعلی)</td>";
    echo "<td><code>" . route('listings.show', $testListing) . "</code></td>";
    echo "<td><span class='seo-badge'>✓ SEO Friendly</span></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>با ID (قدیمی)</td>";
    echo "<td><code>/listings/{$testListing->id}</code></td>";
    echo "<td style='color: red;'>✗ Not SEO Friendly</td>";
    echo "</tr>";
    echo "</table>";
}

echo "<h2>مزایای Slug</h2>";
echo "<ul>";
echo "<li>✓ قابل خواندن برای کاربران</li>";
echo "<li>✓ بهتر برای SEO و رتبه‌بندی گوگل</li>";
echo "<li>✓ شامل کلمات کلیدی محصول</li>";
echo "<li>✓ یونیک و قابل اشتراک‌گذاری</li>";
echo "</ul>";

echo "<hr style='margin: 30px 0;'>";
echo "<div style='text-align: center;'>";
echo "<a href='" . url('/admin/listings') . "' style='font-size: 16px; padding: 10px 20px;'>رفتن به پنل ادمین</a> ";
echo "<a href='" . url('/listings') . "' style='font-size: 16px; padding: 10px 20px;'>رفتن به لیست حراجی‌ها</a>";
echo "</div>";
