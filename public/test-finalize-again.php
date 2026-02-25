<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get listing 19
$listing = \App\Models\Listing::find(19);
$winner = \App\Models\User::find(40);

if (!$listing || !$winner) {
    echo "Listing or winner not found\n";
    exit;
}

echo "Current listing status: " . $listing->status . "\n";
echo "Current winner: " . $listing->current_winner_id . "\n";

// Check wallet before
$wallet = $winner->wallet;
echo "\nWallet before:\n";
echo "  Balance: " . $wallet->balance . "\n";
echo "  Frozen: " . $wallet->frozen . "\n";

// Check if already has order
$existingOrder = \App\Models\Order::where('buyer_id', $winner->id)
    ->whereHas('items', function($q) use ($listing) {
        $q->where('listing_id', $listing->id);
    })
    ->first();

if ($existingOrder) {
    echo "\nOrder already exists: #" . $existingOrder->order_number . "\n";
    echo "Order total: " . $existingOrder->total . "\n";
}

// Check transactions
echo "\nRecent transactions:\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $winner->id)
    ->where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', $listing->id)
    ->orderBy('id', 'desc')
    ->get();

foreach ($transactions as $tx) {
    echo "  ID: {$tx->id} | Type: {$tx->type} | Amount: {$tx->amount} | Frozen: {$tx->frozen_before} -> {$tx->frozen_after} | Desc: {$tx->description}\n";
}
