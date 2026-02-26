<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$notifs = DB::table('notifications')
    ->where('user_id', 40)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

echo "Notifications for User 40:\n";
if ($notifs->isEmpty()) {
    echo "  No notifications found!\n";
} else {
    foreach ($notifs as $n) {
        echo "  - {$n->type}: {$n->message} ({$n->created_at})\n";
    }
}
