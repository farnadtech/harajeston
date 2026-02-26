<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Fixing Transaction Status ===\n\n";

// Update all pending transactions to completed
$updated = \App\Models\WalletTransaction::where('status', 'pending')
    ->update(['status' => 'completed']);

echo "Updated {$updated} transactions from 'pending' to 'completed'\n";
