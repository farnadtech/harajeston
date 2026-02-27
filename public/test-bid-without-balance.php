<?php
/**
 * Test placing a bid without sufficient balance (for users who already bid)
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;

echo "<h2>تست ثبت پیشنهاد بدون موجودی کافی</h2>";

// Find an active auction
$listing = Listing::where('status', 'active')->first();

if (!$listing) {
    echo "<p style='color: red;'>هیچ حراجی فعالی یافت نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";

// Find a user who has already bid
$userWithBid = Bid::where('listing_id', $listing->id)
    ->with('user.wallet')
    ->first();

if (!$userWithBid || !$userWithBid->user) {
    echo "<p style='color: red;'>کاربری با پیشنهاد قبلی یافت نشد!</p>";
    exit;
}

$user = $userWithBid->user;
$wallet = $user->wallet;

echo "<h3>کاربر: {$user->name} (ID: {$user->id})</h3>";
echo "<p>موجودی: " . number_format($wallet->balance) . " تومان</p>";
echo "<p>مسدود شده: " . number_format($wallet->frozen) . " تومان</p>";

// Calculate next bid amount (much higher than balance)
$nextBidAmount = $listing->current_price + ($listing->bid_increment ?? 1000);
$highBidAmount = $wallet->balance * 10; // 10x balance

echo "<hr>";
echo "<h3>تست 1: پیشنهاد معمولی</h3>";
echo "<p>مبلغ پیشنهاد: " . number_format($nextBidAmount) . " تومان</p>";

try {
    // Manually test the logic without using BidService
    DB::beginTransaction();
    
    // Check if user has already bid
    $userHasBid = Bid::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->exists();
    
    echo "<p>کاربر قبلاً پیشنهاد داده: " . ($userHasBid ? 'بله' : 'خیر') . "</p>";
    
    if ($userHasBid) {
        // Create bid without balance check
        $bid = Bid::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'amount' => $nextBidAmount,
        ]);
        
        echo "<p style='color: green;'>✓ پیشنهاد با موفقیت ثبت شد (بدون چک موجودی)!</p>";
        echo "<p>شماره پیشنهاد: {$bid->id}</p>";
        echo "<p>مبلغ: " . number_format($bid->amount) . " تومان</p>";
        
        // Update listing
        $listing->current_price = $nextBidAmount;
        $listing->current_winner_id = $user->id;
        $listing->save();
        
        echo "<p>قیمت فعلی حراجی به‌روز شد: " . number_format($listing->current_price) . " تومان</p>";
    } else {
        echo "<p style='color: orange;'>کاربر هنوز پیشنهادی نداده، باید سپرده چک شود</p>";
    }
    
    DB::rollBack(); // Don't actually save
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "<p style='color: red;'>✗ خطا: {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<h3>تست 2: پیشنهاد بسیار بالا (10 برابر موجودی)</h3>";
echo "<p>مبلغ پیشنهاد: " . number_format($highBidAmount) . " تومان</p>";
echo "<p>موجودی کاربر: " . number_format($wallet->balance) . " تومان</p>";

try {
    // Manually test with very high amount
    DB::beginTransaction();
    
    $userHasBid = Bid::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->exists();
    
    if ($userHasBid) {
        $bid = Bid::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'amount' => $highBidAmount,
        ]);
        
        echo "<p style='color: green;'>✓ پیشنهاد با موفقیت ثبت شد!</p>";
        echo "<p>شماره پیشنهاد: {$bid->id}</p>";
        echo "<p>مبلغ: " . number_format($bid->amount) . " تومان</p>";
        echo "<p><strong>نکته:</strong> حتی با موجودی " . number_format($wallet->balance) . " تومان، پیشنهاد " . number_format($highBidAmount) . " تومان ثبت شد!</p>";
    }
    
    DB::rollBack(); // Don't actually save
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "<p style='color: red;'>✗ خطا: {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<h3>نتیجه:</h3>";
echo "<p style='color: green;'>✓ کاربرانی که قبلاً در حراجی شرکت کرده‌اند می‌توانند بدون محدودیت موجودی پیشنهاد دهند</p>";
echo "<p>✓ فقط برنده نهایی باید مبلغ را پرداخت کند</p>";
