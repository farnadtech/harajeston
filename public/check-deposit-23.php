<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = App\Models\Listing::find(23);

echo "Listing ID: {$listing->id}\n";
echo "Required Deposit: " . ($listing->required_deposit ?? 'NULL') . "\n";
echo "Deposit Amount: " . ($listing->deposit_amount ?? 'NULL') . "\n";
echo "Starting Price: {$listing->starting_price}\n";
