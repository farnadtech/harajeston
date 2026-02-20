<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    /**
     * Display checkout page
     */
    public function show()
    {
        $cart = $this->cartService->getCartWithTotals(auth()->user());

        if (!$cart || empty($cart['items'])) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'سبد خرید شما خالی است.');
        }

        return view('checkout.show', compact('cart'));
    }

    /**
     * Process checkout and create order
     */
    public function process(CheckoutRequest $request)
    {
        $orders = $this->orderService->createOrderFromCart(
            auth()->user(),
            $request->validated()
        );

        return redirect()
            ->route('orders.index')
            ->with('success', 'سفارش شما با موفقیت ثبت شد.');
    }
}
