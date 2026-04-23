<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AuctionParticipation;
use Illuminate\Support\Facades\DB;

echo "بررسی رکوردهای تکراری participation...\n\n";

// پیدا کردن رکوردهای تکراری
$duplicates = DB::table('auction_participations')
    ->select('listing_id', 'user_id', DB::raw('COUNT(*) as count'))
    ->groupBy('listing_id', 'user_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "✓ هیچ رکورد تکراری وجود ندارد.\n";
} else {
    echo "⚠ رکوردهای تکراری یافت شد:\n\n";
    
    foreach ($duplicates as $duplicate) {
        $participations = AuctionParticipation::where('listing_id', $duplicate->listing_id)
            ->where('user_id', $duplicate->user_id)
            ->with(['listing', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        echo "حراجی: {$participations->first()->listing->title} (ID: {$duplicate->listing_id})\n";
        echo "کاربر: {$participations->first()->user->name} (ID: {$duplicate->user_id})\n";
        echo "تعداد رکورد: {$duplicate->count}\n";
        
        foreach ($participations as $index => $p) {
            echo "  [{$index}] ID: {$p->id}, مبلغ: " . number_format($p->deposit_amount) . " تومان, تاریخ: {$p->created_at}\n";
        }
        
        echo "\n";
    }
}

echo "\nآمار کلی:\n";
echo "تعداد کل participations: " . AuctionParticipation::count() . "\n";
echo "تعداد participations یکتا (listing_id + user_id): " . DB::table('auction_participations')
    ->select('listing_id', 'user_id')
    ->distinct()
    ->count() . "\n";
