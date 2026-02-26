<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>تست سیستم تایید آگهی‌ها</title>";
echo "<style>
body{font-family:Tahoma;direction:rtl;padding:20px;background:#f5f5f5;}
.box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.success{background:#d1fae5;border:2px solid #10b981;}
.error{background:#fee2e2;border:2px solid #ef4444;}
.warning{background:#fef3c7;border:2px solid #f59e0b;}
.info{background:#dbeafe;border:2px solid #3b82f6;}
h2{color:#2563eb;margin-top:0;}
table{width:100%;border-collapse:collapse;margin:10px 0;}
th,td{border:1px solid #ddd;padding:8px;text-align:right;}
th{background:#f3f4f6;font-weight:bold;}
.badge{display:inline-block;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold;}
.badge-pending{background:#fef3c7;color:#92400e;}
.badge-active{background:#d1fae5;color:#065f46;}
.badge-suspended{background:#fee2e2;color:#991b1b;}
</style></head><body>";

echo "<h1>🔍 تست سیستم تایید آگهی‌ها</h1>";

// 1. Check current settings
echo "<div class='box info'>";
echo "<h2>1️⃣ تنظیمات فعلی</h2>";
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
$showBeforeStart = \App\Models\SiteSetting::get('default_show_before_start', false);

echo "<table>";
echo "<tr><th>تنظیم</th><th>وضعیت</th><th>توضیح</th></tr>";
echo "<tr>";
echo "<td>نیاز به تایید دستی</td>";
echo "<td><strong>" . ($requireApproval ? '✅ فعال' : '❌ غیرفعال') . "</strong></td>";
echo "<td>" . ($requireApproval ? 'آگهی‌های جدید و ویرایش شده نیاز به تایید دارند' : 'آگهی‌ها بدون تایید منتشر می‌شوند') . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>نمایش قبل از شروع</td>";
echo "<td><strong>" . ($showBeforeStart ? '✅ فعال' : '❌ غیرفعال') . "</strong></td>";
echo "<td>" . ($showBeforeStart ? 'آگهی‌های pending در لیست عمومی نمایش داده می‌شوند' : 'فقط آگهی‌های active نمایش داده می‌شوند') . "</td>";
echo "</tr>";
echo "</table>";

echo "<p style='margin-top:15px;'><a href='/admin/settings' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>تغییر تنظیمات</a></p>";
echo "</div>";

// 2. Listing status breakdown
echo "<div class='box'>";
echo "<h2>2️⃣ آمار آگهی‌ها بر اساس وضعیت</h2>";

$statuses = [
    'pending' => ['label' => 'منتظر تایید / شروع', 'color' => 'warning'],
    'active' => ['label' => 'فعال', 'color' => 'success'],
    'suspended' => ['label' => 'تعلیق شده', 'color' => 'error'],
    'ended' => ['label' => 'پایان یافته', 'color' => 'info'],
    'completed' => ['label' => 'تکمیل شده', 'color' => 'success'],
];

echo "<table>";
echo "<tr><th>وضعیت</th><th>تعداد</th><th>توضیح</th></tr>";

foreach ($statuses as $status => $info) {
    $count = \App\Models\Listing::where('status', $status)->count();
    echo "<tr>";
    echo "<td><span class='badge badge-{$status}'>{$info['label']}</span></td>";
    echo "<td><strong>{$count}</strong></td>";
    echo "<td>";
    
    switch($status) {
        case 'pending':
            if ($requireApproval) {
                echo "آگهی‌هایی که منتظر تایید ادمین هستند یا تایید شده‌اند ولی هنوز شروع نشده‌اند";
            } else {
                echo "آگهی‌هایی که تایید شده‌اند ولی هنوز شروع نشده‌اند";
            }
            break;
        case 'active':
            echo "آگهی‌های در حال برگزاری";
            break;
        case 'suspended':
            echo "آگهی‌های تعلیق شده توسط ادمین - نیاز به ویرایش و تایید مجدد";
            break;
        case 'ended':
            echo "آگهی‌هایی که زمانشان تمام شده ولی هنوز تکمیل نشده‌اند";
            break;
        case 'completed':
            echo "آگهی‌های تکمیل شده";
            break;
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// 3. Pending listings details
$pendingListings = \App\Models\Listing::where('status', 'pending')
    ->with('seller')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($pendingListings->count() > 0) {
    echo "<div class='box warning'>";
    echo "<h2>3️⃣ آگهی‌های منتظر تایید (10 مورد اخیر)</h2>";
    echo "<table>";
    echo "<tr><th>عنوان</th><th>فروشنده</th><th>زمان شروع</th><th>تاریخ ثبت</th><th>عملیات</th></tr>";
    
    foreach ($pendingListings as $listing) {
        $startsAt = \Carbon\Carbon::parse($listing->starts_at);
        $isPast = $startsAt->isPast();
        
        echo "<tr>";
        echo "<td>{$listing->title}</td>";
        echo "<td>{$listing->seller->name}</td>";
        echo "<td>";
        if ($isPast) {
            echo "<span style='color:#dc2626;'>⚠️ گذشته - باید تایید شود</span>";
        } else {
            echo "<span style='color:#059669;'>✓ آینده - " . $startsAt->diffForHumans() . "</span>";
        }
        echo "</td>";
        echo "<td>" . $listing->created_at->diffForHumans() . "</td>";
        echo "<td>";
        echo "<a href='/admin/listings/{$listing->id}' target='_blank' style='color:#2563eb;'>مشاهده</a>";
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p style='margin-top:15px;'><a href='/admin/listings/manage' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>مدیریت همه آگهی‌ها</a></p>";
    echo "</div>";
} else {
    echo "<div class='box success'>";
    echo "<h2>3️⃣ آگهی‌های منتظر تایید</h2>";
    echo "<p>✅ هیچ آگهی منتظر تایید وجود ندارد</p>";
    echo "</div>";
}

// 4. Suspended listings
$suspendedListings = \App\Models\Listing::where('status', 'suspended')
    ->with('seller')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();

if ($suspendedListings->count() > 0) {
    echo "<div class='box error'>";
    echo "<h2>4️⃣ آگهی‌های تعلیق شده</h2>";
    echo "<table>";
    echo "<tr><th>عنوان</th><th>فروشنده</th><th>دلیل تعلیق</th><th>تاریخ تعلیق</th></tr>";
    
    foreach ($suspendedListings as $listing) {
        echo "<tr>";
        echo "<td>{$listing->title}</td>";
        echo "<td>{$listing->seller->name}</td>";
        echo "<td>" . ($listing->suspension_reason ?: '-') . "</td>";
        echo "<td>" . $listing->updated_at->diffForHumans() . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<p style='margin-top:10px;color:#991b1b;'><strong>توجه:</strong> فروشنده باید این آگهی‌ها را ویرایش کند تا دوباره برای تایید ارسال شوند.</p>";
    echo "</div>";
}

// 5. Workflow explanation
echo "<div class='box info'>";
echo "<h2>5️⃣ Workflow سیستم تایید</h2>";

if ($requireApproval) {
    echo "<h3 style='color:#2563eb;'>✅ تایید ادمین فعال است</h3>";
    echo "<ol style='line-height:2;'>";
    echo "<li>فروشنده آگهی می‌سازد → <span class='badge badge-pending'>pending</span></li>";
    echo "<li>ادمین آگهی را بررسی و تایید می‌کند:</li>";
    echo "<ul>";
    echo "<li>اگر زمان شروع در آینده باشد → <span class='badge badge-pending'>pending</span> (منتظر شروع)</li>";
    echo "<li>اگر زمان شروع گذشته باشد → <span class='badge badge-active'>active</span></li>";
    echo "</ul>";
    echo "<li>فروشنده آگهی را ویرایش می‌کند → <span class='badge badge-pending'>pending</span> (نیاز به تایید مجدد)</li>";
    echo "<li>ادمین دوباره تایید می‌کند → <span class='badge badge-active'>active</span> یا <span class='badge badge-pending'>pending</span></li>";
    echo "</ol>";
} else {
    echo "<h3 style='color:#dc2626;'>❌ تایید ادمین غیرفعال است</h3>";
    echo "<ol style='line-height:2;'>";
    echo "<li>فروشنده آگهی می‌سازد:</li>";
    echo "<ul>";
    echo "<li>اگر زمان شروع در آینده باشد → <span class='badge badge-pending'>pending</span> (منتظر شروع)</li>";
    echo "<li>اگر زمان شروع گذشته باشد → <span class='badge badge-active'>active</span></li>";
    echo "</ul>";
    echo "<li>فروشنده آگهی را ویرایش می‌کند → وضعیت تغییر نمی‌کند</li>";
    echo "<li>ادمین آگهی را تعلیق می‌کند → <span class='badge badge-suspended'>suspended</span></li>";
    echo "<li>فروشنده آگهی معلق را ویرایش می‌کند → <span class='badge badge-pending'>pending</span> (نیاز به تایید)</li>";
    echo "</ol>";
}

echo "</div>";

// 6. Summary
echo "<div class='box success'>";
echo "<h2>✅ خلاصه</h2>";
echo "<ul style='line-height:2;'>";
echo "<li>✓ سیستم تایید ساده‌سازی شد</li>";
echo "<li>✓ تنظیمات تکراری حذف شد</li>";
echo "<li>✓ برچسب‌های فارسی برای وضعیت سفارش اضافه شد</li>";
echo "<li>✓ Workflow واضح و قابل فهم است</li>";
echo "<li>✓ ادمین‌ها می‌توانند بدون نیاز به تایید ویرایش کنند</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
