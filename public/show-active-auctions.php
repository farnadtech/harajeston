<?php
/**
 * Show active auctions and users for testing
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\User;
use App\Models\Bid;

echo "<h2>حراجی‌های فعال و کاربران</h2>";

$listings = Listing::where('status', 'active')->get();

foreach ($listings as $listing) {
    echo "<hr>";
    echo "<h3>{$listing->title} (ID: {$listing->id})</h3>";
    echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";
    
    $bids = Bid::where('listing_id', $listing->id)
        ->with('user.wallet')
        ->get()
        ->groupBy('user_id');
    
    echo "<h4>کاربران شرکت‌کننده:</h4>";
    
    if ($bids->isEmpty()) {
        echo "<p>هنوز کسی پیشنهاد نداده</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>کاربر</th><th>تعداد پیشنهاد</th><th>بالاترین پیشنهاد</th><th>موجودی</th><th>لینک تست</th></tr>";
        
        foreach ($bids as $userId => $userBids) {
            $user = $userBids->first()->user;
            $wallet = $user->wallet;
            $highestBid = $userBids->max('amount');
            $bidCount = $userBids->count();
            
            echo "<tr>";
            echo "<td>{$user->name} (ID: {$user->id})</td>";
            echo "<td>{$bidCount}</td>";
            echo "<td>" . number_format($highestBid) . " تومان</td>";
            echo "<td>" . number_format($wallet->balance) . " تومان</td>";
            echo "<td><a href='debug-bid-validation.php?listing_id={$listing->id}&user_id={$user->id}&amount=" . ($listing->current_price + 1000) . "' target='_blank'>تست</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    // Show users who haven't bid yet
    $usersWithoutBid = User::where('id', '!=', $listing->seller_id)
        ->whereHas('wallet')
        ->whereNotIn('id', $bids->keys())
        ->take(5)
        ->get();
    
    if ($usersWithoutBid->isNotEmpty()) {
        echo "<h4>کاربران بدون پیشنهاد (برای تست اولین پیشنهاد):</h4>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>کاربر</th><th>موجودی</th><th>لینک تست</th></tr>";
        
        foreach ($usersWithoutBid as $user) {
            $wallet = $user->wallet;
            echo "<tr>";
            echo "<td>{$user->name} (ID: {$user->id})</td>";
            echo "<td>" . number_format($wallet->balance) . " تومان</td>";
            echo "<td><a href='debug-bid-validation.php?listing_id={$listing->id}&user_id={$user->id}&amount=" . ($listing->current_price + 1000) . "' target='_blank'>تست</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
}
