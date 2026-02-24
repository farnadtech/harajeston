<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Columns in listings table:</h2>";
echo "<pre>";

$columns = DB::select('SHOW COLUMNS FROM listings');

foreach ($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

echo "</pre>";

// Check if current_highest_bid exists
$hasColumn = collect($columns)->contains(function($col) {
    return $col->Field === 'current_highest_bid';
});

if ($hasColumn) {
    echo "<p style='color: green;'>✓ Column 'current_highest_bid' EXISTS</p>";
} else {
    echo "<p style='color: red;'>✗ Column 'current_highest_bid' DOES NOT EXIST</p>";
    echo "<p>Need to run migration or add column manually</p>";
}
