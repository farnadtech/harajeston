<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\AuctionParticipation;

echo "تست پاپ‌آپ خرید فوری...\n\n";

// کاربر الهه
$user = User::where('email', 'elaheh@example.com')->first();
if (!$user) {
    echo "❌ کاربر الهه یافت نشد!\n";
    exit;
}

echo "کاربر: {$user->name} (ID: {$user->id})\n";
echo "موجودی کیف پول: " . number_format($user->wallet->balance) . " تومان\n";
echo "مبلغ بلوک شده: " . number_format($user->wallet->frozen) . " تومان\n\n";

// حراجی‌های فعال با خرید فوری
$listings = Listing::where('status', 'active')
    ->whereNotNull('buy_now_price')
    ->where('buy_now_price', '>', 0)
    ->get();

echo "حراجی‌های فعال با خرید فوری:\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($listings as $listing) {
    echo "حراجی: {$listing->title} (ID: {$listing->id})\n";
    echo "قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
    echo "مبلغ سپرده: " . number_format($listing->deposit_amount) . " تومان\n";
    
    // چک کردن participation
    $participation = AuctionParticipation::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->where('deposit_status', 'paid')
        ->first();
    
    if ($participation) {
        echo "✓ کاربر در حراجی شرکت کرده\n";
        echo "  سپرده پرداخت شده: " . number_format($participation->deposit_amount) . " تومان\n";
        echo "  مبلغ قابل پرداخت: " . number_format($listing->buy_now_price - $participation->deposit_amount) . " تومان\n";
        echo "\n";
        echo "محاسبات پاپ‌آپ:\n";
        echo "  \$userParticipation = " . ($participation ? "object" : "null") . "\n";
        echo "  \$depositPaid = " . number_format($participation->deposit_amount) . "\n";
        echo "  \$amountToPay = " . number_format($listing->buy_now_price - $participation->deposit_amount) . "\n";
    } else {
        echo "✗ کاربر در حراجی شرکت نکرده\n";
        echo "  مبلغ قابل پرداخت: " . number_format($listing->buy_now_price) . " تومان\n";
        echo "\n";
        echo "محاسبات پاپ‌آپ:\n";
        echo "  \$userParticipation = null\n";
        echo "  \$depositPaid = 0\n";
        echo "  \$amountToPay = " . number_format($listing->buy_now_price) . "\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "\nبررسی همه participations کاربر:\n";
echo str_repeat("=", 80) . "\n\n";

$allParticipations = AuctionParticipation::where('user_id', $user->id)
    ->with('listing')
    ->get();

foreach ($allParticipations as $p) {
    echo "حراجی: {$p->listing->title} (ID: {$p->listing_id})\n";
    echo "مبلغ سپرده: " . number_format($p->deposit_amount) . " تومان\n";
    echo "وضعیت: {$p->deposit_status}\n";
    echo "تاریخ: {$p->created_at}\n\n";
}
