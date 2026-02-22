<?php
// Test all listing links
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>تست تمام لینک‌های حراجی</h1>";
echo "<style>
    body { font-family: Tahoma, Arial; padding: 20px; direction: rtl; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
    th { background-color: #4CAF50; color: white; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    a { color: #2196F3; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .test-btn { 
        display: inline-block;
        padding: 5px 10px;
        background: #2196F3;
        color: white;
        border-radius: 4px;
        margin: 2px;
        font-size: 12px;
    }
    .test-btn:hover { background: #0b7dda; }
</style>";

$listings = \App\Models\Listing::with('seller')->take(5)->get();

echo "<h2>تست لینک‌های ادمین</h2>";
echo "<table>";
echo "<tr>
    <th>شناسه</th>
    <th>عنوان</th>
    <th>وضعیت</th>
    <th>لینک مشاهده (ادمین)</th>
    <th>لینک مدیریت</th>
    <th>لینک‌های عملیات</th>
</tr>";

foreach ($listings as $listing) {
    $showUrl = url("/admin/listings/{$listing->id}");
    $manageUrl = url("/admin/listings/{$listing->id}/manage");
    $approveUrl = url("/admin/listings/{$listing->id}/approve");
    $activateUrl = url("/admin/listings/{$listing->id}/activate");
    $suspendUrl = url("/admin/listings/{$listing->id}/suspend");
    
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td>{$listing->status}</td>";
    echo "<td><a href='{$showUrl}' target='_blank' class='test-btn'>مشاهده</a></td>";
    echo "<td><a href='{$manageUrl}' target='_blank' class='test-btn'>مدیریت</a></td>";
    echo "<td>";
    echo "<a href='{$approveUrl}' class='test-btn'>تایید</a> ";
    echo "<a href='{$activateUrl}' class='test-btn'>فعال</a> ";
    echo "<a href='{$suspendUrl}' class='test-btn'>تعلیق</a>";
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>تست لینک‌های عمومی (سایت)</h2>";
echo "<table>";
echo "<tr>
    <th>شناسه</th>
    <th>عنوان</th>
    <th>وضعیت</th>
    <th>لینک مشاهده (سایت)</th>
    <th>URL کامل</th>
</tr>";

foreach ($listings as $listing) {
    $publicUrl = url("/listings/{$listing->id}");
    
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td>{$listing->status}</td>";
    echo "<td><a href='{$publicUrl}' target='_blank' class='test-btn'>مشاهده در سایت</a></td>";
    echo "<td style='font-family: monospace; font-size: 11px;'>{$publicUrl}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>تست Route Helper</h2>";
echo "<table>";
echo "<tr><th>Route Name</th><th>Generated URL</th><th>Test</th></tr>";

$testListing = $listings->first();
if ($testListing) {
    $routes = [
        'admin.listings.show' => route('admin.listings.show', $testListing->id),
        'admin.listings.manage' => route('admin.listings.manage', $testListing->id),
        'admin.listings.approve' => route('admin.listings.approve', $testListing->id),
        'admin.listings.activate' => route('admin.listings.activate', $testListing->id),
        'admin.listings.suspend' => route('admin.listings.suspend', $testListing->id),
        'listings.show' => route('listings.show', $testListing->id),
    ];
    
    foreach ($routes as $name => $url) {
        echo "<tr>";
        echo "<td style='font-family: monospace;'>{$name}</td>";
        echo "<td style='font-family: monospace; font-size: 11px;'>{$url}</td>";
        echo "<td><a href='{$url}' target='_blank' class='test-btn'>تست</a></td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<h2>نتیجه</h2>";
echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 8px; border-right: 4px solid #4CAF50;'>";
echo "<p class='success'>✓ تمام لینک‌ها با ID ساده ساخته شدند</p>";
echo "<p>اگر هنوز 404 می‌گیرید، مطمئن شوید که:</p>";
echo "<ul>";
echo "<li>Apache mod_rewrite فعال است</li>";
echo "<li>Cache های Laravel پاک شده‌اند</li>";
echo "<li>با اکانت ادمین لاگین کرده‌اید</li>";
echo "</ul>";
echo "</div>";

echo "<hr style='margin: 30px 0;'>";
echo "<p style='text-align: center; color: #666;'>برای بازگشت به پنل ادمین: <a href='" . url('/admin/listings') . "'>لیست حراجی‌ها</a></p>";
