<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = App\Models\Listing::find(23);

echo "current_winner_id: " . ($listing->current_winner_id ?? 'NULL') . "\n";
echo "winner_user_id: " . ($listing->winner_user_id ?? 'NULL') . "\n";
echo "finalization_deadline: " . ($listing->finalization_deadline ?? 'NULL') . "\n";

$bids = App\Models\Bid::where('listing_id', 23)->orderBy('amount', 'desc')->get();
echo "\nBids:\n";
foreach ($bids as $bid) {
    echo "  User {$bid->user_id}: {$bid->amount} - Status: {$bid->status}\n";
}

$notifications = App\Models\Notification::where('data->listing_id', 23)->get();
echo "\nNotifications:\n";
foreach ($notifications as $notif) {
    $data = json_decode($notif->data, true);
    echo "  User {$notif->user_id}: {$notif->type}\n";
}
