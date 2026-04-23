<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::find(39);
if ($listing) {
    echo "آگهی #39: {$listing->title}\n";
    echo "مبلغ سپرده: " . number_format($listing->deposit_amount) . " تومان\n";
    echo "قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n\n";
    
    // Check all participations
    $participations = \App\Models\AuctionParticipation::where('listing_id', 39)->get();
    echo "تعداد شرکت‌کنندگان: " . $participations->count() . "\n\n";
    
    foreach ($participations as $p) {
        echo "کاربر {$p->user->name} (ID: {$p->user_id}):\n";
        echo "  - وضعیت: {$p->deposit_status}\n";
        echo "  - مبلغ: " . number_format($p->deposit_amount) . " تومان\n";
        echo "  - تاریخ: {$p->created_at}\n\n";
    }
    
    // Check all bids
    $bids = \App\Models\Bid::where('listing_id', 39)->with('user')->get();
    echo "تعداد پیشنهادات: " . $bids->count() . "\n\n";
    
    foreach ($bids as $bid) {
        echo "پیشنهاد {$bid->user->name}: " . number_format($bid->amount) . " تومان در {$bid->created_at}\n";
    }
}
