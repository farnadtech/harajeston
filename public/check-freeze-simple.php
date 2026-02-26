<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WalletTransaction;

echo "Checking freeze_deposit transactions for listing 24...\n\n";

$transactions = WalletTransaction::where('reference_type', 'App\Models\Listing')
    ->where('reference_id', 24)
    ->where('type', 'freeze_deposit')
    ->get();

foreach ($transactions as $trans) {
    $user = \App\Models\User::find($trans->user_id);
    echo "User {$trans->user_id} ({$user->name}):\n";
    echo "  Amount: " . number_format($trans->amount) . " تومان\n";
    echo "  Description: {$trans->description}\n";
    echo "  Created: {$trans->created_at}\n\n";
}
