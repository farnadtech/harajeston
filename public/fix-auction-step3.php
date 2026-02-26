<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== مرحله 3: آزادسازی سپرده بازندگان ===\n\n";

$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->whereNotNull('required_deposit')
    ->where('required_deposit', '>', 0)
    ->get();

foreach ($endedListings as $listing) {
    echo "حراجی #{$listing->id}: {$listing->title}\n";
    
    // پیدا کردن همه شرکت‌کنندگان
    $allBidders = \App\Models\Bid::where('listing_id', $listing->id)
        ->select('user_id')
        ->distinct()
        ->pluck('user_id');
    
    echo "  شرکت‌کنندگان: {$allBidders->count()} نفر | برنده: User #{$listing->current_winner_id}\n";
    
    // آزادسازی سپرده بازندگان
    foreach ($allBidders as $userId) {
        if ($userId == $listing->current_winner_id) {
            echo "  - User #{$userId}: برنده (نگه داشته می‌شود)\n";
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
                    'description' => sprintf('کارمزد بازنده: %s', $listing->title),
                ]);
                
                // واریز کارمزد به سایت
                $siteUser = \App\Models\User::find(1);
                if ($siteUser) {
                    $siteWallet = $siteUser->wallet;
                    $siteWallet->balance += $fee;
                    $siteWallet->save();
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
                        'description' => sprintf('بازگشت سپرده: %s', $listing->title),
                    ]);
                }
                
                echo "  - User #{$userId}: آزاد شد (کارمزد: " . number_format($fee) . " - بازگشت: " . number_format($refundAmount) . ")\n";
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
                
                echo "  - User #{$userId}: آزاد شد (کامل: " . number_format($listing->required_deposit) . ")\n";
            }
        } else {
            echo "  - User #{$userId}: قبلاً آزاد شده\n";
        }
    }
    echo "\n";
}

echo "✓ مرحله 3 کامل شد\n";
