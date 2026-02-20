<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class CartSummary extends Component
{
    public $itemCount = 0;
    public $total = 0;
    public $items = [];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        if (Auth::check()) {
            $cartService = app(CartService::class);
            $cartData = $cartService->getCartWithTotals(Auth::user());
            
            $this->itemCount = collect($cartData['items'])->sum('quantity');
            $this->total = $cartData['grand_total'] ?? 0;
            $this->items = collect($cartData['items'])->take(5)->toArray();
        }
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart-summary');
    }
}
