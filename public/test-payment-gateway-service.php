<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a user
$user = \App\Models\User::find(29);

if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User ID: {$user->id}\n";
echo "Wallet ID: {$user->wallet->id}\n";

// Test creating transaction
try {
    $transaction = \App\Models\WalletTransaction::create([
        'wallet_id' => $user->wallet->id,
        'user_id' => $user->id,
        'type' => 'deposit',
        'amount' => 1000,
        'tax_amount' => 90,
        'final_amount' => 1090,
        'gateway' => 'test',
        'status' => 'pending',
        'description' => 'تست',
        'balance_before' => 0,
        'balance_after' => 0,
        'frozen_before' => 0,
        'frozen_after' => 0,
    ]);
    
    echo "Transaction created successfully! ID: {$transaction->id}\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
