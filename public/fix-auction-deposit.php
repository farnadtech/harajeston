<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\AuctionParticipation;

$slug = 'tst-frnad-1';

$listing = Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found\n");
}

echo "Current required_deposit: {$listing->required_deposit}\n";

// Update to 3000
$listing->required_deposit = 3000;
$listing->save();

echo "Updated required_deposit to: 3000\n\n";

// Create participations for users who bid
echo "Creating participations for bidders...\n";

$bidders = $listing->bids()->select('user_id')->distinct()->get();

foreach ($bidders as $bid) {
    $exists = AuctionParticipation::where('listing_id', $listing->id)
        ->where('user_id', $bid->user_id)
        ->exists();
    
    if (!$exists) {
        AuctionParticipation::create([
            'listing_id' => $listing->id,
            'user_id' => $bid->user_id,
            'deposit_amount' => 3000,
            'deposit_status' => 'frozen',
        ]);
        
        echo "Created participation for user {$bid->user_id}\n";
        
        // Freeze deposit in wallet
        $user = \App\Models\User::find($bid->user_id);
        if ($user && $user->wallet) {
            $wallet = $user->wallet;
            $wallet->balance -= 3000;
            $wallet->frozen += 3000;
            $wallet->save();
            
            echo "  Froze 3000 in wallet\n";
        }
    }
}

echo "\nDone!\n";
echo "Now reset the auction to active status:\n";

$listing->status = 'active';
$listing->ends_at = now()->addMinutes(2); // End in 2 minutes
$listing->save();

echo "Auction will end at: {$listing->ends_at}\n";
echo "Wait 2 minutes and the scheduler will end it automatically.\n";
