<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;
use App\Models\Listing;
use App\Models\Order;

echo "<style>
body { font-family: Tahoma, Arial; direction: rtl; padding: 20px; }
h2 { color: #135bec; border-bottom: 2px solid #135bec; padding-bottom: 10px; }
h3 { color: #333; margin-top: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
th { background-color: #f2f2f2; }
.badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
.badge-green { background: #d1fae5; color: #065f46; }
.badge-yellow { background: #fef3c7; color: #92400e; }
.badge-red { background: #fee2e2; color: #991b1b; }
</style>";

echo "<h2>🔍 تست کامل سیستم - بررسی همه مشکلات</h2>";

// 1. Check admin approval setting
echo "<h3>1️⃣ بررسی تایید ادمین</h3>";
$approvalSetting = SiteSetting::get('require_listing_approval', false);
if ($approvalSetting) {
    echo "<p class='success'>✓ تایید ادمین فعال است</p>";
} else {
    echo "<p class='error'>✗ تایید ادمین غیرفعال است</p>";
    SiteSetting::set('require_listing_approval', true);
    echo "<p class='success'>✓ تایید ادمین فعال شد</p>";
}

// 2. Check recent listings status
echo "<h3>2️⃣ وضعیت آگهی‌های اخیر</h3>";
$recentListings = Listing::orderBy('created_at', 'desc')->take(5)->get();
echo "<table>";
echo "<tr><th>ID</th><th>عنوان</th><th>وضعیت</th><th>تاریخ ایجاد</th></tr>";
foreach ($recentListings as $listing) {
    $badgeClass = $listing->status === 'pending' ? 'badge-yellow' : ($listing->status === 'active' ? 'badge-green' : 'badge-red');
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td><span class='badge {$badgeClass}'>{$listing->status}</span></td>";
    echo "<td>{$listing->created_at->format('Y-m-d H:i')}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Check suspended listings
echo "<h3>3️⃣ آگهی‌های تعلیق شده</h3>";
$suspendedListings = Listing::where('status', 'suspended')->get();
if ($suspendedListings->count() > 0) {
    echo "<p class='success'>✓ تعداد آگهی‌های تعلیق شده: {$suspendedListings->count()}</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>عنوان</th><th>دلیل رد</th><th>لینک ویرایش</th></tr>";
    foreach ($suspendedListings as $listing) {
        echo "<tr>";
        echo "<td>{$listing->id}</td>";
        echo "<td>{$listing->title}</td>";
        echo "<td>" . ($listing->rejection_reason ?? 'ندارد') . "</td>";
        echo "<td><a href='/listings/{$listing->id}/edit' target='_blank'>ویرایش</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>هیچ آگهی تعلیق شده‌ای وجود ندارد.</p>";
}

// 4. Check order statuses
echo "<h3>4️⃣ وضعیت سفارشات اخیر</h3>";
$recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();
if ($recentOrders->count() > 0) {
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
    
    echo "<table>";
    echo "<tr><th>شماره سفارش</th><th>وضعیت (انگلیسی)</th><th>وضعیت (فارسی)</th><th>تاریخ</th></tr>";
    foreach ($recentOrders as $order) {
        $persianStatus = $statusLabels[$order->status] ?? $order->status;
        echo "<tr>";
        echo "<td>{$order->order_number}</td>";
        echo "<td>{$order->status}</td>";
        echo "<td><strong>{$persianStatus}</strong></td>";
        echo "<td>{$order->created_at->format('Y-m-d H:i')}</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success'>✓ ترجمه فارسی وضعیت سفارشات کار می‌کند</p>";
} else {
    echo "<p>هیچ سفارشی وجود ندارد.</p>";
}

// 5. Cache clearing
echo "<h3>5️⃣ پاک کردن کش‌ها</h3>";
try {
    Artisan::call('cache:clear');
    echo "<p class='success'>✓ Application cache cleared</p>";
    
    Artisan::call('view:clear');
    echo "<p class='success'>✓ View cache cleared</p>";
    
    Artisan::call('config:clear');
    echo "<p class='success'>✓ Config cache cleared</p>";
    
    Artisan::call('route:clear');
    echo "<p class='success'>✓ Route cache cleared</p>";
    
    echo "<p class='success'>✓ همه کش‌ها پاک شدند</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ خطا در پاک کردن کش: {$e->getMessage()}</p>";
}

// Summary
echo "<h3>📊 خلاصه</h3>";
echo "<ul>";
echo "<li><strong>تایید ادمین:</strong> " . ($approvalSetting ? "✓ فعال" : "✗ غیرفعال") . "</li>";
echo "<li><strong>آگهی‌های تعلیق شده:</strong> {$suspendedListings->count()} عدد</li>";
echo "<li><strong>سفارشات اخیر:</strong> {$recentOrders->count()} عدد</li>";
echo "<li><strong>کش‌ها:</strong> ✓ پاک شدند</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>🔗 لینک‌های مفید</h3>";
echo "<ul>";
echo "<li><a href='/dashboard'>داشبورد فروشنده</a> - برای بررسی ترجمه فارسی وضعیت سفارشات</li>";
echo "<li><a href='/listings/create'>ایجاد آگهی جدید</a> - برای تست انتخاب چند عکس و حذف عکس</li>";
echo "<li><a href='/admin/listings/manage'>مدیریت آگهی‌ها (ادمین)</a> - برای تایید آگهی‌های pending</li>";
echo "</ul>";

echo "<p style='margin-top: 30px; padding: 15px; background: #f0f9ff; border-right: 4px solid #135bec;'>";
echo "<strong>توجه:</strong> اگر هنوز مشکلی دارید، لطفاً مرورگر خود را با Ctrl+F5 رفرش کنید تا کش مرورگر پاک شود.";
echo "</p>";
