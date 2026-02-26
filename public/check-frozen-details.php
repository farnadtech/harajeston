<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== بررسی دقیق موجودی مسدود ===\n\n";

$listing = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->orderBy('ends_at', 'desc')
    ->first();

$winner = \App\Models\User::find($listing->current_winner_id);

echo "حراجی: {$listing->title} (ID: {$listing->id})\n";
echo "برنده: User #{$winner->id}\n";
echo "سپرده مورد نیاز: " . number_format($listing->required_deposit) . " تومان\n\n";

echo "=== کیف پول برنده ===\n";
$wallet = $winner->wallet;
echo "موجودی: " . number_format($wallet->balance) . " تومان\n";
echo "مسدود شده: " . number_format($wallet->frozen) . " تومان\n\n";

echo "=== همه تراکنش‌های freeze_deposit برنده ===\n";
$freezeTransactions = \App\Models\WalletTransaction::where('user_id', $winner->id)
    ->where('type', 'freeze_deposit')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($freezeTransactions as $tx) {
    $refType = $tx->reference_type ? class_basename($tx->reference_type) : 'N/A';
    echo "- مبلغ: " . number_format($tx->amount) . " تومان\n";
    echo "  نوع: {$refType} #{$tx->reference_id}\n";
    echo "  توضیحات: {$tx->description}\n";
    echo "  تاریخ: {$tx->created_at}\n";
    
    if ($tx->reference_type == \App\Models\Listing::class) {
        $refListing = \App\Models\Listing::find($tx->reference_id);
        if ($refListing) {
            echo "  حراجی: {$refListing->title} (وضعیت: {$refListing->status})\n";
        }
    }
    echo "\n";
}

echo "=== همه تراکنش‌های release_deposit برنده ===\n";
$releaseTransactions = \App\Models\WalletTransaction::where('user_id', $winner->id)
    ->where('type', 'release_deposit')
    ->orderBy('created_at', 'desc')
    ->get();

if ($releaseTransactions->isEmpty()) {
    echo "هیچ تراکنش آزادسازی وجود ندارد\n\n";
} else {
    foreach ($releaseTransactions as $tx) {
        $refType = $tx->reference_type ? class_basename($tx->reference_type) : 'N/A';
        echo "- مبلغ: " . number_format($tx->amount) . " تومان\n";
        echo "  نوع: {$refType} #{$tx->reference_id}\n";
        echo "  توضیحات: {$tx->description}\n";
        echo "  تاریخ: {$tx->created_at}\n\n";
    }
}

echo "=== شرکت در حراجی‌ها ===\n";
$participations = \App\Models\AuctionParticipation::where('user_id', $winner->id)
    ->with('listing')
    ->get();

foreach ($participations as $p) {
    echo "- حراجی #{$p->listing_id}: {$p->listing->title}\n";
    echo "  وضعیت: {$p->listing->status}\n";
    echo "  سپرده پرداخت شده: " . ($p->deposit_paid ? 'بله' : 'خیر') . "\n";
    echo "  سپرده برگشت داده شده: " . ($p->deposit_returned ? 'بله' : 'خیر') . "\n\n";
}
