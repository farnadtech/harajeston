<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;
use App\Models\Bid;
use App\Models\SiteSetting;
use App\Services\WalletService;

echo "Fixing auction 24...\n\n";

// 1. Update payment deadline setting to 72 hours
echo "1. Updating payment deadline setting to 72 hours...\n";
SiteSetting::set('auction_payment_deadline_hours', 72);
echo "   ✓ Done\n\n";

// 2. Update listing 24 finalization deadline
echo "2. Updating listing 24 finalization deadline...\n";
$listing = Listing::find(24);
$listing->finalization_deadline = now()->addHours(72);
$listing->save();
echo "   New deadline: {$listing->finalization_deadline}\n";
echo "   ✓ Done\n\n";

// 3. Release loser deposits
echo "3. Releasing loser deposits...\n";

$bids = Bid::where('listing_id', 24)
    ->orderBy('amount', 'desc')
    ->with('user')
    ->get();

$winner = $bids->first();
$losers = $bids->skip(1);

$depositPercentage = (float) SiteSetting::get('auction_deposit_percentage', 20);
$depositAmount = (int) ($listing->starting_price * ($depositPercentage / 100));

echo "   Deposit amount: " . number_format($depositAmount) . " تومان\n";
echo "   Winner: User {$winner->user_id} ({$winner->user->name})\n";
echo "   Losers: {$losers->count()}\n\n";

$walletService = app(WalletService::class);

foreach ($losers as $bid) {
    $user = $bid->user;
    echo "   Processing User {$user->id} ({$user->name})...\n";
    
    $wallet = $user->wallet;
    echo "     Before - Balance: " . number_format($wallet->balance) . ", Frozen: " . number_format($wallet->frozen) . "\n";
    
    // Check loser fee settings
    $loserFeeEnabled = SiteSetting::get('loser_fee_enabled', false);
    $loserFeePercentage = (float) SiteSetting::get('loser_fee_percentage', 0);
    
    if ($loserFeeEnabled && $loserFeePercentage > 0 && $depositAmount > 0) {
        // Calculate fee
        $fee = (int) ($depositAmount * ($loserFeePercentage / 100));
        $refundAmount = $depositAmount - $fee;
        
        echo "     Loser fee: " . number_format($fee) . " تومان\n";
        echo "     Refund: " . number_format($refundAmount) . " تومان\n";
        
        // Deduct fee from frozen
        $wallet->frozen -= $fee;
        $wallet->save();
        
        \App\Models\WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'deduct_frozen',
            'amount' => $fee,
            'final_amount' => $fee,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance,
            'frozen_before' => $wallet->frozen + $fee,
            'frozen_after' => $wallet->frozen,
            'reference_type' => \App\Models\Listing::class,
            'reference_id' => $listing->id,
            'status' => 'completed',
            'description' => sprintf('کارمزد بازنده حراجی: %s', $listing->title),
        ]);
        
        // Transfer fee to site
        $siteUser = \App\Models\User::find(1);
        if ($siteUser) {
            $walletService->addFunds(
                $siteUser,
                $fee,
                sprintf('کارمزد بازنده مزایده: %s', $listing->title)
            );
        }
        
        // Release remaining deposit
        if ($refundAmount > 0) {
            $walletService->releaseDeposit($user, $refundAmount, $listing);
        }
    } else {
        // Release full deposit without fee
        $walletService->releaseDeposit($user, $depositAmount, $listing);
    }
    
    $wallet->refresh();
    echo "     After - Balance: " . number_format($wallet->balance) . ", Frozen: " . number_format($wallet->frozen) . "\n";
    echo "     ✓ Done\n\n";
}

echo "✓ All fixes applied!\n";
