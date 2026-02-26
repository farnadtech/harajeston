<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM notifications');
echo "Notification columns:\n";
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}

echo "\n\nRecent notifications:\n";
$notifs = DB::table('notifications')->orderBy('id', 'desc')->limit(5)->get();
foreach ($notifs as $n) {
    echo "  ID: {$n->id}, User: {$n->user_id}, Type: {$n->type}\n";
}
