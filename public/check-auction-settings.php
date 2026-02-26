<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SiteSetting;

echo "Checking auction settings...\n\n";

$settings = [
    'auction_deposit_percentage',
    'auction_payment_deadline_hours',
    'loser_fee_enabled',
    'loser_fee_percentage',
];

foreach ($settings as $key) {
    $value = SiteSetting::get($key, 'NOT SET');
    echo "{$key}: {$value}\n";
}

echo "\nDone!\n";
