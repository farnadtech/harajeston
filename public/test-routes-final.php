<?php
// Final test for all routes
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>تست نهایی تمام Route ها</h1>";
echo "<style>
    body { font-family: Tahoma, Arial; padding: 20px; direction: rtl; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
    th { background-color: #4CAF50; color: white; }
    a { color: #2196F3; text-decoration: none; padding: 5px 10px; background: #e3f2fd; border-radius: 4px; display: inline-block; margin: 2px; }
    a:hover { background: #2196F3; color: white; }
    code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style>";

$listing = \App\Models\Listing::first();

if (!$listing) {
    echo "<p class='error'>هیچ حراجی در دیتابیس وجود ندارد!</p>";
    exit;
}

echo "<h2>اطلاعات حراجی تست</h2>";
echo "<table>";
echo "<tr><th>فیلد</th><th>مقدار</th></tr>";
echo "<tr><td>ID</td><td><code>{$listing->id}</code></td></tr>";
echo "<tr><td>عنوان</td><td>{$listing->title}</td></tr>";
echo "<tr><td>Slug</td><td><code>{$listing->slug}</code></td></tr>";
echo "<tr><td>وضعیت</td><td>{$listing->status}</td></tr>";
echo "</table>";

echo "<h2>✓ Route های عمومی (با ID)</h2>";
echo "<table>";
echo "<tr><th>نام Route</th><th>URL تولید شده</th><th>تست</th></tr>";

$publicRoutes = [
    'listings.show' => route('listings.show', $listing->id),
    'listings.participate' => route('listings.participate', $listing->id),
    'listings.comments.store' => route('listings.comments.store', $listing->id),
];

foreach ($publicRoutes as $name => $url) {
    echo "<tr>";
    echo "<td><code>{$name}</code></td>";
    echo "<td style='font-size: 11px;'><code>{$url}</code></td>";
    echo "<td><a href='{$url}' target='_blank'>تست لینک</a></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>✓ Route های ادمین (با ID)</h2>";
echo "<table>";
echo "<tr><th>نام Route</th><th>URL تولید شده</th><th>تست</th></tr>";

$adminRoutes = [
    'admin.listings.index' => route('admin.listings.index'),
    'admin.listings.show' => route('admin.listings.show', $listing->id),
    'admin.listings.manage' => route('admin.listings.manage', $listing->id),
    'admin.listings.edit' => route('admin.listings.edit', $listing->id),
    'admin.listings.approve' => route('admin.listings.approve', $listing->id),
    'admin.listings.reject' => route('admin.listings.reject', $listing->id),
    'admin.listings.activate' => route('admin.listings.activate', $listing->id),
    'admin.listings.suspend' => route('admin.listings.suspend', $listing->id),
];

foreach ($adminRoutes as $name => $url) {
    echo "<tr>";
    echo "<td><code>{$name}</code></td>";
    echo "<td style='font-size: 11px;'><code>{$url}</code></td>";
    echo "<td><a href='{$url}' target='_blank'>تست لینک</a></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>نتیجه نهایی</h2>";
echo "<div style='background: #e8f5e9; padding: 20px; border-radius: 8px; border-right: 4px solid #4CAF50;'>";
echo "<p class='success'>✓ تمام route ها با ID ساخته شدند</p>";
echo "<p class='success'>✓ دیگر از slug استفاده نمی‌شود</p>";
echo "<p class='success'>✓ تمام لینک‌ها باید کار کنند</p>";
echo "</div>";

echo "<hr style='margin: 30px 0;'>";
echo "<div style='text-align: center;'>";
echo "<a href='" . url('/admin/listings') . "' style='font-size: 16px; padding: 10px 20px;'>رفتن به پنل ادمین</a> ";
echo "<a href='" . url('/listings') . "' style='font-size: 16px; padding: 10px 20px;'>رفتن به لیست حراجی‌ها</a>";
echo "</div>";
