<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class WalletBalance extends Component
{
    public $balance = 0;
    public $frozen = 0;
    public $available = 0;

    public function mount()
    {
        $this->loadBalance();
    }

    public function loadBalance()
    {
        if (Auth::check() && Auth::user()->wallet) {
            $wallet = Auth::user()->wallet;
            $this->balance = $wallet->balance;
            $this->frozen = $wallet->frozen;
            $this->available = $wallet->balance;
        }
    }

    #[On('wallet-updated')]
    public function refreshBalance()
    {
        $this->loadBalance();
    }

    public function render()
    {
        return view('livewire.wallet-balance');
    }
}
