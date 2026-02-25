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

    /**
     * Show auction checkout page
     */
    public function auctionCheckout(\App\Models\Listing $listing)
    {
        // Verify user is the winner
        if ($listing->current_winner_id !== auth()->id()) {
            abort(403, 'شما برنده این حراجی نیستید');
        }
        
        // Verify auction is ended
        if ($listing->status !== 'ended') {
            abort(403, 'این حراجی هنوز به پایان نرسیده است');
        }
        
        // Get winning bid
        $winningBid = \App\Models\Bid::where('listing_id', $listing->id)
            ->where('user_id', auth()->id())
            ->orderBy('amount', 'desc')
            ->first();
        
        if (!$winningBid) {
            abort(404, 'پیشنهاد برنده یافت نشد');
        }
        
        // Calculate amounts
        $depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
        $depositAmount = (int) ($listing->starting_price * ($depositPercentage / 100));
        $totalAmount = $winningBid->amount;
        $remainingAmount = $totalAmount - $depositAmount;
        
        // Get available shipping methods
        $shippingMethods = $listing->shippingMethods;
        
        return view('checkout.auction', compact('listing', 'winningBid', 'depositAmount', 'totalAmount', 'remainingAmount', 'shippingMethods'));
    }
    
    /**
     * Process auction checkout
     */
    public function processAuctionCheckout(\App\Models\Listing $listing, \Illuminate\Http\Request $request)
    {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_phone' => 'required|string|max:20',
        ], [
            'shipping_method_id.required' => 'انتخاب روش ارسال الزامی است.',
            'shipping_method_id.exists' => 'روش ارسال انتخاب شده نامعتبر است.',
            'shipping_address.required' => 'آدرس ارسال الزامی است.',
            'shipping_address.max' => 'آدرس ارسال نباید بیشتر از 500 کاراکتر باشد.',
            'shipping_city.required' => 'شهر الزامی است.',
            'shipping_city.max' => 'نام شهر نباید بیشتر از 100 کاراکتر باشد.',
            'shipping_postal_code.required' => 'کد پستی الزامی است.',
            'shipping_postal_code.max' => 'کد پستی نباید بیشتر از 20 کاراکتر باشد.',
            'shipping_phone.required' => 'شماره تماس الزامی است.',
            'shipping_phone.max' => 'شماره تماس نباید بیشتر از 20 کاراکتر باشد.',
        ]);
        
        // Verify user is the winner
        if ($listing->current_winner_id !== auth()->id()) {
            return back()->with('error', 'شما برنده این حراجی نیستید');
        }
        
        // Verify auction is ended
        if ($listing->status !== 'ended') {
            return back()->with('error', 'این حراجی هنوز به پایان نرسیده است');
        }
        
        try {
            $auctionService = app(\App\Services\AuctionService::class);
            $order = $auctionService->finalizeAuctionWithShipping(
                $listing,
                auth()->user(),
                $request->all()
            );
            
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'سفارش شما با موفقیت ثبت شد');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

}
