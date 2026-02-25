<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Notifications for Buyer (User 40) ===\n";
$notifications = \App\Models\Notification::where('user_id', 40)
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($notifications as $n) {
    echo "ID: {$n->id} | Title: {$n->title} | Message: {$n->message}\n";
}

echo "\n=== Notifications for Seller (User 31) ===\n";
$notifications = \App\Models\Notification::where('user_id', 31)
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($notifications as $n) {
    echo "ID: {$n->id} | Title: {$n->title} | Message: {$n->message}\n";
}
