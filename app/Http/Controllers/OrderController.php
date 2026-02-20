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
     * Update order status (seller only)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->authorize('updateStatus', $order);

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
}
