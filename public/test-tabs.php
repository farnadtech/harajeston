<?php
/**
 * Test script for store auction tabs functionality
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Store Tabs</title>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);}";
echo "h2{color:#2563eb;border-bottom:2px solid #2563eb;padding-bottom:10px;}";
echo "a{display:inline-block;padding:10px 20px;margin:5px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;}";
echo "a:hover{background:#1d4ed8;}";
echo "table{width:100%;border-collapse:collapse;margin-top:10px;}";
echo "th,td{padding:10px;border:1px solid #ddd;text-align:right;}";
echo "th{background:#f0f0f0;}</style></head><body>";

echo "<h1>تست تب‌های حراج فروشگاه</h1>";

// Get a sample store
$store = \App\Models\Store::with('user')->first();

if (!$store) {
    echo "<div class='box'><p style='color:red;'>هیچ فروشگاهی یافت نشد!</p></div>";
    echo "</body></html>";
    exit;
}

echo "<div class='box'><h2>فروشگاه نمونه: {$store->store_name}</h2>";
echo "<p>نام کاربری: <strong>{$store->slug}</strong></p>";

// Count listings by status for this store
$activeCount = $store->listings()->where('status', 'active')->count();
$endedCount = $store->listings()->where('status', 'completed')->count();
$upcomingCount = $store->listings()->where('status', 'pending')->count();

echo "<table>";
echo "<tr><th>وضعیت</th><th>تعداد</th><th>لینک</th></tr>";
echo "<tr><td>حراج‌های فعال</td><td><strong>{$activeCount}</strong></td>";
echo "<td><a href='/stores/{$store->slug}?tab=active'>مشاهده</a></td></tr>";
echo "<tr><td>حراج‌های تمام شده</td><td><strong>{$endedCount}</strong></td>";
echo "<td><a href='/stores/{$store->slug}?tab=ended'>مشاهده</a></td></tr>";
echo "<tr><td>حراج‌های آینده</td><td><strong>{$upcomingCount}</strong></td>";
echo "<td><a href='/stores/{$store->slug}?tab=upcoming'>مشاهده</a></td></tr>";
echo "</table>";
echo "</div>";

// Show all stores
echo "<div class='box'><h2>تمام فروشگاه‌ها</h2>";
$stores = \App\Models\Store::with('user')->get();

echo "<table>";
echo "<tr><th>نام فروشگاه</th><th>فعال</th><th>تمام شده</th><th>آینده</th><th>عملیات</th></tr>";

foreach ($stores as $s) {
    $active = $s->listings()->where('status', 'active')->count();
    $ended = $s->listings()->where('status', 'completed')->count();
    $upcoming = $s->listings()->where('status', 'pending')->count();
    
    echo "<tr>";
    echo "<td>{$s->store_name}</td>";
    echo "<td>{$active}</td>";
    echo "<td>{$ended}</td>";
    echo "<td>{$upcoming}</td>";
    echo "<td><a href='/stores/{$s->slug}'>مشاهده فروشگاه</a></td>";
    echo "</tr>";
}
echo "</table></div>";

echo "</body></html>";
