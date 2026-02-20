<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Services\BidService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class AuctionBidding extends Component
{
    public Listing $listing;
    public $bidAmount;
    public $currentHighestBid;
    public $rankings = [];
    public $errorMessage = '';
    public $successMessage = '';

    protected $rules = [
        'bidAmount' => 'required|numeric|min:0',
    ];

    public function mount(Listing $listing)
    {
        $this->listing = $listing;
        $this->loadBiddingData();
    }

    public function loadBiddingData()
    {
        $this->listing->refresh();
        $this->currentHighestBid = $this->listing->current_highest_bid ?? $this->listing->base_price;
        
        $bidService = app(BidService::class);
        $this->rankings = $bidService->getCurrentRankings($this->listing)
            ->take(10)
            ->toArray();
    }

    public function incrementBid($amount)
    {
        $currentBid = is_numeric($this->bidAmount) ? (int)$this->bidAmount : $this->currentHighestBid;
        $this->bidAmount = $currentBid + $amount;
    }

    public function placeBid()
    {
        $this->validate();
        $this->errorMessage = '';
        $this->successMessage = '';

        try {
            $bidService = app(BidService::class);
            $bidService->placeBid(
                Auth::user(),
                $this->listing,
                $this->bidAmount
            );

            $this->successMessage = 'پیشنهاد شما با موفقیت ثبت شد';
            $this->bidAmount = '';
            $this->loadBiddingData();
            
            $this->dispatch('bid-placed', listingId: $this->listing->id);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    #[On('bid-placed')]
    public function refreshBidding($listingId)
    {
        if ($listingId == $this->listing->id) {
            $this->loadBiddingData();
        }
    }

    public function render()
    {
        return view('livewire.auction-bidding');
    }
}
