<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Bid;
use App\Models\Order;
use App\Models\Notification;

$listing = Listing::with(['bids' => function($q) {
    $q->orderBy('amount', 'desc');
}])->find(23);

if (!$listing) {
    echo "Listing not found!\n";
    exit;
}

echo "=== Listing Info ===\n";
echo "ID: {$listing->id}\n";
echo "Title: {$listing->title}\n";
echo "Status: {$listing->status}\n";
echo "Ends At: {$listing->ends_at}\n";
echo "Is Ended: " . ($listing->ends_at->isPast() ? 'YES' : 'NO') . "\n";
echo "Current Price: {$listing->current_price}\n";
echo "Winner User ID: {$listing->winner_user_id}\n\n";

echo "=== Bids ===\n";
foreach ($listing->bids as $bid) {
    echo "Bid #{$bid->id}: User {$bid->user_id} - Amount: {$bid->amount} - Status: {$bid->status}\n";
}

$winningBid = $listing->bids->where('status', 'winning')->first();
echo "\nWinning Bid: " . ($winningBid ? "Bid #{$winningBid->id} by User {$winningBid->user_id}" : "NONE") . "\n";

echo "\n=== Orders ===\n";
$orders = Order::where('listing_id', 23)->get();
foreach ($orders as $order) {
    echo "Order #{$order->id}: User {$order->user_id} - Status: {$order->status} - Total: {$order->total_amount}\n";
}

echo "\n=== Notifications for Winner ===\n";
if ($winningBid) {
    $notifications = Notification::where('user_id', $winningBid->user_id)
        ->where('data->listing_id', 23)
        ->get();
    
    foreach ($notifications as $notif) {
        $data = json_decode($notif->data, true);
        $message = isset($data['message']) ? $data['message'] : 'N/A';
        echo "Notification: {$notif->type} - Message: {$message}\n";
    }
}

echo "\n=== Check if endAuction was called ===\n";
echo "Winner User ID is set: " . ($listing->winner_user_id ? 'YES' : 'NO') . "\n";
echo "Status should be 'ended': " . ($listing->status === 'ended' ? 'YES' : 'NO') . "\n";
