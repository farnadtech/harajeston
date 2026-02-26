<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check withdrawal transactions
$withdrawals = \App\Models\WalletTransaction::where('type', 'withdrawal')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

echo "=== Recent Withdrawal Transactions ===\n";
foreach ($withdrawals as $tx) {
    echo "ID: {$tx->id}\n";
    echo "User: {$tx->user_id}\n";
    echo "Amount: " . number_format($tx->amount) . "\n";
    echo "Status: " . ($tx->status ?? 'NULL') . "\n";
    echo "Description: {$tx->description}\n";
    echo "\n";
}

// Check deposit transactions
$deposits = \App\Models\WalletTransaction::where('type', 'deposit')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

echo "=== Recent Deposit Transactions ===\n";
foreach ($deposits as $tx) {
    echo "ID: {$tx->id}\n";
    echo "User: {$tx->user_id}\n";
    echo "Amount: " . number_format($tx->amount) . "\n";
    echo "Status: " . ($tx->status ?? 'NULL') . "\n";
    echo "Description: {$tx->description}\n";
    echo "\n";
}
