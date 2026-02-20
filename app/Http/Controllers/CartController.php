<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\Listing;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display cart with items and totals
     */
    public function index()
    {
        $cart = $this->cartService->getCartWithTotals(auth()->user());

        return view('cart.index', compact('cart'));
    }

    /**
     * Add item to cart
     */
    public function add(AddToCartRequest $request)
    {
        $listing = Listing::findOrFail($request->listing_id);

        $this->cartService->addToCart(
            auth()->user(),
            $listing,
            $request->quantity
        );

        return redirect()
            ->route('cart.index')
            ->with('success', 'محصول به سبد خرید اضافه شد.');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, int $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $this->cartService->updateCartItem($cartItemId, $request->quantity);

        return redirect()
            ->route('cart.index')
            ->with('success', 'تعداد محصول به‌روزرسانی شد.');
    }

    /**
     * Remove item from cart
     */
    public function remove(int $cartItemId)
    {
        $this->cartService->removeFromCart($cartItemId);

        return redirect()
            ->route('cart.index')
            ->with('success', 'محصول از سبد خرید حذف شد.');
    }
}
