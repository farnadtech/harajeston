<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = App\Models\Listing::find(24);

echo "Listing 24:\n";
echo "  base_price: " . ($listing->base_price ?? 'NULL') . "\n";
echo "  starting_price: " . $listing->starting_price . "\n";
echo "  required_deposit: " . $listing->required_deposit . "\n";
