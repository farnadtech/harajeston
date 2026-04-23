<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$listing = \App\Models\Listing::where('slug', 'tst-tg')->first();
if (!$listing) { echo "Not found\n"; exit; }

echo "Status: {$listing->status}\n";
echo "current_winner_id: " . ($listing->current_winner_id ?? 'NULL') . "\n";
echo "ends_at: {$listing->ends_at}\n";

$order = \App\Models\Order::whereHas('items', fn($q) => $q->where('listing_id', $listing->id))->first();
if ($order) {
    echo "Order ID: {$order->id}\n";
    echo "Order buyer_id: {$order->buyer_id}\n";
    echo "Order status: {$order->status}\n";
    $buyer = \App\Models\User::find($order->buyer_id);
    echo "Buyer: {$buyer?->name} ({$buyer?->email})\n";
} else {
    echo "No order found\n";
}
