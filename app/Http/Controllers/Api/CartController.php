<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function add(AddToCartRequest $request)
    {
        try {
            $cartItem = $this->cartService->addToCart(
                $request->user(),
                $request->listing_id,
                $request->quantity
            );
            
            return response()->json([
                'message' => 'محصول به سبد خرید اضافه شد',
                'cart_item' => $cartItem,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCartWithTotals($request->user());
        
        return response()->json([
            'cart' => $cart,
        ]);
    }

    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->updateCartItem($request->user(), $itemId, $validated['quantity']);
            
            return response()->json([
                'message' => 'سبد خرید به‌روزرسانی شد',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function remove(Request $request, $itemId)
    {
        try {
            $this->cartService->removeFromCart($request->user(), $itemId);
            
            return response()->json([
                'message' => 'محصول از سبد خرید حذف شد',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
