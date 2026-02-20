<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function process(CheckoutRequest $request)
    {
        try {
            $orders = $this->orderService->createOrderFromCart(
                $request->user(),
                $request->shipping_address,
                $request->shipping_methods ?? []
            );
            
            return response()->json([
                'message' => 'سفارش با موفقیت ثبت شد',
                'orders' => $orders,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
