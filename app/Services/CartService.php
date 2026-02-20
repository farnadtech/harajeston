<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Models\User;
use App\Exceptions\DirectSale\OutOfStockException;
use App\Exceptions\Cart\CartItemNotFoundException;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Add item to cart
     */
    public function addToCart(User $user, Listing $listing, int $quantity): CartItem
    {
        // Validate listing type
        if (!in_array($listing->type, ['direct_sale', 'hybrid'])) {
            throw new \InvalidArgumentException('فقط محصولات فروش مستقیم قابل افزودن به سبد خرید هستند.');
        }
        
        // Validate stock
        if ($listing->stock < $quantity) {
            throw new OutOfStockException($listing->id, $listing->title);
        }
        
        return DB::transaction(function () use ($user, $listing, $quantity) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('listing_id', $listing->id)
                ->first();
            
            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($listing->stock < $newQuantity) {
                    throw new OutOfStockException($listing->id, $listing->title);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'listing_id' => $listing->id,
                    'quantity' => $quantity,
                    'price_snapshot' => $listing->price,
                ]);
            }
            
            return $cartItem->fresh();
        });
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(CartItem $cartItem, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('تعداد باید بیشتر از صفر باشد.');
        }
        
        $listing = $cartItem->listing;
        
        if ($listing->stock < $quantity) {
            throw new OutOfStockException($listing->id, $listing->title);
        }
        
        $cartItem->quantity = $quantity;
        $cartItem->save();
        
        return $cartItem->fresh();
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    /**
     * Get cart with calculated totals
     */
    public function getCartWithTotals(User $user): array
    {
        $cart = Cart::where('user_id', $user->id)
            ->with(['items.listing.seller'])
            ->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return [
                'items' => [],
                'subtotal' => 0,
                'shipping' => 0,
                'total' => 0,
            ];
        }
        
        $subtotal = 0;
        
        foreach ($cart->items as $item) {
            $itemTotal = $item->price_snapshot * $item->quantity;
            $subtotal += $itemTotal;
        }
        
        // Simplified shipping calculation
        $shipping = 0;
        
        return [
            'items' => $cart->items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
