<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$notifs = DB::table('notifications')
    ->where('notifiable_id', 40)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "Recent notifications for User 40:\n";
foreach ($notifs as $n) {
    echo "  - {$n->type} at {$n->created_at}\n";
}
