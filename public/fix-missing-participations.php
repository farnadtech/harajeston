<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WalletTransaction;
use App\Models\AuctionParticipation;
use App\Models\Listing;

echo "بازسازی رکوردهای participation گمشده...\n\n";

// پیدا کردن تمام تراکنش‌های freeze_deposit که participation ندارند
$depositTransactions = WalletTransaction::where('type', 'freeze_deposit')
    ->where('reference_type', 'App\Models\Listing')
    ->whereNotNull('reference_id')
    ->orderBy('created_at', 'asc')
    ->get();

$fixed = 0;
$skipped = 0;

foreach ($depositTransactions as $transaction) {
    $listingId = $transaction->reference_id;
    $userId = $transaction->user_id;
    
    // چک کنیم که آیا participation وجود دارد
    $existingParticipation = AuctionParticipation::where('listing_id', $listingId)
        ->where('user_id', $userId)
        ->first();
    
    if ($existingParticipation) {
        $skipped++;
        continue;
    }
    
    // پیدا کردن حراجی
    $listing = Listing::find($listingId);
    if (!$listing) {
        echo "⚠ حراجی {$listingId} یافت نشد - تراکنش {$transaction->id}\n";
        continue;
    }
    
    // ایجاد رکورد participation
    AuctionParticipation::create([
        'listing_id' => $listingId,
        'user_id' => $userId,
        'deposit_amount' => $transaction->amount,
        'deposit_status' => 'paid',
        'created_at' => $transaction->created_at,
        'updated_at' => $transaction->created_at,
    ]);
    
    $fixed++;
    echo "✓ ایجاد participation برای کاربر {$userId} در حراجی '{$listing->title}' (مبلغ: " . number_format($transaction->amount) . " تومان)\n";
}

echo "\n";
echo "تعداد رکوردهای ایجاد شده: {$fixed}\n";
echo "تعداد رکوردهای موجود (رد شده): {$skipped}\n";
echo "\nبررسی نهایی...\n\n";

// بررسی نهایی
$allDepositTransactions = WalletTransaction::where('type', 'freeze_deposit')
    ->where('reference_type', 'App\Models\Listing')
    ->whereNotNull('reference_id')
    ->count();

$allParticipations = AuctionParticipation::count();

echo "تعداد کل تراکنش‌های سپرده: {$allDepositTransactions}\n";
echo "تعداد کل رکوردهای participation: {$allParticipations}\n";

if ($allDepositTransactions == $allParticipations) {
    echo "\n✓ همه تراکنش‌ها دارای رکورد participation هستند!\n";
} else {
    echo "\n⚠ هنوز " . ($allDepositTransactions - $allParticipations) . " تراکنش بدون participation وجود دارد.\n";
}
