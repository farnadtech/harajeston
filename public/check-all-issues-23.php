<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Bid;
use App\Models\User;

$listing = Listing::find(23);
$winningBid = Bid::where('listing_id', 23)->orderBy('amount', 'desc')->first();

echo "=== Listing Info ===\n";
echo "Required Deposit: " . ($listing->required_deposit ?? 'NULL') . "\n";
echo "Deposit Amount: " . ($listing->deposit_amount ?? 'NULL') . "\n";
echo "Starting Price: {$listing->starting_price}\n";
echo "Current Price: {$listing->current_price}\n";
echo "Winner User ID: {$listing->current_winner_id}\n";
echo "Finalization Deadline: {$listing->finalization_deadline}\n\n";

echo "=== Admin Settings ===\n";
$deadline_hours = \App\Models\SiteSetting::get('auction_finalize_deadline_hours', 24);
echo "auction_finalize_deadline_hours: {$deadline_hours}\n\n";

echo "=== Winner Info ===\n";
if ($winningBid) {
    $winner = User::find($winningBid->user_id);
    echo "Winner: User {$winner->id} ({$winner->name})\n";
    echo "Winning Bid: {$winningBid->amount}\n";
    
    $wallet = $winner->wallet;
    echo "Wallet Balance: {$wallet->balance}\n";
    echo "Wallet Frozen: {$wallet->frozen}\n\n";
}

echo "=== All Bids ===\n";
$bids = Bid::where('listing_id', 23)->orderBy('amount', 'desc')->get();
foreach ($bids as $bid) {
    $user = User::find($bid->user_id);
    $wallet = $user->wallet;
    echo "User {$bid->user_id}: Bid {$bid->amount} - Frozen: {$wallet->frozen}\n";
}

echo "\n=== Calculation ===\n";
echo "Winning Bid Amount: {$winningBid->amount}\n";
echo "Required Deposit (should be): " . ($listing->required_deposit ?? 'NULL') . "\n";
echo "Remaining Amount: " . ($winningBid->amount - ($listing->required_deposit ?? 0)) . "\n";
