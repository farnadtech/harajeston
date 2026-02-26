<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;
use App\Models\Listing;

echo "<h2>بررسی و فعال‌سازی سیستم تایید ادمین</h2>";

// Check current setting
$currentSetting = SiteSetting::where('key', 'require_listing_approval')->first();
echo "<h3>وضعیت فعلی:</h3>";
if ($currentSetting) {
    echo "<p>Setting exists: " . ($currentSetting->value ? 'TRUE' : 'FALSE') . "</p>";
} else {
    echo "<p style='color: red;'>Setting does not exist!</p>";
}

// Enable the setting
SiteSetting::set('require_listing_approval', true);
echo "<p style='color: green;'>✓ تایید ادمین فعال شد</p>";

// Verify
$newSetting = SiteSetting::get('require_listing_approval', false);
echo "<h3>وضعیت جدید:</h3>";
echo "<p>require_listing_approval = " . ($newSetting ? 'TRUE' : 'FALSE') . "</p>";

// Check recent listings
echo "<h3>آگهی‌های اخیر:</h3>";
$recentListings = Listing::orderBy('created_at', 'desc')->take(5)->get();
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>عنوان</th><th>وضعیت</th><th>تاریخ ایجاد</th></tr>";
foreach ($recentListings as $listing) {
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td><strong>{$listing->status}</strong></td>";
    echo "<td>{$listing->created_at}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3 style='color: green;'>✓ سیستم تایید ادمین فعال است</h3>";
echo "<p>از این به بعد همه آگهی‌های جدید با وضعیت 'pending' ایجاد می‌شوند و نیاز به تایید ادمین دارند.</p>";
echo "<p><a href='/admin/listings/manage'>مدیریت آگهی‌ها</a></p>";
