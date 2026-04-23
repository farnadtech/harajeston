<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('name', 'LIKE', '%الهه%')->first();

echo "کاربر: {$user->name} (ID: {$user->id})\n\n";

// Check all participations
$participations = \App\Models\AuctionParticipation::where('user_id', $user->id)->get();

echo "تعداد شرکت در حراجی‌ها: " . $participations->count() . "\n\n";

foreach ($participations as $p) {
    $listing = $p->listing;
    echo "حراجی: {$listing->title} (ID: {$listing->id})\n";
    echo "  - وضعیت سپرده: {$p->deposit_status}\n";
    echo "  - مبلغ سپرده: " . number_format($p->deposit_amount) . " تومان\n";
    echo "  - تاریخ: {$p->created_at}\n";
    echo "  - قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
    echo "  - اختلاف: " . number_format($listing->buy_now_price - $p->deposit_amount) . " تومان\n\n";
}

// Check wallet transactions
echo "تراکنش‌های سپرده:\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
    ->where('type', 'freeze_deposit')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($transactions as $t) {
    echo "  - " . number_format($t->amount) . " تومان - {$t->description} - {$t->created_at}\n";
}
