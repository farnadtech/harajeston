<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Notification;

$slug = 'tst-frnad-1';

$listing = Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found\n");
}

echo "=== AUCTION STATUS ===\n";
echo "Title: {$listing->title}\n";
echo "Status: {$listing->status}\n";
echo "Winner ID: {$listing->current_winner_id}\n";
echo "Finalization deadline: {$listing->finalization_deadline}\n\n";

echo "=== PARTICIPATIONS ===\n";
$participations = $listing->participations()->with('user')->get();
foreach ($participations as $p) {
    echo "User: {$p->user->name} (ID: {$p->user_id})\n";
    echo "  Deposit: {$p->deposit_amount} | Status: {$p->deposit_status}\n";
    
    $wallet = $p->user->wallet;
    echo "  Wallet - Balance: {$wallet->balance} | Frozen: {$wallet->frozen}\n\n";
}

echo "=== WINNER NOTIFICATION ===\n";
if ($listing->current_winner_id) {
    $winnerNotifications = Notification::where('user_id', $listing->current_winner_id)
        ->where('type', 'auction_won')
        ->where('link', 'like', '%' . $listing->slug . '%')
        ->get();
    
    if ($winnerNotifications->count() > 0) {
        foreach ($winnerNotifications as $notif) {
            echo "✓ Notification sent!\n";
            echo "  Title: {$notif->title}\n";
            echo "  Message: {$notif->message}\n";
            echo "  Created: {$notif->created_at}\n";
        }
    } else {
        echo "✗ No notification found for winner\n";
    }
} else {
    echo "No winner set\n";
}

echo "\n=== VIEW AUCTION PAGE ===\n";
echo "http://localhost/haraj/public/listings/{$listing->slug}\n";
