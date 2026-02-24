<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Exceptions\Cart\CartEmptyException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Order\InvalidOrderStatusException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OrderService
{
    protected $walletService;
    protected $listingService;
    protected $cartService;

    public function __construct(
        WalletService $walletService,
        ListingService $listingService,
        CartService $cartService
    ) {
        $this->walletService = $walletService;
        $this->listingService = $listingService;
        $this->cartService = $cartService;
    }

    /**
     * Create order from buy now
     */
    public function createOrderFromBuyNow(\App\Models\Listing $listing, User $buyer): Order
    {
        return DB::transaction(function () use ($listing, $buyer) {
            // Validate listing is available for buy now
            if (!$listing->buy_now_price || $listing->buy_now_price <= 0) {
                throw new \InvalidArgumentException('خرید فوری برای این حراجی فعال نیست.');
            }

            if (!$listing->isActive()) {
                throw new \InvalidArgumentException('این حراجی فعال نیست.');
            }

            // Check wallet balance
            $wallet = $buyer->wallet;
            if (!$wallet || $wallet->balance < $listing->buy_now_price) {
                throw new \App\Exceptions\Wallet\InsufficientBalanceException(
                    $buyer->id,
                    $listing->buy_now_price,
                    $wallet ? $wallet->balance : 0
                );
            }

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'buyer_id' => $buyer->id,
                'seller_id' => $listing->seller_id,
                'status' => 'pending',
                'subtotal' => $listing->buy_now_price,
                'shipping_cost' => 0, // Will be set when shipping method is selected
                'total' => $listing->buy_now_price,
                'shipping_address' => null, // Will be set later
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'listing_id' => $listing->id,
                'quantity' => 1,
                'price_snapshot' => $listing->buy_now_price,
                'subtotal' => $listing->buy_now_price,
            ]);

            // Process payment - deduct from buyer
            $this->walletService->deduct(
                $buyer,
                $listing->buy_now_price,
                'خرید فوری حراجی: ' . $listing->title,
                $order
            );

            // Add to seller (will be held until delivery confirmation)
            $this->walletService->addFunds(
                $listing->seller,
                $listing->buy_now_price,
                'فروش خرید فوری: ' . $listing->title
            );

            // End the auction
            $listing->status = 'sold';
            $listing->save();

            // Send notifications
            $buyer->notify(new \App\Notifications\OrderPlacedNotification($order, false));
            $listing->seller->notify(new \App\Notifications\OrderPlacedNotification($order, true));

            return $order->fresh();
        });
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart(User $buyer, array $shippingData): Collection
    {
        return DB::transaction(function () use ($buyer, $shippingData) {
            $cart = Cart::where('user_id', $buyer->id)
                ->with('items.listing.seller')
                ->lockForUpdate()
                ->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                throw new CartEmptyException('سبد خرید خالی است.');
            }
            
            // Group items by seller
            $itemsBySeller = $cart->items->groupBy('listing.seller_id');
            $orders = collect();
            
            foreach ($itemsBySeller as $sellerId => $items) {
                // Validate stock for all items
                foreach ($items as $item) {
                    if ($item->listing->stock < $item->quantity) {
                        throw new \App\Exceptions\DirectSale\OutOfStockException(
                            $item->listing->id,
                            $item->listing->title
                        );
                    }
                }
                
                // Calculate totals
                $subtotal = $items->sum(fn($item) => $item->price_snapshot * $item->quantity);
                $shippingCost = 0; // Simplified
                $total = $subtotal + $shippingCost;
                
                // Create order
                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'buyer_id' => $buyer->id,
                    'seller_id' => $sellerId,
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'shipping_address' => $shippingData['address'] ?? null,
                ]);
                
                // Create order items and decrement stock
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'listing_id' => $item->listing_id,
                        'quantity' => $item->quantity,
                        'price_snapshot' => $item->price_snapshot,
                        'subtotal' => $item->price_snapshot * $item->quantity,
                    ]);
                    
                    $this->listingService->decrementStock($item->listing, $item->quantity);
                }
                
                // Process payment
                $this->walletService->deduct($buyer, $total, 'پرداخت سفارش ' . $order->order_number, $order);
                $this->walletService->addFunds(
                    User::find($sellerId),
                    $total,
                    'دریافت پرداخت سفارش ' . $order->order_number
                );
                
                // Send notifications
                $buyer->notify(new \App\Notifications\OrderPlacedNotification($order, false));
                User::find($sellerId)->notify(new \App\Notifications\OrderPlacedNotification($order, true));
                
                $orders->push($order);
            }
            
            // Clear cart
            $this->cartService->clearCart($cart);
            
            return $orders;
        });
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $newStatus): Order
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new InvalidOrderStatusException($order->status, $newStatus);
        }
        
        $oldStatus = $order->status;
        $order->status = $newStatus;
        $order->save();
        
        // Send notification to buyer
        $order->buyer->notify(new \App\Notifications\OrderStatusUpdatedNotification($order, $oldStatus));
        
        return $order->fresh();
    }

    /**
     * Cancel order (within 1 hour, status pending)
     */
    public function cancelOrder(Order $order, User $user): Order
    {
        // Validate cancellation eligibility
        if ($order->buyer_id !== $user->id) {
            throw new \InvalidArgumentException('فقط خریدار می‌تواند سفارش را لغو کند.');
        }
        
        if ($order->status !== 'pending') {
            throw new InvalidOrderStatusException($order->status, 'cancelled');
        }
        
        $oneHourAgo = Carbon::now()->subHour();
        if ($order->created_at->lt($oneHourAgo)) {
            throw new \InvalidArgumentException('زمان لغو سفارش (1 ساعت) گذشته است.');
        }
        
        return DB::transaction(function () use ($order) {
            // Refund buyer
            $this->walletService->refund(
                $order->buyer,
                $order->total,
                'بازگشت وجه سفارش لغو شده ' . $order->order_number,
                $order
            );
            
            // Deduct from seller
            $this->walletService->deduct(
                $order->seller,
                $order->total,
                'بازگشت وجه سفارش لغو شده ' . $order->order_number,
                $order
            );
            
            // Restore stock
            foreach ($order->items as $item) {
                $this->listingService->incrementStock($item->listing, $item->quantity);
            }
            
            // Update order status
            $order->status = 'cancelled';
            $order->save();
            
            return $order->fresh();
        });
    }

    /**
     * Get orders by user (buyer or seller)
     */
    public function getOrdersByUser(User $user, string $role = 'buyer'): Collection
    {
        $query = Order::query();
        
        if ($role === 'buyer') {
            $query->where('buyer_id', $user->id);
        } else {
            $query->where('seller_id', $user->id);
        }
        
        return $query->with(['items.listing', 'buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Generate unique order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (Order::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }
}
