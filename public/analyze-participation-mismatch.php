<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WalletTransaction;
use App\Models\AuctionParticipation;

echo "تحلیل عدم تطابق بین participations و تراکنش‌ها...\n\n";

// تمام participations
$participations = AuctionParticipation::with(['listing', 'user'])->get();

echo "بررسی هر participation:\n";
echo str_repeat("=", 80) . "\n\n";

$withTransaction = 0;
$withoutTransaction = 0;

foreach ($participations as $p) {
    // پیدا کردن تراکنش مربوطه
    $transaction = WalletTransaction::where('type', 'freeze_deposit')
        ->where('reference_type', 'App\Models\Listing')
        ->where('reference_id', $p->listing_id)
        ->where('user_id', $p->user_id)
        ->first();
    
    if ($transaction) {
        $withTransaction++;
    } else {
        $withoutTransaction++;
        echo "⚠ Participation بدون تراکنش:\n";
        echo "  ID: {$p->id}\n";
        echo "  حراجی: {$p->listing->title} (ID: {$p->listing_id})\n";
        echo "  کاربر: {$p->user->name} (ID: {$p->user_id})\n";
        echo "  مبلغ سپرده: " . number_format($p->deposit_amount) . " تومان\n";
        echo "  تاریخ: {$p->created_at}\n";
        echo "\n";
    }
}

echo str_repeat("=", 80) . "\n";
echo "نتیجه:\n";
echo "Participations با تراکنش: {$withTransaction}\n";
echo "Participations بدون تراکنش: {$withoutTransaction}\n";
echo "\n";

// بررسی معکوس: تراکنش‌های بدون participation
echo "بررسی تراکنش‌های بدون participation:\n";
echo str_repeat("=", 80) . "\n\n";

$transactions = WalletTransaction::where('type', 'freeze_deposit')
    ->where('reference_type', 'App\Models\Listing')
    ->whereNotNull('reference_id')
    ->get();

$transWithParticipation = 0;
$transWithoutParticipation = 0;

foreach ($transactions as $t) {
    $participation = AuctionParticipation::where('listing_id', $t->reference_id)
        ->where('user_id', $t->user_id)
        ->first();
    
    if ($participation) {
        $transWithParticipation++;
    } else {
        $transWithoutParticipation++;
        $listing = \App\Models\Listing::find($t->reference_id);
        echo "⚠ تراکنش بدون participation:\n";
        echo "  ID: {$t->id}\n";
        echo "  حراجی: " . ($listing ? $listing->title : 'حذف شده') . " (ID: {$t->reference_id})\n";
        echo "  کاربر ID: {$t->user_id}\n";
        echo "  مبلغ: " . number_format($t->amount) . " تومان\n";
        echo "  تاریخ: {$t->created_at}\n";
        echo "\n";
    }
}

echo str_repeat("=", 80) . "\n";
echo "نتیجه:\n";
echo "تراکنش‌ها با participation: {$transWithParticipation}\n";
echo "تراکنش‌ها بدون participation: {$transWithoutParticipation}\n";
