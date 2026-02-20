<?php

namespace App\Services;

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
            
            // Validate auction is active
            if ($listing->status !== 'active') {
                throw new AuctionNotActiveException($listing->id, $listing->status);
            }
            
            // Validate deposit paid
            $participation = AuctionParticipation::where('listing_id', $listing->id)
                ->where('user_id', $user->id)
                ->where('deposit_status', 'paid')
                ->first();
            
            if (!$participation) {
                throw new DepositNotPaidException($listing->id, $user->id);
            }
            
            // Validate bid amount is higher than current highest
            if ($listing->current_highest_bid !== null && $amount <= $listing->current_highest_bid) {
                throw new InvalidBidAmountException($amount, $listing->current_highest_bid + 1);
            }
            
            // Validate bid amount is at least base price
            if ($amount < $listing->base_price) {
                throw new InvalidBidAmountException($amount, $listing->base_price);
            }
            
            // Create bid
            $bid = Bid::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'amount' => $amount,
            ]);
            
            // Update listing with new highest bid
            $listing->current_highest_bid = $amount;
            $listing->highest_bidder_id = $user->id;
            $listing->save();
            
            // TODO: Broadcast BidPlaced event for real-time updates
            // TODO: Send notification to previous highest bidder (outbid)
            
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
