<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * List all orders with filters
     */
    public function index(Request $request)
    {
        $query = Order::with('buyer', 'seller', 'items');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number, buyer name, or seller name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show detailed order view
     */
    public function show(Order $order)
    {
        $order->load('buyer', 'seller', 'items.listing', 'shippingMethod');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'وضعیت سفارش با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Update shipping information
     */
    public function updateShipping(Request $request, Order $order)
    {
        $request->validate([
            'shipping_address' => 'nullable|string|max:500',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_phone' => 'nullable|string|max:20',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order->update($request->only([
            'shipping_address',
            'shipping_city',
            'shipping_postal_code',
            'shipping_phone',
            'tracking_number'
        ]));

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'اطلاعات ارسال با موفقیت به‌روزرسانی شد.');
    }
}
