<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\AuctionParticipation;
use App\Services\WalletService;

$slug = 'tst-frnad-1';

$listing = Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found\n");
}

echo "Listing: {$listing->title}\n";
echo "Winner ID: {$listing->current_winner_id}\n\n";

$walletService = app(WalletService::class);

// Get all participations
$participations = $listing->participations()->with('user')->get();

foreach ($participations as $p) {
    echo "User: {$p->user->name} (ID: {$p->user_id})\n";
    
    // Skip winner
    if ($p->user_id === $listing->current_winner_id) {
        echo "  → Winner - keeping deposit frozen\n\n";
        continue;
    }
    
    // Release deposit for losers
    if ($p->deposit_status === 'frozen') {
        try {
            $walletService->releaseDeposit($p->user, $p->deposit_amount, $listing);
            
            // Update participation status
            $p->deposit_status = 'released';
            $p->save();
            
            echo "  ✓ Released {$p->deposit_amount} تومان\n";
            
            $wallet = $p->user->wallet;
            echo "  New Balance: {$wallet->balance} | Frozen: {$wallet->frozen}\n\n";
        } catch (\Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n\n";
        }
    } else {
        echo "  → Already {$p->deposit_status}\n\n";
    }
}

echo "Done!\n";
