<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== مرحله 1: تنظیم پارامترهای سیستم ===\n\n";

// 1. تنظیم مهلت پرداخت به 24 ساعت
echo "1. تنظیم مهلت پرداخت...\n";
\App\Models\SiteSetting::set('auction_payment_deadline_hours', 24);
echo "   ✓ مهلت پرداخت: 24 ساعت\n\n";

// 2. تنظیم درصد سپرده به 20%
echo "2. تنظیم درصد سپرده...\n";
\App\Models\SiteSetting::set('auction_deposit_percentage', 20);
echo "   ✓ درصد سپرده: 20%\n\n";

// 3. اصلاح مهلت حراجی‌های ended
echo "3. اصلاح مهلت حراجی‌های ended...\n";
$endedListings = \App\Models\Listing::where('status', 'ended')
    ->whereNotNull('current_winner_id')
    ->whereNotNull('finalization_deadline')
    ->get();

foreach ($endedListings as $listing) {
    $correctDeadline = $listing->ends_at->addHours(24);
    $listing->finalization_deadline = $correctDeadline;
    $listing->save();
    echo "   ✓ حراجی #{$listing->id}: مهلت به {$correctDeadline} تغییر کرد\n";
}

echo "\n✓ مرحله 1 کامل شد\n";
