<?php

namespace App\Services;

use App\Services;

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Exceptions\Auction\DepositNotPaidException;
use App\Exceptions\Auction\InvalidBidAmountException;
use App\Exceptions\Auction\AuctionNotActiveException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class BidService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Place a bid on an auction
     * 
     * @param User $user
     * @param Listing $listing
     * @param float $amount
     * @return Bid
     * @throws DepositNotPaidException
     * @throws InvalidBidAmountException
     * @throws AuctionNotActiveException
     */
    public function placeBid(User $user, Listing $listing, float $amount): Bid
    {
        return DB::transaction(function () use ($user, $listing, $amount) {
            // Lock listing row to prevent race conditions
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            // Prevent seller from bidding on their own auction
            if ($listing->seller_id === $user->id) {
                throw new \Exception('شما نمی‌توانید در حراجی خودتان شرکت کنید.');
            }
            
            // Validate auction is active
            if ($listing->status !== 'active') {
                throw new AuctionNotActiveException($listing->id, $listing->status);
            }
            
            // Get deposit from site settings
            $depositSetting = \App\Models\SiteSetting::where('key', 'deposit_type')->first();
            $depositType = $depositSetting ? $depositSetting->value : 'none';
            
            $depositAmount = 0;
            if ($depositType === 'fixed') {
                $fixedSetting = \App\Models\SiteSetting::where('key', 'deposit_fixed_amount')->first();
                $depositAmount = $fixedSetting ? (int)$fixedSetting->value : 0;
            } elseif ($depositType === 'percentage') {
                $percentageSetting = \App\Models\SiteSetting::where('key', 'deposit_percentage')->first();
                $percentage = $percentageSetting ? (float)$percentageSetting->value : 0;
                $depositAmount = (int)($listing->starting_price * ($percentage / 100));
            }
            
            // Get highest bid
            $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
            $increment = $listing->bid_increment ?? 1000;
            $minimumBid = $highestBid ? $highestBid->amount + $increment : $listing->starting_price;
            
            // Validate bid amount is higher than minimum
            if ($amount < $minimumBid) {
                throw new InvalidBidAmountException($amount, $minimumBid);
            }
            
            // Check wallet balance and block deposit if needed
            $wallet = $user->wallet;
            if (!$wallet) {
                throw new \Exception('کیف پول شما یافت نشد. لطفا با پشتیبانی تماس بگیرید.');
            }
            
            $balance = $wallet->balance;
            $requiredBalance = $amount;
            
            // Check if user has already bid (deposit already blocked)
            $userHasBid = $listing->bids()->where('user_id', $user->id)->exists();
            
            // Add deposit to required balance if this is first bid
            if ($depositAmount > 0 && !$userHasBid) {
                $requiredBalance += $depositAmount;
            }
            
            if ($balance < $requiredBalance) {
                if ($depositAmount > 0 && !$userHasBid) {
                    throw new \Exception('موجودی کیف پول شما کافی نیست. مبلغ مورد نیاز: ' . number_format($requiredBalance) . ' تومان (شامل ' . number_format($depositAmount) . ' تومان سپرده)');
                } else {
                    throw new \Exception('موجودی کیف پول شما کافی نیست. مبلغ مورد نیاز: ' . number_format($requiredBalance) . ' تومان');
                }
            }
            
            // Block deposit amount for first bid
            if ($depositAmount > 0 && !$userHasBid) {
                $wallet->balance -= $depositAmount;
                $wallet->frozen += $depositAmount;
                $wallet->save();
                
                // ثبت تراکنش بلاک سپرده
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $user->id,
                    'type' => 'freeze_deposit',
                    'amount' => $depositAmount,
                    'final_amount' => $depositAmount,
                    'balance_before' => $wallet->balance + $depositAmount,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen - $depositAmount,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'status' => 'completed',
                    'description' => sprintf('بلاک سپرده حراجی: %s', $listing->title),
                ]);
            }
            
            // Create bid
            $bid = Bid::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'amount' => $amount,
            ]);
            
            // Update listing with new highest bid
            $listing->current_price = $amount;
            $listing->current_winner_id = $user->id;
            $listing->save();
            
            // Send notification to seller
            $this->notificationService->notifyNewBid($bid);
            
            // Notify previous highest bidder if exists
            if ($highestBid && $highestBid->user_id !== $user->id) {
                $this->notificationService->notifyOutbid($bid, $highestBid->user);
            }
            
            // TODO: Broadcast BidPlaced event for real-time updates
            
            return $bid;
        });
    }
    
    /**
     * Get current rankings for an auction
     * Returns collection of users with their highest bids, ordered by amount DESC
     * 
     * @param Listing $listing
     * @return Collection
     */
    public function getCurrentRankings(Listing $listing): Collection
    {
        // Get all bids for this listing
        $bids = Bid::where('listing_id', $listing->id)
            ->with('user')
            ->orderBy('amount', 'desc')
            ->orderBy('created_at', 'asc') // Earlier bid wins in case of tie
            ->get();
        
        // Group by user and get highest bid per user
        $rankings = $bids->groupBy('user_id')->map(function ($userBids) {
            return $userBids->first(); // Highest bid due to ordering
        })->values();
        
        // Add rank information
        return $rankings->map(function ($bid, $index) {
            return [
                'rank' => $index + 1,
                'user' => $bid->user,
                'amount' => $bid->amount,
                'bid_time' => $bid->created_at,
            ];
        });
    }
    
    /**
     * Get user's highest bid for a listing
     * 
     * @param User $user
     * @param Listing $listing
     * @return Bid|null
     */
    public function getUserHighestBid(User $user, Listing $listing): ?Bid
    {
        return Bid::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->orderBy('amount', 'desc')
            ->first();
    }
    
    /**
     * Get all bids for a listing
     * 
     * @param Listing $listing
     * @return Collection
     */
    public function getListingBids(Listing $listing): Collection
    {
        return Bid::where('listing_id', $listing->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
