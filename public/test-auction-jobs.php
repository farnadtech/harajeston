<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== تست Job های حراجی ===\n\n";

try {
    // Test ProcessAuctionStarting
    echo "1. تست ProcessAuctionStarting:\n";
    $pendingAuctions = \App\Models\Listing::where('status', 'pending')
        ->where('starts_at', '<=', now())
        ->get();
    echo "   تعداد حراجی‌های pending: {$pendingAuctions->count()}\n";
    
    foreach ($pendingAuctions as $auction) {
        echo "   - حراجی #{$auction->id}: {$auction->title}\n";
        echo "     starts_at: {$auction->starts_at}\n";
        echo "     now: " . now() . "\n";
    }
    
    // Test ProcessAuctionEnding
    echo "\n2. تست ProcessAuctionEnding:\n";
    $activeAuctions = \App\Models\Listing::where('status', 'active')
        ->where('ends_at', '<=', now())
        ->get();
    echo "   تعداد حراجی‌های active که باید تموم بشن: {$activeAuctions->count()}\n";
    
    foreach ($activeAuctions as $auction) {
        echo "   - حراجی #{$auction->id}: {$auction->title}\n";
        echo "     ends_at: {$auction->ends_at}\n";
        echo "     now: " . now() . "\n";
        
        // Try to end it
        try {
            $auctionService = app(\App\Services\AuctionService::class);
            $auctionService->endAuction($auction);
            echo "     ✓ با موفقیت پایان یافت\n";
        } catch (\Exception $e) {
            echo "     ✗ خطا: " . $e->getMessage() . "\n";
            echo "     Stack: " . $e->getTraceAsString() . "\n";
        }
    }
    
    echo "\n✓ تست کامل شد\n";
    
} catch (\Exception $e) {
    echo "خطای کلی: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
