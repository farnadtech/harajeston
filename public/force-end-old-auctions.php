<?php
/**
 * Force end old auctions that have corrupted frozen wallet state
 * این اسکریپت حراجی‌های قدیمی که frozen کیف پول کاربران خراب شده رو دستی تموم می‌کنه
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use Illuminate\Support\Facades\DB;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\SiteSetting;
use App\Models\User;

echo "<pre style='direction:ltr;font-family:monospace;'>\n";
echo "=== Force End Old Auctions ===\n\n";

// پیدا کردن حراجی‌های active که ends_at گذشته
$expiredAuctions = Listing::where('status', 'active')
    ->where('ends_at', '<', now())
    ->get();

echo "Found " . $expiredAuctions->count() . " expired active auctions\n\n";

if ($expiredAuctions->isEmpty()) {
    echo "No expired auctions found.\n";
    exit;
}

$depositPercentage = (float) SiteSetting::get('auction_deposit_percentage', 20);
$deadlineHours = (int) SiteSetting::get('auction_payment_deadline_hours', 24);
$loserFeeEnabled = SiteSetting::get('loser_fee_enabled', false);
$loserFeePercentage = (float) SiteSetting::get('loser_fee_percentage', 0);

foreach ($expiredAuctions as $listing) {
    echo "Processing auction #{$listing->id}: {$listing->title}\n";
    
    try {
        DB::transaction(function () use ($listing, $depositPercentage, $deadlineHours, $loserFeeEnabled, $loserFeePercentage) {
            // Re-fetch with lock
            $listing = Listing::where('id', $listing->id)->lockForUpdate()->first();
            
            if ($listing->status !== 'active') {
                echo "  Skipping - status is {$listing->status}\n";
                return;
            }
            
            // Get all unique bids per user (highest bid per user)
            $bids = Bid::where('listing_id', $listing->id)
                ->orderBy('amount', 'desc')
                ->orderBy('created_at', 'asc')
                ->get()
                ->unique('user_id')
                ->values();
            
            if ($bids->isEmpty()) {
                $listing->status = 'failed';
                $listing->save();
                echo "  No bids - marked as failed\n";
                return;
            }
            
            $winner = $bids->first();
            $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));
            
            echo "  Winner: user #{$winner->user_id}, bid: " . number_format($winner->amount) . "\n";
            echo "  Deposit amount: " . number_format($depositAmount) . "\n";
            echo "  Total bidders: " . $bids->count() . "\n";
            
            // Release deposits for losers safely
            foreach ($bids->skip(1) as $bid) {
                $user = $bid->user;
                if (!$user) continue;
                
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                if (!$wallet) continue;
                
                $currentFrozen = max(0, $wallet->frozen);
                
                if ($loserFeeEnabled && $loserFeePercentage > 0 && $depositAmount > 0) {
                    $fee = (int) ($depositAmount * ($loserFeePercentage / 100));
                    $refund = $depositAmount - $fee;
                    $actualFee = min($fee, $currentFrozen);
                    $actualRefund = min($refund, max(0, $currentFrozen - $actualFee));
                    
                    $wallet->frozen = max(0, $currentFrozen - $actualFee - $actualRefund);
                    $wallet->balance += $actualRefund;
                    $wallet->save();
                    echo "  Loser user #{$user->id}: fee={$actualFee}, refund={$actualRefund}\n";
                } else {
                    // Full refund - safe
                    $actualRefund = min($depositAmount, $currentFrozen);
                    if ($actualRefund > 0) {
                        $wallet->frozen = max(0, $currentFrozen - $actualRefund);
                        $wallet->balance += $actualRefund;
                        $wallet->save();
                        echo "  Loser user #{$user->id}: refunded {$actualRefund}\n";
                    } else {
                        echo "  Loser user #{$user->id}: frozen=0, nothing to release\n";
                    }
                }
            }
            
            // End the auction
            $listing->status = 'ended';
            $listing->current_winner_id = $winner->user_id;
            $listing->finalization_deadline = now()->addHours($deadlineHours);
            $listing->save();
            
            echo "  ✓ Auction ended. Winner: user #{$winner->user_id}\n";
            
            // Send notification
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyAuctionWon($listing, $winner->user, $winner->amount);
                echo "  ✓ Winner notification sent\n";
            } catch (\Exception $e) {
                echo "  ! Notification failed: " . $e->getMessage() . "\n";
            }
        });
        
    } catch (\Exception $e) {
        echo "  ✗ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Done ===\n";
echo "</pre>";
