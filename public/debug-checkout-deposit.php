<?php
// Debug checkout deposit issue

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<h2>دیباگ مشکل سپرده در checkout</h2>";

// Get the listing
$listingId = $_GET['listing_id'] ?? null;

if (!$listingId) {
    echo "<p>لطفاً listing_id را در URL مشخص کنید: ?listing_id=X</p>";
    
    // Show ended listings
    $endedListings = \App\Models\Listing::where('status', 'ended')
        ->with('bids')
        ->get();
    
    echo "<h3>آگهی‌های تمام شده:</h3>";
    foreach ($endedListings as $listing) {
        echo "<p><a href='?listing_id={$listing->id}'>{$listing->title} (ID: {$listing->id})</a></p>";
    }
    exit;
}

$listing = \App\Models\Listing::with(['bids', 'auctionParticipations'])->find($listingId);

if (!$listing) {
    echo "<p style='color: red;'>آگهی پیدا نشد!</p>";
    exit;
}

echo "<h3>آگهی: {$listing->title}</h3>";
echo "<p>وضعیت: {$listing->status}</p>";
echo "<p>قیمت پایه: " . number_format($listing->starting_price) . " تومان</p>";
echo "<p>برنده: User ID {$listing->current_winner_id}</p>";

// Get winning bid
$winningBid = $listing->bids()
    ->where('user_id', $listing->current_winner_id)
    ->orderBy('amount', 'desc')
    ->first();

if ($winningBid) {
    echo "<p>مبلغ برنده شده: " . number_format($winningBid->amount) . " تومان</p>";
} else {
    echo "<p style='color: red;'>پیشنهاد برنده یافت نشد!</p>";
}

// Check participation
$participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
    ->where('user_id', $listing->current_winner_id)
    ->first();

echo "<hr>";
echo "<h3>بررسی Participation:</h3>";

if ($participation) {
    echo "<p style='color: green;'>✓ Participation وجود دارد</p>";
    echo "<p>مبلغ سپرده: " . number_format($participation->deposit_amount) . " تومان</p>";
    echo "<p>وضعیت سپرده: {$participation->deposit_status}</p>";
    echo "<p>تاریخ ایجاد: {$participation->created_at}</p>";
} else {
    echo "<p style='color: red;'>✗ Participation وجود ندارد!</p>";
    echo "<p>این یعنی کاربر بدون پرداخت سپرده برنده شده (مثلاً تنها شرکت‌کننده بوده)</p>";
}

// Check all participations for this listing
echo "<hr>";
echo "<h3>همه Participations این آگهی:</h3>";
$allParticipations = \App\Models\AuctionParticipation::where('listing_id', $listing->id)->get();

if ($allParticipations->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>User ID</th><th>مبلغ سپرده</th><th>وضعیت</th><th>تاریخ</th></tr>";
    foreach ($allParticipations as $p) {
        $highlight = $p->user_id == $listing->current_winner_id ? 'background: yellow;' : '';
        echo "<tr style='{$highlight}'>";
        echo "<td>{$p->user_id}</td>";
        echo "<td>" . number_format($p->deposit_amount) . "</td>";
        echo "<td>{$p->deposit_status}</td>";
        echo "<td>{$p->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>هیچ participation وجود ندارد</p>";
}

// Calculate what deposit should be
echo "<hr>";
echo "<h3>محاسبه سپرده:</h3>";
$depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
$calculatedDeposit = (int) ($listing->starting_price * ($depositPercentage / 100));

echo "<p>درصد سپرده از تنظیمات: {$depositPercentage}%</p>";
echo "<p>سپرده محاسبه شده: " . number_format($calculatedDeposit) . " تومان</p>";

if ($participation) {
    if ($participation->deposit_amount == $calculatedDeposit) {
        echo "<p style='color: green;'>✓ مبلغ سپرده صحیح است</p>";
    } else {
        echo "<p style='color: orange;'>⚠ مبلغ سپرده با محاسبه فعلی متفاوت است</p>";
        echo "<p>سپرده ذخیره شده: " . number_format($participation->deposit_amount) . "</p>";
        echo "<p>سپرده محاسبه شده: " . number_format($calculatedDeposit) . "</p>";
    }
}

// Solution
echo "<hr>";
echo "<h3>راه‌حل:</h3>";
if (!$participation) {
    echo "<p style='color: red;'>مشکل: این آگهی participation ندارد!</p>";
    echo "<p>دلایل احتمالی:</p>";
    echo "<ul>";
    echo "<li>آگهی قبل از پیاده‌سازی سیستم سپرده ایجاد شده</li>";
    echo "<li>کاربر به صورت دستی برنده شده (توسط ادمین)</li>";
    echo "<li>باگ در سیستم ثبت participation</li>";
    echo "</ul>";
    
    echo "<p><strong>راه‌حل پیشنهادی:</strong></p>";
    echo "<p>در کنترلر checkout، اگر participation وجود نداشت، سپرده را 0 در نظر بگیریم</p>";
    echo "<pre>
\$depositAmount = \$participation ? \$participation->deposit_amount : 0;
</pre>";
}
