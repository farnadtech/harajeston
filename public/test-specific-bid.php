<?php
/**
 * Test specific bid scenario
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;

echo "<h2>تست پیشنهاد خاص</h2>";

// Test with listing 25, user 41, amount 150000
$listing = Listing::find(25);
$user = User::find(41);
$amount = 150000;

if (!$listing || !$user) {
    echo "<p style='color: red;'>حراجی یا کاربر یافت نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title} (ID: {$listing->id})</h3>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";

echo "<h3>کاربر: {$user->name} (ID: {$user->id})</h3>";
$wallet = $user->wallet;
echo "<p>موجودی: " . number_format($wallet->balance) . " تومان</p>";

echo "<h3>پیشنهاد: " . number_format($amount) . " تومان</h3>";

// Check if user has bid before
$userHasBid = Bid::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->exists();

echo "<p><strong>کاربر قبلاً پیشنهاد داده:</strong> " . ($userHasBid ? '<span style="color: green;">بله ✓</span>' : '<span style="color: red;">خیر ✗</span>') . "</p>";

if ($userHasBid) {
    $previousBids = Bid::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->orderBy('amount', 'desc')
        ->get();
    
    echo "<h4>پیشنهادهای قبلی:</h4>";
    echo "<ul>";
    foreach ($previousBids as $bid) {
        echo "<li>" . number_format($bid->amount) . " تومان - " . \Morilog\Jalali\Jalalian::fromDateTime($bid->created_at)->format('Y/m/d H:i') . "</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<h3>بررسی اعتبارسنجی PlaceBidRequest:</h3>";

// Check minimum bid
$highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
$increment = $listing->bid_increment ?? 1000;
$minAmount = $highestBid ? $highestBid->amount + $increment : $listing->starting_price;

echo "<p>حداقل مبلغ مجاز: " . number_format($minAmount) . " تومان</p>";

if ($amount < $minAmount) {
    echo "<p style='color: red;'>✗ مبلغ پیشنهاد کمتر از حداقل است</p>";
    exit;
} else {
    echo "<p style='color: green;'>✓ مبلغ پیشنهاد معتبر است</p>";
}

// Check deposit
$depositSetting = \App\Models\SiteSetting::where('key', 'deposit_type')->first();
$depositType = $depositSetting ? $depositSetting->value : 'none';

$depositAmount = 0;
if ($depositType === 'fixed') {
    $fixedSetting = \App\Models\SiteSetting::where('key', 'deposit_fixed_amount')->first();
    $depositAmount = $fixedSetting ? (int)$fixedSetting->value : 0;
} elseif ($depositType === 'percentage') {
    $percentageSetting = \App\Models\SiteSetting::where('key', 'deposit_percentage')->first();
    $percentage = $percentageSetting ? (float)$percentageSetting->value : 0;
    $depositAmount = (int)($listing->starting_price * ($percentage / 100));
}

echo "<p>نوع سپرده: {$depositType}</p>";
echo "<p>مبلغ سپرده: " . number_format($depositAmount) . " تومان</p>";

echo "<hr>";
echo "<h3>منطق جدید (بعد از اصلاح):</h3>";

if (!$userHasBid) {
    echo "<p style='color: orange;'>این اولین پیشنهاد کاربر است</p>";
    echo "<p>فقط موجودی برای سپرده چک می‌شود: " . number_format($depositAmount) . " تومان</p>";
    echo "<p>موجودی فعلی: " . number_format($wallet->balance) . " تومان</p>";
    
    if ($wallet->balance >= $depositAmount) {
        echo "<p style='color: green;'><strong>✓ نتیجه: پیشنهاد باید قبول شود</strong></p>";
    } else {
        echo "<p style='color: red;'><strong>✗ نتیجه: پیشنهاد رد می‌شود (موجودی برای سپرده کافی نیست)</strong></p>";
    }
} else {
    echo "<p style='color: green;'>✓ کاربر قبلاً پیشنهاد داده است</p>";
    echo "<p style='color: green;'>✓ هیچ چک موجودی انجام نمی‌شود</p>";
    echo "<p style='color: green;'><strong>✓ نتیجه: پیشنهاد باید بدون مشکل قبول شود</strong></p>";
    echo "<p><em>حتی اگر مبلغ پیشنهاد (" . number_format($amount) . " تومان) بیشتر از موجودی (" . number_format($wallet->balance) . " تومان) باشد</em></p>";
}
