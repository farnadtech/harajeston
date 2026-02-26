<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== اصلاح تراکنش‌های سپرده گمشده ===\n\n";

$depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);

// پیدا کردن حراجی‌های ended که برنده دارند
$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->get();

foreach ($endedListings as $listing) {
    echo "بررسی حراجی #{$listing->id}: {$listing->title}\n";
    
    $winner = \App\Models\User::find($listing->current_winner_id);
    if (!$winner) {
        echo "  ✗ برنده یافت نشد\n\n";
        continue;
    }
    
    $depositAmount = (int) ($listing->starting_price * ($depositPercentage / 100));
    
    // بررسی وجود تراکنش
    $hasTransaction = \App\Models\WalletTransaction::where('user_id', $winner->id)
        ->where('reference_type', \App\Models\Listing::class)
        ->where('reference_id', $listing->id)
        ->where('type', 'freeze_deposit')
        ->exists();
    
    if (!$hasTransaction) {
        echo "  ! تراکنش سپرده وجود ندارد\n";
        echo "    سپرده محاسبه شده: " . number_format($depositAmount) . " تومان\n";
        
        $wallet = $winner->wallet;
        echo "    موجودی مسدود فعلی: " . number_format($wallet->frozen) . " تومان\n";
        
        // ثبت تراکنش
        \App\Models\WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $winner->id,
            'type' => 'freeze_deposit',
            'amount' => $depositAmount,
            'final_amount' => $depositAmount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance,
            'frozen_before' => $wallet->frozen - $depositAmount,
            'frozen_after' => $wallet->frozen,
            'reference_type' => \App\Models\Listing::class,
            'reference_id' => $listing->id,
            'description' => sprintf('بلاک سپرده حراجی (اصلاح شده): %s', $listing->title),
            'created_at' => $listing->ends_at, // تاریخ پایان حراجی
            'updated_at' => $listing->ends_at,
        ]);
        
        echo "    ✓ تراکنش ثبت شد\n";
    } else {
        echo "  ✓ تراکنش موجود است\n";
    }
    
    echo "\n";
}

echo "✓ اصلاح کامل شد\n";
