<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = App\Models\Listing::find(23);
$listing->status = 'active';
$listing->current_winner_id = null;
$listing->finalization_deadline = null;
$listing->save();

echo "✓ Auction 23 reset to active\n";
