<?php
/**
 * Test real bid through API/Controller
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Http\Requests\PlaceBidRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

echo "<h2>تست واقعی API پیشنهاد</h2>";

// Login as user 41
$user = User::find(41);
Auth::login($user);

echo "<h3>کاربر: {$user->name} (ID: {$user->id})</h3>";
echo "<p>موجودی: " . number_format($user->wallet->balance) . " تومان</p>";

// Test data
$listingId = 25;
$amount = 150000;

$listing = Listing::find($listingId);
echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";

echo "<h3>پیشنهاد: " . number_format($amount) . " تومان</h3>";

echo "<hr>";
echo "<h3>تست اعتبارسنجی:</h3>";

// Create validator manually
$data = [
    'listing_id' => $listingId,
    'amount' => $amount,
];

$rules = [
    'listing_id' => 'required|exists:listings,id',
    'amount' => 'required|numeric|min:1000',
];

$validator = Validator::make($data, $rules);

// Add custom validation (same as PlaceBidRequest)
$validator->after(function ($validator) use ($listingId, $amount) {
    if ($listingId) {
        $listing = \App\Models\Listing::find($listingId);
        
        if ($listing) {
            $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
            $increment = $listing->bid_increment ?? 1000;
            $minAmount = $highestBid ? $highestBid->amount + $increment : $listing->starting_price;
            
            if ($amount < $minAmount) {
                $validator->errors()->add('amount', 'مبلغ پیشنهاد باید حداقل ' . number_format($minAmount) . ' تومان باشد.');
                return;
            }
            
            // Check wallet balance only for first bid (deposit requirement)
            $user = auth()->user();
            $wallet = $user->wallet;
            $balance = $wallet ? $wallet->balance : 0;
            
            // Check if user has already bid in this auction
            $userHasBid = $listing->bids()->where('user_id', $user->id)->exists();
            
            echo "<p>کاربر قبلاً پیشنهاد داده: " . ($userHasBid ? '<strong style="color: green;">بله</strong>' : '<strong style="color: red;">خیر</strong>') . "</p>";
            
            // Only check balance for first bid (when deposit needs to be blocked)
            if (!$userHasBid) {
                // Get deposit from site settings
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
                
                echo "<p>نیاز به موجودی برای سپرده: " . number_format($depositAmount) . " تومان</p>";
                
                // For first bid, only check if user has enough for deposit
                if ($depositAmount > 0 && $balance < $depositAmount) {
                    $validator->errors()->add('amount', 'موجودی کیف پول شما برای پرداخت سپرده کافی نیست.');
                }
            } else {
                echo "<p style='color: green;'>✓ نیازی به چک موجودی نیست (کاربر قبلاً پیشنهاد داده)</p>";
            }
        }
    }
});

if ($validator->fails()) {
    echo "<p style='color: red;'><strong>✗ اعتبارسنجی ناموفق:</strong></p>";
    echo "<ul>";
    foreach ($validator->errors()->all() as $error) {
        echo "<li style='color: red;'>{$error}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green;'><strong>✓ اعتبارسنجی موفق!</strong></p>";
    echo "<p>پیشنهاد می‌تواند ثبت شود.</p>";
}
