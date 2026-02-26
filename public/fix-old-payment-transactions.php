<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Fixing Old Payment Transactions ===\n\n";

// Find all deduct_frozen transactions related to orders
$oldTxs = \App\Models\WalletTransaction::where('type', 'deduct_frozen')
    ->where('reference_type', \App\Models\Order::class)
    ->whereNotNull('reference_id')
    ->get();

echo "Found {$oldTxs->count()} old deduct_frozen transactions\n\n";

foreach ($oldTxs as $tx) {
    echo "Transaction #{$tx->id}:\n";
    echo "- User: {$tx->user_id}\n";
    echo "- Amount: " . number_format($tx->amount) . "\n";
    echo "- Description: {$tx->description}\n";
    
    // Update type to withdrawal
    $tx->type = 'withdrawal';
    $tx->save();
    
    echo "✓ Updated to 'withdraw'\n\n";
}

echo "Done! All transactions updated.\n";
