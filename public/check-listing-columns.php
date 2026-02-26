<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM listings');
echo "Columns with 'winner' or 'current':\n";
foreach ($columns as $col) {
    if (strpos($col->Field, 'winner') !== false || strpos($col->Field, 'current') !== false) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
}
