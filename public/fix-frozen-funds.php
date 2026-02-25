<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fix the frozen funds issue for user 40, listing 19
$user = \App\Models\User::find(40);
$listing = \App\Models\Listing::find(19);
$wallet = $user->wallet;

echo "Current wallet state:\n";
echo "  Balance: " . $wallet->balance . "\n";
echo "  Frozen: " . $wallet->frozen . "\n";

// The issue: 37000 was deducted from balance but not frozen
// We need to:
// 1. Add 37000 back to balance
// 2. Freeze 37000

echo "\nFixing frozen funds...\n";

\Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $listing) {
    $wallet = \App\Models\Wallet::where('id', $wallet->id)
        ->lockForUpdate()
        ->first();
    
    $beforeBalance = $wallet->balance;
    $beforeFrozen = $wallet->frozen;
    
    // Add back the 37000 to balance
    $wallet->balance += 37000;
    // Freeze it
    $wallet->frozen += 37000;
    $wallet->save();
    
    echo "  Balance: {$beforeBalance} -> {$wallet->balance}\n";
    echo "  Frozen: {$beforeFrozen} -> {$wallet->frozen}\n";
    
    // Delete the wrong transaction (ID 50)
    \App\Models\WalletTransaction::where('id', 50)->delete();
    echo "  Deleted wrong transaction ID 50\n";
    
    // Create correct transaction
    \App\Models\WalletTransaction::create([
        'wallet_id' => $wallet->id,
        'user_id' => $wallet->user_id,
        'type' => 'freeze_deposit',
        'amount' => 37000,
        'final_amount' => 37000,
        'balance_before' => $beforeBalance + 37000,
        'balance_after' => $wallet->balance,
        'frozen_before' => $beforeFrozen,
        'frozen_after' => $wallet->frozen,
        'reference_type' => \App\Models\Listing::class,
        'reference_id' => $listing->id,
        'description' => sprintf('بلاک مبلغ باقیمانده حراجی: %s', $listing->title),
    ]);
    echo "  Created correct freeze_deposit transaction\n";
});

echo "\nFixed! New wallet state:\n";
$wallet->refresh();
echo "  Balance: " . $wallet->balance . "\n";
echo "  Frozen: " . $wallet->frozen . "\n";

echo "\nTransactions:\n";
$transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
    ->where('reference_type', \App\Models\Listing::class)
    ->where('reference_id', $listing->id)
    ->orderBy('id', 'desc')
    ->get();

foreach ($transactions as $tx) {
    echo "  ID: {$tx->id} | Type: {$tx->type} | Amount: {$tx->amount} | Frozen: {$tx->frozen_before} -> {$tx->frozen_after}\n";
}
