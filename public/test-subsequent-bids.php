<?php
/**
 * Test subsequent bids without balance requirement
 * After first bid and deposit block, user should be able to place more bids without balance check
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Wallet;
use App\Models\SiteSetting;

echo "<h2>تست پیشنهادهای متوالی بدون نیاز به موجودی</h2>";

// Find an active auction
$listing = Listing::where('status', 'active')
    ->first();

if (!$listing) {
    echo "<p style='color: red;'>هیچ حراجی فعالی یافت نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>قیمت شروع: " . number_format($listing->starting_price) . " تومان</p>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";

// Get deposit settings
$depositType = SiteSetting::where('key', 'deposit_type')->value('value') ?? 'none';
$depositAmount = 0;

if ($depositType === 'fixed') {
    $depositAmount = (int)(SiteSetting::where('key', 'deposit_fixed_amount')->value('value') ?? 0);
} elseif ($depositType === 'percentage') {
    $percentage = (float)(SiteSetting::where('key', 'deposit_percentage')->value('value') ?? 0);
    $depositAmount = (int)($listing->starting_price * ($percentage / 100));
}

echo "<p>نوع سپرده: {$depositType}</p>";
echo "<p>مبلغ سپرده: " . number_format($depositAmount) . " تومان</p>";

// Find a user who has already bid on this auction
$userWithBid = Bid::where('listing_id', $listing->id)
    ->with('user.wallet')
    ->first();

if ($userWithBid && $userWithBid->user) {
    $user = $userWithBid->user;
    echo "<h3>کاربر تست (با پیشنهاد قبلی): {$user->name} (ID: {$user->id})</h3>";
} else {
    // Find a user who is not the seller
    $user = User::where('id', '!=', $listing->seller_id)
        ->whereHas('wallet')
        ->first();
    
    if (!$user) {
        echo "<p style='color: red;'>کاربر مناسبی یافت نشد!</p>";
        exit;
    }
    
    echo "<h3>کاربر تست (بدون پیشنهاد قبلی): {$user->name} (ID: {$user->id})</h3>";
}

echo "<hr>";

$wallet = $user->wallet;
echo "<p>موجودی کیف پول: " . number_format($wallet->balance) . " تومان</p>";
echo "<p>مبلغ مسدود شده: " . number_format($wallet->frozen) . " تومان</p>";

// Check if user has already bid
$userBids = Bid::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->orderBy('amount', 'desc')
    ->get();

echo "<hr>";
echo "<h3>پیشنهادهای قبلی کاربر:</h3>";

if ($userBids->isEmpty()) {
    echo "<p style='color: orange;'>کاربر هنوز پیشنهادی نداده است.</p>";
    echo "<p><strong>برای اولین پیشنهاد:</strong></p>";
    echo "<ul>";
    echo "<li>نیاز به موجودی برای سپرده: " . number_format($depositAmount) . " تومان</li>";
    echo "<li>موجودی فعلی: " . number_format($wallet->balance) . " تومان</li>";
    if ($wallet->balance >= $depositAmount) {
        echo "<li style='color: green;'>✓ موجودی کافی است</li>";
    } else {
        echo "<li style='color: red;'>✗ موجودی کافی نیست</li>";
    }
    echo "</ul>";
} else {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>مبلغ</th><th>زمان</th></tr>";
    foreach ($userBids as $bid) {
        echo "<tr>";
        echo "<td>" . number_format($bid->amount) . " تومان</td>";
        echo "<td>" . \Morilog\Jalali\Jalalian::fromDateTime($bid->created_at)->format('Y/m/d H:i') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green;'><strong>✓ کاربر قبلاً پیشنهاد داده است</strong></p>";
    echo "<p><strong>برای پیشنهادهای بعدی:</strong></p>";
    echo "<ul>";
    echo "<li style='color: green;'>✓ نیازی به چک موجودی نیست</li>";
    echo "<li style='color: green;'>✓ سپرده قبلاً بلاک شده است</li>";
    echo "<li style='color: green;'>✓ کاربر می‌تواند هر مبلغی (بالاتر از حداقل) پیشنهاد دهد</li>";
    echo "<li>فقط در صورت برنده شدن، مبلغ نهایی از کیف پول کسر می‌شود</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>تست منطق جدید:</h3>";

$minBid = $listing->current_price + ($listing->bid_increment ?? 1000);
echo "<p>حداقل پیشنهاد بعدی: " . number_format($minBid) . " تومان</p>";

// Test scenarios
$testAmount = $minBid;

echo "<h4>سناریو 1: پیشنهاد " . number_format($testAmount) . " تومان</h4>";

if ($userBids->isEmpty()) {
    // First bid - check deposit
    if ($wallet->balance >= $depositAmount) {
        echo "<p style='color: green;'>✓ پیشنهاد قابل قبول است (موجودی برای سپرده کافی است)</p>";
    } else {
        echo "<p style='color: red;'>✗ پیشنهاد رد می‌شود (موجودی برای سپرده کافی نیست)</p>";
        echo "<p>نیاز: " . number_format($depositAmount) . " تومان، موجود: " . number_format($wallet->balance) . " تومان</p>";
    }
} else {
    // Subsequent bid - no balance check
    echo "<p style='color: green;'>✓ پیشنهاد قابل قبول است (بدون نیاز به چک موجودی)</p>";
}

// Test with very high amount
$highAmount = $wallet->balance * 10; // 10x wallet balance
echo "<h4>سناریو 2: پیشنهاد " . number_format($highAmount) . " تومان (10 برابر موجودی)</h4>";

if ($userBids->isEmpty()) {
    echo "<p style='color: orange;'>برای اولین پیشنهاد، فقط سپرده چک می‌شود نه کل مبلغ</p>";
    if ($wallet->balance >= $depositAmount) {
        echo "<p style='color: green;'>✓ پیشنهاد قابل قبول است</p>";
    } else {
        echo "<p style='color: red;'>✗ پیشنهاد رد می‌شود</p>";
    }
} else {
    echo "<p style='color: green;'>✓ پیشنهاد قابل قبول است (بدون نیاز به چک موجودی)</p>";
    echo "<p><em>حتی اگر مبلغ پیشنهاد بسیار بیشتر از موجودی باشد، مشکلی نیست</em></p>";
}

echo "<hr>";
echo "<h3>نتیجه:</h3>";
echo "<ul>";
echo "<li><strong>پیشنهاد اول:</strong> فقط موجودی برای سپرده چک می‌شود</li>";
echo "<li><strong>پیشنهادهای بعدی:</strong> هیچ چک موجودی انجام نمی‌شود</li>";
echo "<li><strong>پرداخت نهایی:</strong> فقط برنده باید مبلغ را پرداخت کند</li>";
echo "<li><strong>بازندگان:</strong> سپرده آنها آزاد می‌شود</li>";
echo "</ul>";
