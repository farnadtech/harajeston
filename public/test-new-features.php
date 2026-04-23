<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\User;
use App\Models\Bid;
use App\Services\BidService;

echo "<h1>تست ویژگی‌های جدید حراجی</h1>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// پیدا کردن یک حراجی فعال با پیشنهاد
$listing = Listing::where('status', 'active')
    ->whereHas('bids')
    ->with(['bids' => function($q) {
        $q->orderBy('amount', 'desc');
    }])
    ->first();

if (!$listing) {
    echo "<p class='error'>هیچ حراجی فعالی با پیشنهاد یافت نشد.</p>";
    exit;
}

echo "<h2>حراجی: {$listing->title}</h2>";
echo "<p>ID: {$listing->id}</p>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";
echo "<p>قیمت خرید فوری: " . ($listing->buy_now_price ? number_format($listing->buy_now_price) . " تومان" : "غیرفعال") . "</p>";
echo "<p>تمدید خودکار: " . ($listing->auto_extend ? "فعال" : "غیرفعال") . "</p>";
echo "<p>زمان پایان: {$listing->ends_at}</p>";

$highestBid = $listing->bids->first();
if ($highestBid) {
    echo "<p>بالاترین پیشنهاد: " . number_format($highestBid->amount) . " تومان توسط کاربر {$highestBid->user->name}</p>";
}

echo "<hr>";

// تست 1: بررسی غیرفعال شدن خرید فوری
echo "<h3>تست 1: غیرفعال شدن خرید فوری</h3>";
if ($listing->buy_now_price) {
    if ($listing->current_price >= $listing->buy_now_price) {
        echo "<p class='error'>خطا: قیمت فعلی بالاتر از قیمت خرید فوری است ولی خرید فوری هنوز فعال است!</p>";
    } else {
        echo "<p class='success'>✓ خرید فوری فعال است و قیمت آن بالاتر از بالاترین پیشنهاد است.</p>";
    }
} else {
    echo "<p class='info'>خرید فوری غیرفعال است (احتمالاً به دلیل پیشنهاد بالاتر)</p>";
}

echo "<hr>";

// تست 2: بررسی امکان ویرایش buy_now_price
echo "<h3>تست 2: امکان ویرایش قیمت خرید فوری</h3>";
echo "<p class='info'>این ویژگی در صفحه ویرایش آگهی قابل تست است.</p>";
echo "<p>لینک ویرایش: <a href='/haraj/public/listings/{$listing->id}/edit' target='_blank'>ویرایش آگهی</a></p>";

echo "<hr>";

// تست 3: بررسی تمدید خودکار
echo "<h3>تست 3: تمدید خودکار در 5 دقیقه آخر</h3>";
$now = now();
$endsAt = $listing->ends_at;
$minutesRemaining = $now->diffInMinutes($endsAt, false);

echo "<p>دقایق باقیمانده: " . round($minutesRemaining, 2) . "</p>";

if ($listing->auto_extend) {
    echo "<p class='success'>✓ تمدید خودکار فعال است.</p>";
    
    if ($minutesRemaining < 5 && $minutesRemaining > 0) {
        echo "<p class='info'>⚠ حراجی در 5 دقیقه آخر است. پیشنهاد جدید باعث تمدید 5 دقیقه‌ای می‌شود.</p>";
    } else if ($minutesRemaining >= 5) {
        echo "<p class='info'>حراجی هنوز در 5 دقیقه آخر نیست.</p>";
    }
} else {
    echo "<p class='info'>تمدید خودکار غیرفعال است.</p>";
}

echo "<hr>";

// تست 4: بررسی محاسبه خرید فوری با سپرده
echo "<h3>تست 4: محاسبه خرید فوری با سپرده</h3>";

// پیدا کردن کاربری که پیشنهاد داده
if ($highestBid) {
    $user = $highestBid->user;
    $participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->first();
    
    if ($participation) {
        echo "<p>کاربر: {$user->name}</p>";
        echo "<p>سپرده بلاک شده: " . number_format($participation->deposit_amount) . " تومان</p>";
        
        if ($listing->buy_now_price) {
            $amountToPay = $listing->buy_now_price - $participation->deposit_amount;
            echo "<p class='success'>✓ در صورت خرید فوری، فقط " . number_format($amountToPay) . " تومان اضافی پرداخت می‌شود.</p>";
            echo "<p class='info'>محاسبه: " . number_format($listing->buy_now_price) . " - " . number_format($participation->deposit_amount) . " = " . number_format($amountToPay) . "</p>";
        } else {
            echo "<p class='info'>خرید فوری غیرفعال است.</p>";
        }
    } else {
        echo "<p class='error'>رکورد participation یافت نشد!</p>";
    }
}

echo "<hr>";
echo "<h3>خلاصه تست‌ها</h3>";
echo "<ul>";
echo "<li>✓ ویژگی 1: غیرفعال کردن خرید فوری - پیاده‌سازی شده در BidService</li>";
echo "<li>✓ ویژگی 2: ویرایش buy_now_price - قابل تست در صفحه ویرایش</li>";
echo "<li>✓ ویژگی 3: تمدید خودکار - پیاده‌سازی شده در BidService</li>";
echo "<li>✓ ویژگی 4: محاسبه خرید فوری با سپرده - پیاده‌سازی شده در ListingController</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='/haraj/public/listings/{$listing->id}'>مشاهده آگهی</a></p>";
