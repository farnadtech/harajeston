<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== شروع اصلاح سیستم حراجی ===\n\n";

// 1. تنظیم مهلت پرداخت به 24 ساعت
echo "1. تنظیم مهلت پرداخت...\n";
\App\Models\SiteSetting::set('auction_payment_deadline_hours', 24);
echo "   ✓ مهلت پرداخت: 24 ساعت\n\n";

// 2. تنظیم درصد سپرده به 20%
echo "2. تنظیم درصد سپرده...\n";
\App\Models\SiteSetting::set('auction_deposit_percentage', 20);
echo "   ✓ درصد سپرده: 20%\n\n";

// 3. اصلاح حراجی‌های ended که مهلت اشتباه دارند
echo "3. اصلاح مهلت حراجی‌های ended...\n";
$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->whereNotNull('finalization_deadline')
    ->get();

foreach ($endedListings as $listing) {
    $correctDeadline = $listing->ends_at->addHours(24);
    if ($listing->finalization_deadline != $correctDeadline) {
        $listing->finalization_deadline = $correctDeadline;
        $listing->save();
        echo "   ✓ حراجی #{$listing->id}: مهلت از {$listing->finalization_deadline} به {$correctDeadline} تغییر کرد\n";
    }
}
echo "\n";

// 4. اصلاح required_deposit برای حراجی‌ها
echo "4. اصلاح مقدار سپرده حراجی‌ها...\n";
$listings = \App\Models\Listing::whereIn('status', ['active', 'ended', 'pending'])
    ->get();

foreach ($listings as $listing) {
    // محاسبه سپرده صحیح (20% از قیمت پایه)
    $correctDeposit = (int) ($listing->starting_price * 0.20);
    
    if ($listing->required_deposit != $correctDeposit) {
        $oldDeposit = $listing->required_deposit;
        $listing->required_deposit = $correctDeposit;
        $listing->save();
        echo "   ✓ حراجی #{$listing->id} ({$listing->title}): سپرده از " . number_format($oldDeposit) . " به " . number_format($correctDeposit) . " تومان تغییر کرد\n";
    }
}
echo "\n";

// 5. بررسی و اصلاح تراکنش‌های سپرده
echo "5. بررسی تراکنش‌های سپرده...\n";
$participations = \App\Models\AuctionParticipation::where('deposit_paid', true)
    ->with(['listing', 'user'])
    ->get();

foreach ($participations as $participation) {
    $listing = $participation->listing;
    $user = $participation->user;
    
    // بررسی وجود تراکنش freeze_deposit
    $hasTransaction = \App\Models\WalletTransaction::where('user_id', $user->id)
        ->where('reference_type', \App\Models\Listing::class)
        ->where('reference_id', $listing->id)
        ->where('type', 'freeze_deposit')
        ->exists();
    
    if (!$hasTransaction && $listing->required_deposit > 0) {
        echo "   ! شرکت‌کننده User #{$user->id} در حراجی #{$listing->id} تراکنش ندارد\n";
        echo "     سپرده مورد نیاز: " . number_format($listing->required_deposit) . " تومان\n";
        echo "     موجودی مسدود فعلی: " . number_format($user->wallet->frozen) . " تومان\n";
        
        // ثبت تراکنش برای سپرده
        $wallet = $user->wallet;
        \App\Models\WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'freeze_deposit',
            'amount' => $listing->required_deposit,
            'final_amount' => $listing->required_deposit,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance,
            'frozen_before' => $wallet->frozen - $listing->required_deposit,
            'frozen_after' => $wallet->frozen,
            'reference_type' => \App\Models\Listing::class,
            'reference_id' => $listing->id,
            'description' => sprintf('بلاک سپرده حراجی (اصلاح شده): %s', $listing->title),
        ]);
        echo "     ✓ تراکنش ثبت شد\n\n";
    }
}

// 6. آزادسازی سپرده بازندگان
echo "\n6. آزادسازی سپرده بازندگان...\n";
$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->whereNotNull('required_deposit')
    ->where('required_deposit', '>', 0)
    ->get();

foreach ($endedListings as $listing) {
    echo "   بررسی حراجی #{$listing->id}: {$listing->title}\n";
    
    // پیدا کردن همه شرکت‌کنندگان
    $allBidders = \App\Models\Bid::where('listing_id', $listing->id)
        ->select('user_id')
        ->distinct()
        ->pluck('user_id');
    
    echo "     تعداد شرکت‌کنندگان: {$allBidders->count()}\n";
    echo "     برنده: User #{$listing->current_winner_id}\n";
    
    // آزادسازی سپرده بازندگان
    foreach ($allBidders as $userId) {
        if ($userId == $listing->current_winner_id) {
            echo "     - User #{$userId}: برنده (سپرده نگه داشته می‌شود)\n";
            continue;
        }
        
        $user = \App\Models\User::find($userId);
        if (!$user) continue;
        
        // بررسی آیا سپرده آزاد شده یا نه
        $hasReleaseTransaction = \App\Models\WalletTransaction::where('user_id', $userId)
            ->where('reference_type', \App\Models\Listing::class)
            ->where('reference_id', $listing->id)
            ->where('type', 'release_deposit')
            ->exists();
        
        if (!$hasReleaseTransaction) {
            // آزادسازی سپرده
            $wallet = $user->wallet;
            
            // بررسی کارمزد بازندگان
            $loserFeeEnabled = \App\Models\SiteSetting::get('loser_fee_enabled', false);
            $loserFeePercentage = (float) \App\Models\SiteSetting::get('loser_fee_percentage', 0);
            
            if ($loserFeeEnabled && $loserFeePercentage > 0) {
                $fee = (int) ($listing->required_deposit * ($loserFeePercentage / 100));
                $refundAmount = $listing->required_deposit - $fee;
                
                // کسر کارمزد
                $wallet->frozen -= $fee;
                $wallet->save();
                
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $userId,
                    'type' => 'deduct_frozen',
                    'amount' => $fee,
                    'final_amount' => $fee,
                    'balance_before' => $wallet->balance,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen + $fee,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'description' => sprintf('کارمزد بازنده حراجی: %s', $listing->title),
                ]);
                
                // واریز کارمزد به سایت
                $siteUser = \App\Models\User::find(1);
                if ($siteUser) {
                    $siteWallet = $siteUser->wallet;
                    $siteWallet->balance += $fee;
                    $siteWallet->save();
                    
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $siteWallet->id,
                        'user_id' => 1,
                        'type' => 'commission',
                        'amount' => $fee,
                        'final_amount' => $fee,
                        'balance_before' => $siteWallet->balance - $fee,
                        'balance_after' => $siteWallet->balance,
                        'frozen_before' => $siteWallet->frozen,
                        'frozen_after' => $siteWallet->frozen,
                        'reference_type' => \App\Models\Listing::class,
                        'reference_id' => $listing->id,
                        'description' => sprintf('کارمزد بازنده حراجی: %s', $listing->title),
                    ]);
                }
                
                // آزادسازی مابقی
                if ($refundAmount > 0) {
                    $wallet->frozen -= $refundAmount;
                    $wallet->balance += $refundAmount;
                    $wallet->save();
                    
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => $userId,
                        'type' => 'release_deposit',
                        'amount' => $refundAmount,
                        'final_amount' => $refundAmount,
                        'balance_before' => $wallet->balance - $refundAmount,
                        'balance_after' => $wallet->balance,
                        'frozen_before' => $wallet->frozen + $refundAmount,
                        'frozen_after' => $wallet->frozen,
                        'reference_type' => \App\Models\Listing::class,
                        'reference_id' => $listing->id,
                        'description' => sprintf('بازگشت سپرده (پس از کسر کارمزد): %s', $listing->title),
                    ]);
                }
                
                echo "     - User #{$userId}: آزاد شد (کارمزد: " . number_format($fee) . " - بازگشت: " . number_format($refundAmount) . " تومان)\n";
            } else {
                // آزادسازی کامل بدون کارمزد
                $wallet->frozen -= $listing->required_deposit;
                $wallet->balance += $listing->required_deposit;
                $wallet->save();
                
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $userId,
                    'type' => 'release_deposit',
                    'amount' => $listing->required_deposit,
                    'final_amount' => $listing->required_deposit,
                    'balance_before' => $wallet->balance - $listing->required_deposit,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen + $listing->required_deposit,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'description' => sprintf('بازگشت سپرده: %s', $listing->title),
                ]);
                
                echo "     - User #{$userId}: آزاد شد (کامل: " . number_format($listing->required_deposit) . " تومان)\n";
            }
        } else {
            echo "     - User #{$userId}: قبلاً آزاد شده\n";
        }
    }
    echo "\n";
}

echo "\n=== اصلاح کامل شد ===\n";
echo "تنظیمات جدید:\n";
echo "- مهلت پرداخت: 24 ساعت\n";
echo "- درصد سپرده: 20%\n";
echo "- کارمزد بازندگان: " . \App\Models\SiteSetting::get('loser_fee_percentage', 0) . "%\n";
