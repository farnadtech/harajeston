<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== تست نهایی سیستم حراجی ===\n\n";

// بررسی تنظیمات
echo "1. تنظیمات سیستم:\n";
$deadlineHours = \App\Models\SiteSetting::get('auction_payment_deadline_hours', 24);
$depositPercentage = \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
$loserFeeEnabled = \App\Models\SiteSetting::get('loser_fee_enabled', false);
$loserFeePercentage = \App\Models\SiteSetting::get('loser_fee_percentage', 0);

echo "   - مهلت پرداخت: {$deadlineHours} ساعت\n";
echo "   - درصد سپرده: {$depositPercentage}%\n";
echo "   - کارمزد بازندگان: " . ($loserFeeEnabled ? "فعال ({$loserFeePercentage}%)" : "غیرفعال") . "\n\n";

// بررسی حراجی ended
echo "2. حراجی‌های ended:\n";
$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->orderBy('ends_at', 'desc')
    ->get();

foreach ($endedListings as $listing) {
    echo "\n   حراجی #{$listing->id}: {$listing->title}\n";
    echo "   قیمت پایه: " . number_format($listing->starting_price) . " تومان\n";
    
    $expectedDeposit = (int) ($listing->starting_price * ($depositPercentage / 100));
    echo "   سپرده محاسبه شده: " . number_format($expectedDeposit) . " تومان\n";
    
    $winner = \App\Models\User::find($listing->current_winner_id);
    $winningBid = \App\Models\Bid::where('listing_id', $listing->id)
        ->where('user_id', $winner->id)
        ->orderBy('amount', 'desc')
        ->first();
    
    echo "   برنده: User #{$winner->id}\n";
    echo "   مبلغ برنده شده: " . number_format($winningBid->amount) . " تومان\n";
    echo "   مبلغ باقیمانده: " . number_format($winningBid->amount - $expectedDeposit) . " تومان\n";
    
    $expectedDeadline = $listing->ends_at->addHours($deadlineHours);
    echo "   مهلت پرداخت: {$listing->finalization_deadline}\n";
    echo "   مهلت محاسبه شده: {$expectedDeadline}\n";
    
    if ($listing->finalization_deadline == $expectedDeadline) {
        echo "   ✓ مهلت صحیح است\n";
    } else {
        echo "   ✗ مهلت اشتباه است\n";
    }
    
    // بررسی کیف پول برنده
    $wallet = $winner->wallet;
    echo "   کیف پول برنده:\n";
    echo "     - موجودی: " . number_format($wallet->balance) . " تومان\n";
    echo "     - مسدود شده: " . number_format($wallet->frozen) . " تومان\n";
    
    // بررسی تراکنش‌های سپرده
    $depositTx = \App\Models\WalletTransaction::where('user_id', $winner->id)
        ->where('reference_type', \App\Models\Listing::class)
        ->where('reference_id', $listing->id)
        ->where('type', 'freeze_deposit')
        ->first();
    
    if ($depositTx) {
        echo "     ✓ تراکنش سپرده ثبت شده: " . number_format($depositTx->amount) . " تومان\n";
    } else {
        echo "     ✗ تراکنش سپرده ثبت نشده\n";
    }
    
    // بررسی بازندگان
    $allBidders = \App\Models\Bid::where('listing_id', $listing->id)
        ->select('user_id')
        ->distinct()
        ->pluck('user_id');
    
    $losers = $allBidders->filter(fn($id) => $id != $winner->id);
    echo "   بازندگان: {$losers->count()} نفر\n";
    
    foreach ($losers as $loserId) {
        $loser = \App\Models\User::find($loserId);
        $releaseT = \App\Models\WalletTransaction::where('user_id', $loserId)
            ->where('reference_type', \App\Models\Listing::class)
            ->where('reference_id', $listing->id)
            ->where('type', 'release_deposit')
            ->first();
        
        if ($releaseT) {
            echo "     ✓ User #{$loserId}: سپرده آزاد شده (" . number_format($releaseT->amount) . " تومان)\n";
        } else {
            echo "     ✗ User #{$loserId}: سپرده آزاد نشده\n";
        }
    }
}

echo "\n=== تست کامل شد ===\n";
