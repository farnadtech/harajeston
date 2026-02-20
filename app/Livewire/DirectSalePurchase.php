<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class DirectSalePurchase extends Component
{
    public Listing $listing;
    public $quantity = 1;
    public $stock;
    public $errorMessage = '';
    public $successMessage = '';

    protected $rules = [
        'quantity' => 'required|integer|min:1',
    ];

    public function mount(Listing $listing)
    {
        $this->listing = $listing;
        $this->loadStock();
    }

    public function loadStock()
    {
        $this->listing->refresh();
        $this->stock = $this->listing->stock ?? 0;
    }

    public function addToCart()
    {
        $this->validate();
        $this->errorMessage = '';
        $this->successMessage = '';

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $cartService = app(CartService::class);
            $cartService->addToCart(Auth::user(), $this->listing, $this->quantity);

            $this->successMessage = 'محصول به سبد خرید اضافه شد';
            $this->quantity = 1;
            $this->loadStock();
            
            $this->dispatch('cart-updated');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function buyNow()
    {
        $this->validate();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $cartService = app(CartService::class);
            $cartService->addToCart(Auth::user(), $this->listing, $this->quantity);
            
            return redirect()->route('checkout.show');
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    #[On('stock-updated')]
    public function refreshStock($listingId)
    {
        if ($listingId == $this->listing->id) {
            $this->loadStock();
        }
    }

    public function render()
    {
        return view('livewire.direct-sale-purchase');
    }
}
