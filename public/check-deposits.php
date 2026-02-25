<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;

$slug = 'tst-frnad-1';

$listing = Listing::where('slug', $slug)->with(['participations.user.wallet', 'bids.user'])->first();

if (!$listing) {
    die("Listing not found\n");
}

echo "Listing: {$listing->title}\n";
echo "Status: {$listing->status}\n";
echo "Required deposit: {$listing->required_deposit}\n";
echo "Winner ID: {$listing->current_winner_id}\n";
echo "\n";

echo "All participants and their deposits:\n";
echo str_repeat("-", 80) . "\n";

$participations = $listing->participations;
foreach ($participations as $p) {
    $wallet = $p->user->wallet;
    $isWinner = $p->user_id === $listing->current_winner_id;
    
    echo sprintf(
        "User ID: %d | Name: %s | %s\n",
        $p->user_id,
        $p->user->name,
        $isWinner ? "*** WINNER ***" : ""
    );
    echo sprintf(
        "  Balance: %s | Frozen: %s | Deposit Status: %s\n",
        number_format($wallet->balance),
        number_format($wallet->frozen),
        $p->deposit_status ?? 'N/A'
    );
    echo "\n";
}

echo "\nAll bids:\n";
echo str_repeat("-", 80) . "\n";

$bids = $listing->bids()->orderBy('amount', 'desc')->get();
foreach ($bids as $index => $bid) {
    echo sprintf(
        "#%d | User: %s | Amount: %s | Time: %s\n",
        $index + 1,
        $bid->user->name,
        number_format($bid->amount),
        $bid->created_at->format('Y-m-d H:i:s')
    );
}
