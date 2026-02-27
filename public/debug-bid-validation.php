<?php
/**
 * Debug bid validation to see what's happening
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Wallet;
use App\Http\Requests\PlaceBidRequest;
use Illuminate\Support\Facades\Validator;

echo "<h2>دیباگ اعتبارسنجی پیشنهاد</h2>";

// Get listing ID from query string
$listingId = $_GET['listing_id'] ?? null;
$userId = $_GET['user_id'] ?? null;
$amount = $_GET['amount'] ?? null;

if (!$listingId || !$userId || !$amount) {
    echo "<p style='color: red;'>لطفاً پارامترها را وارد کنید:</p>";
    echo "<p>?listing_id=X&user_id=Y&amount=Z</p>";
    
    // Show available listings
    $listings = Listing::where('status', 'active')->get();
    echo "<h3>حراجی‌های فعال:</h3>";
    foreach ($listings as $listing) {
        echo "<p>ID: {$listing->id} - {$listing->title}</p>";
    }
    
    exit;
}

$listing = Listing::find($listingId);
$user = User::find($userId);

if (!$listing || !$user) {
    echo "<p style='color: red;'>حراجی یا کاربر یافت نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";

echo "<h3>کاربر: {$user->name}</h3>";
$wallet = $user->wallet;
echo "<p>موجودی: " . number_format($wallet->balance) . " تومان</p>";

echo "<h3>پیشنهاد: " . number_format($amount) . " تومان</h3>";

// Check if user has bid before
$userHasBid = Bid::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->exists();

echo "<p>کاربر قبلاً پیشنهاد داده: " . ($userHasBid ? '<strong style="color: green;">بله</strong>' : '<strong style="color: red;">خیر</strong>') . "</p>";

if ($userHasBid) {
    $userBids = Bid::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->orderBy('amount', 'desc')
        ->get();
    
    echo "<h4>پیشنهادهای قبلی:</h4>";
    echo "<ul>";
    foreach ($userBids as $bid) {
        echo "<li>" . number_format($bid->amount) . " تومان</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<h3>بررسی اعتبارسنجی:</h3>";

// Simulate validation
$highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
$increment = $listing->bid_increment ?? 1000;
$minAmount = $highestBid ? $highestBid->amount + $increment : $listing->starting_price;

echo "<p>حداقل مبلغ مجاز: " . number_format($minAmount) . " تومان</p>";

if ($amount < $minAmount) {
    echo "<p style='color: red;'>✗ مبلغ پیشنهاد کمتر از حداقل است</p>";
} else {
    echo "<p style='color: green;'>✓ مبلغ پیشنهاد معتبر است</p>";
}

// Check deposit settings
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
echo "<h3>چک موجودی:</h3>";

if (!$userHasBid) {
    echo "<p style='color: orange;'>این اولین پیشنهاد کاربر است</p>";
    echo "<p>نیاز به موجودی برای سپرده: " . number_format($depositAmount) . " تومان</p>";
    echo "<p>موجودی فعلی: " . number_format($wallet->balance) . " تومان</p>";
    
    if ($wallet->balance >= $depositAmount) {
        echo "<p style='color: green;'>✓ موجودی برای سپرده کافی است</p>";
    } else {
        echo "<p style='color: red;'>✗ موجودی برای سپرده کافی نیست</p>";
        echo "<p><strong>خطا:</strong> موجودی کیف پول شما برای پرداخت سپرده کافی نیست</p>";
    }
} else {
    echo "<p style='color: green;'>✓ کاربر قبلاً پیشنهاد داده است</p>";
    echo "<p style='color: green;'>✓ نیازی به چک موجودی نیست</p>";
    echo "<p style='color: green;'>✓ سپرده قبلاً بلاک شده است</p>";
    echo "<p><strong>نتیجه:</strong> پیشنهاد باید بدون مشکل ثبت شود</p>";
}

echo "<hr>";
echo "<h3>تست با مقادیر مختلف:</h3>";
echo "<ul>";
echo "<li><a href='?listing_id={$listingId}&user_id={$userId}&amount=" . ($minAmount) . "'>پیشنهاد حداقل: " . number_format($minAmount) . "</a></li>";
echo "<li><a href='?listing_id={$listingId}&user_id={$userId}&amount=" . ($minAmount + 10000) . "'>پیشنهاد +10,000: " . number_format($minAmount + 10000) . "</a></li>";
echo "<li><a href='?listing_id={$listingId}&user_id={$userId}&amount=" . ($wallet->balance * 10) . "'>پیشنهاد 10x موجودی: " . number_format($wallet->balance * 10) . "</a></li>";
echo "</ul>";
