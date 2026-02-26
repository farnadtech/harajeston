<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WalletTransaction;

echo "Updating freeze_deposit transactions status...\n\n";

// Find all freeze_deposit transactions with pending status
$transactions = WalletTransaction::where('type', 'freeze_deposit')
    ->where('status', 'pending')
    ->get();

echo "Found " . $transactions->count() . " freeze_deposit transactions with pending status\n\n";

if ($transactions->count() > 0) {
    foreach ($transactions as $transaction) {
        echo "Transaction ID: {$transaction->id}\n";
        echo "  User ID: {$transaction->user_id}\n";
        echo "  Amount: {$transaction->amount}\n";
        echo "  Created: {$transaction->created_at}\n";
        echo "  Old Status: {$transaction->status}\n";
        
        $transaction->status = 'completed';
        $transaction->save();
        
        echo "  New Status: completed ✓\n\n";
    }
    
    echo "✓ Successfully updated {$transactions->count()} freeze_deposit transactions\n";
} else {
    echo "✓ No pending freeze_deposit transactions found. All are already completed!\n";
}

echo "\nDone!\n";
