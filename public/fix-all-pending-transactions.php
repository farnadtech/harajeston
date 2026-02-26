<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WalletTransaction;

echo "Updating ALL pending transactions to completed status...\n\n";

// Find all transactions with pending status
$transactions = WalletTransaction::where('status', 'pending')->get();

echo "Found " . $transactions->count() . " pending transactions\n\n";

if ($transactions->count() > 0) {
    $byType = $transactions->groupBy('type');
    
    echo "Breakdown by type:\n";
    foreach ($byType as $type => $items) {
        echo "  - {$type}: {$items->count()} transactions\n";
    }
    echo "\n";
    
    foreach ($transactions as $transaction) {
        echo "Transaction ID: {$transaction->id}\n";
        echo "  Type: {$transaction->type}\n";
        echo "  User ID: {$transaction->user_id}\n";
        echo "  Amount: {$transaction->amount}\n";
        echo "  Created: {$transaction->created_at}\n";
        echo "  Old Status: {$transaction->status}\n";
        
        $transaction->status = 'completed';
        $transaction->save();
        
        echo "  New Status: completed ✓\n\n";
    }
    
    echo "✓ Successfully updated {$transactions->count()} transactions\n";
} else {
    echo "✓ No pending transactions found. All are already completed!\n";
}

echo "\nDone!\n";
