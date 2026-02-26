<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Bid;
use App\Models\SiteSetting;

echo "Checking latest ended auction...\n\n";

// Get latest ended auction
$listing = Listing::where('status', 'ended')
    ->orderBy('updated_at', 'desc')
    ->first();

if (!$listing) {
    echo "No ended auction found!\n";
    exit;
}

echo "Listing ID: {$listing->id}\n";
echo "Title: {$listing->title}\n";
echo "Status: {$listing->status}\n";
echo "Starting Price: " . number_format($listing->starting_price) . " تومان\n";
echo "Current Winner ID: {$listing->current_winner_id}\n";
echo "Finalization Deadline: {$listing->finalization_deadline}\n\n";

// Get deposit settings
$depositPercentage = (float) SiteSetting::get('auction_deposit_percentage', 20);
echo "Deposit Percentage Setting: {$depositPercentage}%\n";

$calculatedDeposit = (int) ($listing->starting_price * ($depositPercentage / 100));
echo "Calculated Deposit: " . number_format($calculatedDeposit) . " تومان\n";
echo "Listing Required Deposit: " . number_format($listing->required_deposit) . " تومان\n\n";

// Get payment deadline setting
$deadlineHours = (int) SiteSetting::get('auction_payment_deadline_hours', 24);
echo "Payment Deadline Setting: {$deadlineHours} hours\n\n";

// Get all bids
$bids = Bid::where('listing_id', $listing->id)
    ->orderBy('amount', 'desc')
    ->with('user')
    ->get();

echo "Total Bids: {$bids->count()}\n\n";

if ($bids->count() > 0) {
    echo "Bids:\n";
    foreach ($bids as $index => $bid) {
        $isWinner = $index === 0;
        echo "  " . ($isWinner ? "🏆 WINNER" : "  Loser") . " - User {$bid->user_id} ({$bid->user->name}): " . number_format($bid->amount) . " تومان\n";
        
        // Check wallet frozen amount
        $wallet = $bid->user->wallet;
        echo "    Wallet Balance: " . number_format($wallet->balance) . " تومان\n";
        echo "    Wallet Frozen: " . number_format($wallet->frozen) . " تومان\n";
    }
}

echo "\n";

// Check freeze_deposit transactions for this listing
$freezeTransactions = \App\Models\WalletTransaction::where('reference_type', 'App\Models\Listing')
    ->where('reference_id', $listing->id)
    ->where('type', 'freeze_deposit')
    ->with('user')
    ->get();

echo "Freeze Deposit Transactions: {$freezeTransactions->count()}\n";
foreach ($freezeTransactions as $trans) {
    echo "  User {$trans->user_id} ({$trans->user->name}): " . number_format($trans->amount) . " تومان\n";
}

echo "\n";

// Check release_deposit transactions
$releaseTransactions = \App\Models\WalletTransaction::where('reference_type', 'App\Models\Listing')
    ->where('reference_id', $listing->id)
    ->where('type', 'release_deposit')
    ->with('user')
    ->get();

echo "Release Deposit Transactions: {$releaseTransactions->count()}\n";
foreach ($releaseTransactions as $trans) {
    echo "  User {$trans->user_id} ({$trans->user->name}): " . number_format($trans->amount) . " تومان\n";
}

echo "\nDone!\n";
