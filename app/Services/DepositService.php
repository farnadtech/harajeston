<?php

namespace App\Services;

use App\Models\User;
use App\Models\Listing;
use App\Models\AuctionParticipation;
use App\Exceptions\Auction\AlreadyParticipatingException;
use App\Exceptions\Auction\AuctionNotActiveException;
use App\Exceptions\Wallet\InsufficientBalanceException;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        protected WalletService $walletService,
        protected CommissionService $commissionService
    ) {}
    
    /**
     * Participate in auction by paying deposit
     * 
     * @param User $user
     * @param Listing $listing
     * @return AuctionParticipation
     * @throws AlreadyParticipatingException
     * @throws AuctionNotActiveException
     * @throws InsufficientBalanceException
     */
    public function participateInAuction(User $user, Listing $listing): AuctionParticipation
    {
        // Validate auction is active or pending
        if (!in_array($listing->status, ['pending', 'active'])) {
            throw new AuctionNotActiveException($listing->id, $listing->status);
        }
        
        // Check for existing participation
        $existingParticipation = AuctionParticipation::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($existingParticipation) {
            throw new AlreadyParticipatingException($listing->id, $user->id);
        }
        
        return DB::transaction(function () use ($user, $listing) {
            // Calculate deposit based on site settings
            $depositAmount = $this->commissionService->calculateDeposit($listing->starting_price);
            
            // Freeze deposit amount in user's wallet
            $this->walletService->freezeDeposit(
                $user,
                $depositAmount,
                $listing
            );
            
            // Create participation record
            $participation = AuctionParticipation::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'deposit_amount' => $depositAmount,
                'deposit_status' => 'paid',
            ]);
            
            return $participation;
        });
    }
    
    /**
     * Check if user has participated in auction
     * 
     * @param User $user
     * @param Listing $listing
     * @return bool
     */
    public function hasParticipated(User $user, Listing $listing): bool
    {
        return AuctionParticipation::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->where('deposit_status', 'paid')
            ->exists();
    }
    
    /**
     * Get participation record
     * 
     * @param User $user
     * @param Listing $listing
     * @return AuctionParticipation|null
     */
    public function getParticipation(User $user, Listing $listing): ?AuctionParticipation
    {
        return AuctionParticipation::where('listing_id', $listing->id)
            ->where('user_id', $user->id)
            ->first();
    }
}
