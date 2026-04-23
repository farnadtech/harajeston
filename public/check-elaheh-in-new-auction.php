<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find listing 42
$listing = \App\Models\Listing::find(42);
if (!$listing) {
    echo "آگهی #42 یافت نشد!\n";
    exit;
}

echo "آگهی #42: {$listing->title}\n";
echo "مبلغ سپرده: " . number_format($listing->deposit_amount) . " تومان\n";
echo "قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n\n";

// Find user
$user = \App\Models\User::where('name', 'LIKE', '%الهه%')->first();
if (!$user) {
    echo "کاربر الهه یافت نشد!\n";
    exit;
}

echo "کاربر: {$user->name} (ID: {$user->id})\n\n";

// Check participation
$participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->first();

if ($participation) {
    echo "✓ کاربر در حراجی شرکت کرده:\n";
    echo "  - وضعیت سپرده: {$participation->deposit_status}\n";
    echo "  - مبلغ سپرده: " . number_format($participation->deposit_amount) . " تومان\n";
    echo "  - تاریخ: {$participation->created_at}\n\n";
    
    echo "محاسبات برای خرید فوری:\n";
    echo "  - قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
    echo "  - سپرده پرداخت شده: " . number_format($participation->deposit_amount) . " تومان\n";
    echo "  - مبلغ قابل پرداخت: " . number_format($listing->buy_now_price - $participation->deposit_amount) . " تومان\n";
} else {
    echo "✗ کاربر در این حراجی شرکت نکرده است.\n";
    echo "برای تست، ابتدا باید در حراجی شرکت کند (سپرده پرداخت کند).\n";
}

// Check wallet
$wallet = $user->wallet;
if ($wallet) {
    echo "\nموجودی کیف پول:\n";
    echo "  - موجودی آزاد: " . number_format($wallet->balance) . " تومان\n";
    echo "  - موجودی بلاک شده: " . number_format($wallet->frozen) . " تومان\n";
}
