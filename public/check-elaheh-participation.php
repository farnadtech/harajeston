<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find listing by slug
$listing = \App\Models\Listing::where('slug', 'frosh-fory')->first();
if (!$listing) {
    echo "آگهی با slug 'frosh-fory' یافت نشد!\n";
    exit;
}

echo "آگهی: {$listing->title} (ID: {$listing->id})\n";
echo "قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n\n";

// Find user
$user = \App\Models\User::where('name', 'LIKE', '%الهه%')->orWhere('name', 'LIKE', '%میرخزازی%')->first();
if (!$user) {
    echo "کاربر الهه میرخزازی یافت نشد!\n";
    exit;
}

echo "کاربر: {$user->name} (ID: {$user->id})\n";
echo "ایمیل: {$user->email}\n\n";

// Check participation
$participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->first();

if ($participation) {
    echo "✓ کاربر در حراجی شرکت کرده:\n";
    echo "  - وضعیت سپرده: {$participation->deposit_status}\n";
    echo "  - مبلغ سپرده: " . number_format($participation->deposit_amount) . " تومان\n";
    echo "  - تاریخ شرکت: {$participation->created_at}\n\n";
    
    // Calculate amounts
    $depositPaid = $participation->deposit_amount;
    $amountToPay = $listing->buy_now_price - $depositPaid;
    
    echo "محاسبات:\n";
    echo "  - قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
    echo "  - سپرده پرداخت شده: " . number_format($depositPaid) . " تومان\n";
    echo "  - مبلغ قابل پرداخت: " . number_format($amountToPay) . " تومان\n";
} else {
    echo "✗ کاربر در این حراجی شرکت نکرده است.\n";
}

// Check bids
$bids = \App\Models\Bid::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->get();

if ($bids->count() > 0) {
    echo "\nپیشنهادات کاربر:\n";
    foreach ($bids as $bid) {
        echo "  - مبلغ: " . number_format($bid->amount) . " تومان در {$bid->created_at}\n";
    }
}
