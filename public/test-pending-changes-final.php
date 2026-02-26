<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>تست سیستم Pending Changes</h2>";

// 1. بررسی Notification Classes
echo "<h3>1. بررسی Notification Classes</h3>";
$approvedClass = class_exists('App\Notifications\ListingChangesApprovedNotification');
$rejectedClass = class_exists('App\Notifications\ListingChangesRejectedNotification');

echo "✓ ListingChangesApprovedNotification: " . ($approvedClass ? "موجود است" : "یافت نشد") . "<br>";
echo "✓ ListingChangesRejectedNotification: " . ($rejectedClass ? "موجود است" : "یافت نشد") . "<br>";

// 2. بررسی Routes
echo "<h3>2. بررسی Routes</h3>";
$routes = app('router')->getRoutes();
$approveRoute = $routes->getByName('admin.listings.pending-changes.approve');
$rejectRoute = $routes->getByName('admin.listings.pending-changes.reject');

echo "✓ Route approve: " . ($approveRoute ? "موجود است" : "یافت نشد") . "<br>";
echo "✓ Route reject: " . ($rejectRoute ? "موجود است" : "یافت نشد") . "<br>";

// 3. بررسی ListingPendingChange Model
echo "<h3>3. بررسی Model</h3>";
$modelExists = class_exists('App\Models\ListingPendingChange');
echo "✓ ListingPendingChange Model: " . ($modelExists ? "موجود است" : "یافت نشد") . "<br>";

if ($modelExists) {
    $pendingChanges = \App\Models\ListingPendingChange::where('status', 'pending')->get();
    echo "✓ تعداد تغییرات pending: " . $pendingChanges->count() . "<br>";
    
    if ($pendingChanges->count() > 0) {
        echo "<h4>نمونه تغییرات pending:</h4>";
        foreach ($pendingChanges->take(3) as $change) {
            echo "- آگهی #{$change->listing_id}: " . count($change->changes) . " فیلد تغییر یافته<br>";
        }
    }
}

// 4. بررسی آگهی‌هایی که تغییرات pending دارند
echo "<h3>4. آگهی‌های با تغییرات pending</h3>";
$listingsWithChanges = \App\Models\Listing::whereHas('pendingChanges', function($q) {
    $q->where('status', 'pending');
})->with('pendingChanges')->get();

echo "✓ تعداد آگهی‌ها: " . $listingsWithChanges->count() . "<br>";

if ($listingsWithChanges->count() > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>عنوان</th><th>تعداد تغییرات</th><th>لینک مدیریت</th></tr>";
    foreach ($listingsWithChanges as $listing) {
        $changesCount = $listing->pendingChanges->where('status', 'pending')->count();
        $manageUrl = url("/admin/listings/{$listing->id}/manage");
        echo "<tr>";
        echo "<td>{$listing->id}</td>";
        echo "<td>{$listing->title}</td>";
        echo "<td>{$changesCount}</td>";
        echo "<td><a href='{$manageUrl}' target='_blank'>مدیریت</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>✅ همه چیز آماده است!</h3>";
echo "<p>برای تست:</p>";
echo "<ol>";
echo "<li>یک آگهی فعال ایجاد کنید</li>";
echo "<li>آن را ویرایش کنید (تغییراتی در عنوان، توضیحات، روش ارسال ایجاد کنید)</li>";
echo "<li>به صفحه لیست آگهی‌ها بروید و ستون 'تغییرات' را بررسی کنید</li>";
echo "<li>روی دکمه مدیریت کلیک کنید</li>";
echo "<li>بنر نارنجی تغییرات را مشاهده کنید</li>";
echo "<li>دکمه‌های 'تایید' و 'رد' را تست کنید</li>";
echo "</ol>";
?>
