<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\User;

echo "<h2>بررسی آگهی‌های تعلیق شده</h2>";

// Find suspended listings
$suspendedListings = Listing::where('status', 'suspended')->get();

echo "<h3>تعداد آگهی‌های تعلیق شده: " . $suspendedListings->count() . "</h3>";

if ($suspendedListings->count() > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>عنوان</th><th>فروشنده</th><th>وضعیت</th><th>دلیل رد</th><th>لینک ویرایش</th></tr>";
    foreach ($suspendedListings as $listing) {
        echo "<tr>";
        echo "<td>{$listing->id}</td>";
        echo "<td>{$listing->title}</td>";
        echo "<td>{$listing->seller->name}</td>";
        echo "<td><strong style='color: red;'>{$listing->status}</strong></td>";
        echo "<td>" . ($listing->rejection_reason ?? 'ندارد') . "</td>";
        echo "<td><a href='/listings/{$listing->id}/edit' target='_blank'>ویرایش</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>هیچ آگهی تعلیق شده‌ای وجود ندارد.</p>";
    echo "<p>برای تست، یک آگهی را تعلیق می‌کنیم...</p>";
    
    // Find a listing to suspend
    $listing = Listing::where('status', '!=', 'suspended')->first();
    if ($listing) {
        $listing->update([
            'status' => 'suspended',
            'rejection_reason' => 'تست سیستم - این آگهی برای تست تعلیق شده است'
        ]);
        echo "<p style='color: green;'>✓ آگهی #{$listing->id} تعلیق شد</p>";
        echo "<p><a href='/listings/{$listing->id}/edit' target='_blank'>مشاهده صفحه ویرایش</a></p>";
    }
}

echo "<hr>";
echo "<h3>تست دکمه ارسال مجدد:</h3>";
echo "<p>اگر آگهی تعلیق شده دارید، در صفحه ویرایش باید دکمه سبز رنگ 'ویرایش و ارسال مجدد برای تایید' نشان داده شود.</p>";
