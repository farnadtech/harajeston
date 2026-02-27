<?php
// Check auction deposit issue
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = auth()->user();
if (!$user) {
    die("Please login first");
}

echo "<h2>Checking Auction Deposit Issue</h2>";

// Get the listing from URL parameter
$listingId = $_GET['listing_id'] ?? null;

if (!$listingId) {
    echo "<p>Please provide listing_id in URL: ?listing_id=XX</p>";
    
    // Show user's won auctions
    $wonAuctions = \App\Models\Listing::where('current_winner_id', $user->id)
        ->where('status', 'ended')
        ->get();
    
    if ($wonAuctions->count() > 0) {
        echo "<h3>Your Won Auctions:</h3>";
        echo "<ul>";
        foreach ($wonAuctions as $auction) {
            echo "<li><a href='?listing_id={$auction->id}'>{$auction->title} (ID: {$auction->id})</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No won auctions found</p>";
    }
    exit;
}

$listing = \App\Models\Listing::find($listingId);
if (!$listing) {
    die("Listing not found");
}

echo "<h3>Listing Info:</h3>";
echo "<p>ID: {$listing->id}</p>";
echo "<p>Title: {$listing->title}</p>";
echo "<p>Status: {$listing->status}</p>";
echo "<p>Required Deposit: " . number_format($listing->required_deposit) . " تومان</p>";
echo "<p>Current Winner ID: {$listing->current_winner_id}</p>";
echo "<p>Current Price: " . number_format($listing->current_price) . " تومان</p>";

// Check participation
$participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->first();

echo "<h3>Auction Participation:</h3>";
if ($participation) {
    echo "<pre>";
    echo "ID: {$participation->id}\n";
    echo "User ID: {$participation->user_id}\n";
    echo "Listing ID: {$participation->listing_id}\n";
    echo "Deposit Amount: " . number_format($participation->deposit_amount) . " تومان\n";
    echo "Deposit Paid: " . ($participation->deposit_paid ? 'Yes' : 'No') . "\n";
    echo "Deposit Paid At: " . ($participation->deposit_paid_at ?? 'NULL') . "\n";
    echo "Created At: {$participation->created_at}\n";
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ No participation record found!</p>";
    echo "<p>This is the problem. The user participated but no record exists.</p>";
}

// Check bids
$bids = \App\Models\Bid::where('listing_id', $listing->id)
    ->where('user_id', $user->id)
    ->orderBy('amount', 'desc')
    ->get();

echo "<h3>User's Bids:</h3>";
if ($bids->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Amount</th><th>Created At</th></tr>";
    foreach ($bids as $bid) {
        echo "<tr>";
        echo "<td>{$bid->id}</td>";
        echo "<td>" . number_format($bid->amount) . "</td>";
        echo "<td>{$bid->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No bids found</p>";
}

// Check wallet transactions
$depositTransactions = \App\Models\WalletTransaction::where('user_id', $user->id)
    ->where('reference_type', 'App\Models\Listing')
    ->where('reference_id', $listing->id)
    ->where('type', 'freeze_deposit')
    ->get();

echo "<h3>Deposit Transactions:</h3>";
if ($depositTransactions->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Type</th><th>Amount</th><th>Description</th><th>Created At</th></tr>";
    foreach ($depositTransactions as $trans) {
        echo "<tr>";
        echo "<td>{$trans->id}</td>";
        echo "<td>{$trans->type}</td>";
        echo "<td>" . number_format($trans->amount) . "</td>";
        echo "<td>{$trans->description}</td>";
        echo "<td>{$trans->created_at}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No deposit transactions found</p>";
}

// Solution
if (!$participation && $listing->required_deposit > 0) {
    echo "<hr>";
    echo "<h3>Solution:</h3>";
    echo "<p>This listing requires a deposit but no participation record exists.</p>";
    echo "<p>This might happen if:</p>";
    echo "<ul>";
    echo "<li>The listing was created before the deposit system was implemented</li>";
    echo "<li>The user bid before paying deposit (old bug)</li>";
    echo "<li>The participation record was deleted</li>";
    echo "</ul>";
    
    echo "<h4>Fix Options:</h4>";
    echo "<p>1. Create a participation record with deposit_amount = 0 (for legacy listings)</p>";
    echo "<p>2. Or update the checkout view to handle missing participation gracefully</p>";
}
