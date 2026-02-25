<?php
/**
 * Test Outbid Notification System
 * 
 * این فایل برای تست سیستم اطلاع‌رسانی پیشنهاد بالاتر است
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Services\BidService;
use App\Services\NotificationService;

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>تست سیستم اطلاع‌رسانی پیشنهاد بالاتر</title>";
echo "<script src='https://cdn.tailwindcss.com'></script>";
echo "</head>";
echo "<body class='bg-gray-50 p-8'>";

echo "<div class='max-w-4xl mx-auto'>";
echo "<h1 class='text-3xl font-bold text-gray-900 mb-6'>تست سیستم اطلاع‌رسانی پیشنهاد بالاتر</h1>";

try {
    // پیدا کردن یک مزایده فعال
    $listing = Listing::where('status', 'active')
        ->whereNotNull('ends_at')
        ->where('ends_at', '>', now())
        ->with(['bids' => function($q) {
            $q->orderBy('amount', 'desc');
        }])
        ->first();
    
    if (!$listing) {
        echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4'>";
        echo "هیچ مزایده فعالی یافت نشد";
        echo "</div>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<div class='bg-white rounded-lg shadow p-6 mb-6'>";
    echo "<h2 class='text-xl font-bold mb-4'>اطلاعات مزایده</h2>";
    echo "<p><strong>عنوان:</strong> {$listing->title}</p>";
    echo "<p><strong>قیمت شروع:</strong> " . number_format($listing->starting_price) . " تومان</p>";
    echo "<p><strong>بالاترین پیشنهاد فعلی:</strong> " . number_format($listing->current_highest_bid ?? $listing->starting_price) . " تومان</p>";
    echo "<p><strong>تعداد پیشنهادات:</strong> {$listing->bids->count()}</p>";
    echo "</div>";
    
    // نمایش پیشنهادات موجود
    if ($listing->bids->count() > 0) {
        echo "<div class='bg-white rounded-lg shadow p-6 mb-6'>";
        echo "<h2 class='text-xl font-bold mb-4'>پیشنهادات موجود</h2>";
        echo "<div class='space-y-2'>";
        foreach ($listing->bids->take(5) as $index => $bid) {
            $badge = $index === 0 ? "<span class='bg-green-500 text-white text-xs px-2 py-1 rounded'>بالاترین</span>" : "";
            echo "<div class='flex items-center justify-between p-3 bg-gray-50 rounded'>";
            echo "<div>";
            echo "<span class='font-bold'>{$bid->user->name}</span>";
            echo " - " . number_format($bid->amount) . " تومان";
            echo "</div>";
            echo "<div>{$badge}</div>";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";
    }
    
    // بررسی نوتیفیکیشن‌های outbid
    $outbidNotifications = \App\Models\Notification::where('type', 'outbid')
        ->where('message', 'like', '%' . $listing->title . '%')
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "<div class='bg-white rounded-lg shadow p-6 mb-6'>";
    echo "<h2 class='text-xl font-bold mb-4'>نوتیفیکیشن‌های Outbid</h2>";
    
    if ($outbidNotifications->count() > 0) {
        echo "<div class='space-y-3'>";
        foreach ($outbidNotifications as $notif) {
            $readBadge = $notif->is_read ? 
                "<span class='bg-gray-500 text-white text-xs px-2 py-1 rounded'>خوانده شده</span>" : 
                "<span class='bg-orange-500 text-white text-xs px-2 py-1 rounded'>جدید</span>";
            
            echo "<div class='border border-gray-200 rounded p-4'>";
            echo "<div class='flex items-center justify-between mb-2'>";
            echo "<span class='font-bold'>{$notif->user->name}</span>";
            echo $readBadge;
            echo "</div>";
            echo "<p class='text-sm text-gray-600 mb-1'><strong>{$notif->title}</strong></p>";
            echo "<p class='text-sm text-gray-700'>{$notif->message}</p>";
            echo "<p class='text-xs text-gray-500 mt-2'>" . $notif->created_at->diffForHumans() . "</p>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p class='text-gray-500'>هنوز نوتیفیکیشن outbid ثبت نشده است</p>";
    }
    echo "</div>";
    
    // راهنمای تست
    echo "<div class='bg-blue-50 border border-blue-200 rounded-lg p-6'>";
    echo "<h2 class='text-xl font-bold mb-4'>راهنمای تست</h2>";
    echo "<ol class='list-decimal list-inside space-y-2 text-gray-700'>";
    echo "<li>با دو کاربر مختلف وارد سیستم شوید</li>";
    echo "<li>کاربر اول یک پیشنهاد روی این مزایده ثبت کند</li>";
    echo "<li>کاربر دوم یک پیشنهاد بالاتر ثبت کند</li>";
    echo "<li>کاربر اول باید نوتیفیکیشن دریافت کند که پیشنهاد بالاتری ثبت شده</li>";
    echo "<li>این صفحه را رفرش کنید تا نوتیفیکیشن‌ها را ببینید</li>";
    echo "</ol>";
    echo "<div class='mt-4'>";
    echo "<a href='" . route('listings.show', $listing) . "' class='inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600'>مشاهده مزایده</a>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
    echo "<strong>خطا:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
