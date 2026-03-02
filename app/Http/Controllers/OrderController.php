<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * List orders (buyer or seller view)
     */
    public function index(Request $request)
    {
        $role = $request->get('role', 'buyer');
        $orders = $this->orderService->getOrdersByUser(auth()->user(), $role);

        return view('orders.index', compact('orders', 'role'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items.listing', 'buyer', 'seller', 'shippingMethod');

        return view('orders.show', compact('order'));
    }
    
    /**
     * Update shipping address for pending orders
     */
    public function updateShippingAddress(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        
        // Only buyer can update address
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }
        
        // Only for pending orders
        if ($order->status !== 'pending') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'فقط سفارشات در انتظار قابل ویرایش هستند.');
        }
        
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_phone' => 'required|string|max:20',
        ], [
            'shipping_address.required' => 'آدرس ارسال الزامی است',
            'shipping_city.required' => 'شهر الزامی است',
            'shipping_state.required' => 'استان الزامی است',
            'shipping_postal_code.required' => 'کد پستی الزامی است',
            'shipping_phone.required' => 'شماره تماس الزامی است',
        ]);
        
        // Update order
        $order->update([
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_state' => $validated['shipping_state'],
            'shipping_postal_code' => $validated['shipping_postal_code'],
            'shipping_phone' => $validated['shipping_phone'],
            'status' => 'processing',
        ]);
        
        // Notify seller
        try {
            $order->seller->notify(new \App\Notifications\OrderReadyForProcessingNotification($order));
        } catch (\Exception $e) {
            \Log::warning('Failed to send notification: ' . $e->getMessage());
        }
        
        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'آدرس ارسال با موفقیت ثبت شد. سفارش شما در حال پردازش است.');
    }

    /**
     * Update order status (seller or buyer for delivery confirmation)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->authorize('updateStatus', $order);
        
        // If buyer is confirming delivery, only allow 'delivered' status
        if ($order->buyer_id === auth()->id()) {
            if ($request->status !== 'delivered' || $order->status !== 'shipped') {
                abort(403, 'شما فقط می‌توانید دریافت کالا را تایید کنید');
            }
        }
        
        // If seller is moving from pending to processing, ensure shipping address is provided
        if ($order->seller_id === auth()->id() && 
            $order->status === 'pending' && 
            $request->status === 'processing') {
            if (!$order->shipping_address) {
                return redirect()
                    ->route('orders.show', $order)
                    ->with('error', 'خریدار هنوز آدرس ارسال را وارد نکرده است. لطفا منتظر بمانید تا خریدار آدرس را وارد کند.');
            }
        }

        $this->orderService->updateOrderStatus($order, $request->status);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }

    /**
     * Cancel order (buyer only, within 1 hour)
     */
    public function cancel(Order $order)
    {
        $this->authorize('cancel', $order);

        $this->orderService->cancelOrder($order, auth()->user());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'سفارش با موفقیت لغو شد.');
    }

    /**
     * Release payment to seller early (buyer only, for auction orders)
     */
    public function releasePayment(Order $order)
    {
        // Check authorization
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        // Check if order is delivered
        if ($order->status !== 'delivered') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'فقط سفارشات تحویل داده شده قابل آزادسازی هستند.');
        }

        // Check if already released
        if ($order->payment_released_at) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'پول این سفارش قبلاً آزاد شده است.');
        }

        // Check if it's an auction order
        $isAuctionOrder = $order->items->first()?->listing?->required_deposit > 0;
        if (!$isAuctionOrder) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'این سفارش مربوط به حراجی نیست.');
        }

        try {
            $auctionService = app(\App\Services\AuctionService::class);
            $auctionService->releasePaymentToSeller($order);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'پول فروشنده با موفقیت آزاد شد.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'خطا در آزادسازی پول: ' . $e->getMessage());
        }
    }

    /**
     * Confirm order preparation (seller only)
     */
    public function confirmPreparation(Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        if ($order->status !== 'processing') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'فقط سفارشات در حال پردازش قابل تایید هستند.');
        }

        try {
            $this->orderService->confirmOrderPreparation($order, auth()->user());

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'تهیه اقلام تایید شد. سفارش آماده ارسال است.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    /**
     * Add tracking number and mark as shipped
     */
    public function addTrackingNumber(Request $request, Order $order)
    {
        if ($order->seller_id !== auth()->id()) {
            abort(403, 'شما مجاز به انجام این عملیات نیستید.');
        }

        if ($order->status !== 'processing') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'فقط سفارشات در حال پردازش قابل ارسال هستند.');
        }

        $request->validate([
            'tracking_number' => 'required|string|max:100',
        ], [
            'tracking_number.required' => 'کد رهگیری الزامی است.',
            'tracking_number.max' => 'کد رهگیری نباید بیشتر از 100 کاراکتر باشد.',
        ]);

        try {
            $this->orderService->addTrackingNumber($order, $request->tracking_number, auth()->user());

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'کد رهگیری ثبت شد و سفارش به مرحله ارسال رفت.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order with penalty (seller or buyer, processing status only)
     */
    public function cancelWithPenalty(Request $request, Order $order)
    {
        $user = auth()->user();
        $cancelledBy = null;

        if ($order->seller_id === $user->id) {
            $cancelledBy = 'seller';
        } elseif ($order->buyer_id === $user->id) {
            $cancelledBy = 'buyer';
        } else {
            abort(403, 'شما مجاز به لغو این سفارش نیستید.');
        }

        if ($order->status !== 'processing') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'فقط سفارشات در حال پردازش قابل لغو هستند.');
        }

        try {
            // Get penalty info for confirmation
            $penaltyType = \App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage');
            $penaltyValue = (float) \App\Models\SiteSetting::get('order_cancellation_penalty_value', 10);
            
            if ($penaltyType === 'percentage') {
                $penalty = ($order->total * $penaltyValue) / 100;
            } else {
                $penalty = $penaltyValue;
            }

            $this->orderService->cancelOrderWithPenalty($order, $user, $cancelledBy);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', sprintf('سفارش لغو شد. جریمه لغو: %s تومان از کیف پول شما کسر شد.', number_format($penalty)));
        } catch (\App\Exceptions\Wallet\InsufficientBalanceException $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'موجودی کیف پول شما برای پرداخت جریمه لغو کافی نیست.');
        } catch (\App\Exceptions\Order\InvalidOrderStatusException $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'وضعیت سفارش برای لغو مناسب نیست.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'خطا در لغو سفارش: ' . $e->getMessage());
        }
    }
}
