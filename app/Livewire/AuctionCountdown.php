<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;

class AuctionCountdown extends Component
{
    public Listing $listing;

    public function mount(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function render()
    {
        return view('livewire.auction-countdown');
    }
}
