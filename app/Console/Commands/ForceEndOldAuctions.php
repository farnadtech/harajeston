<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\WalletService;
use App\Services\NotificationService;

class ForceEndOldAuctions extends Command
{
    protected $signature = 'auction:force-end-old';
    protected $description = 'Force end old auctions with corrupted frozen wallet state';

    public function handle(WalletService $walletService, NotificationService $notificationService)
    {
        $expiredAuctions = Listing::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        $this->info("Found {$expiredAuctions->count()} expired active auctions");

        if ($expiredAuctions->isEmpty()) {
            $this->info('Nothing to process.');
            return 0;
        }

        $depositPercentage = (float) SiteSetting::get('auction_deposit_percentage', 20);
        $deadlineHours = (int) SiteSetting::get('auction_payment_deadline_hours', 24);
        $loserFeeEnabled = SiteSetting::get('loser_fee_enabled', false);
        $loserFeePercentage = (float) SiteSetting::get('loser_fee_percentage', 0);

        foreach ($expiredAuctions as $listing) {
            $this->line("Processing auction #{$listing->id}: {$listing->title}");

            try {
                DB::transaction(function () use ($listing, $depositPercentage, $deadlineHours, $loserFeeEnabled, $loserFeePercentage, $walletService, $notificationService) {
                    $listing = Listing::where('id', $listing->id)->lockForUpdate()->first();

                    if ($listing->status !== 'active') {
                        $this->line("  Skipping - status is {$listing->status}");
                        return;
                    }

                    $bids = Bid::where('listing_id', $listing->id)
                        ->orderBy('amount', 'desc')
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->unique('user_id')
                        ->values();

                    if ($bids->isEmpty()) {
                        $listing->status = 'failed';
                        $listing->save();
                        $this->line("  No bids - marked as failed");
                        return;
                    }

                    $winner = $bids->first();
                    $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));

                    $this->line("  Winner: user #{$winner->user_id}, bid: " . number_format($winner->amount));
                    $this->line("  Deposit amount: " . number_format($depositAmount) . ", Losers: " . ($bids->count() - 1));

                    // Release deposits for losers safely (no negative frozen)
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
                            $this->line("  Loser user #{$user->id}: fee={$actualFee}, refund={$actualRefund}");
                        } else {
                            $actualRefund = min($depositAmount, $currentFrozen);
                            if ($actualRefund > 0) {
                                $wallet->frozen = max(0, $currentFrozen - $actualRefund);
                                $wallet->balance += $actualRefund;
                                $wallet->save();
                                $this->line("  Loser user #{$user->id}: refunded {$actualRefund}");
                            } else {
                                $this->line("  Loser user #{$user->id}: frozen=0, skipped");
                            }
                        }
                    }

                    $listing->status = 'ended';
                    $listing->current_winner_id = $winner->user_id;
                    $listing->finalization_deadline = now()->addHours($deadlineHours);
                    $listing->save();

                    $this->info("  ✓ Auction #{$listing->id} ended. Winner: user #{$winner->user_id}");

                    try {
                        $notificationService->notifyAuctionWon($listing, $winner->user, $winner->amount);
                    } catch (\Exception $e) {
                        $this->warn("  ! Notification failed: " . $e->getMessage());
                    }
                });

            } catch (\Exception $e) {
                $this->error("  ✗ ERROR auction #{$listing->id}: " . $e->getMessage());
            }
        }

        $this->info('Done.');
        return 0;
    }
}
