<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AuctionParticipation extends Component
{
    public Listing $listing;
    public $hasParticipated = false;
    public $errorMessage = '';
    public $successMessage = '';

    public function mount(Listing $listing)
    {
        $this->listing = $listing;
        $this->checkParticipation();
    }

    public function checkParticipation()
    {
        if (Auth::check()) {
            $this->hasParticipated = $this->listing->participations()
                ->where('user_id', Auth::id())
                ->where('deposit_status', 'paid')
                ->exists();
        }
    }

    public function participate()
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        if (!Auth::check()) {
            $this->errorMessage = 'برای شرکت در مزایده ابتدا وارد شوید';
            return;
        }

        try {
            $depositService = app(DepositService::class);
            $depositService->participateInAuction(Auth::id(), $this->listing->id);

            $this->successMessage = 'شما با موفقیت در مزایده شرکت کردید';
            $this->hasParticipated = true;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.auction-participation');
    }
}
