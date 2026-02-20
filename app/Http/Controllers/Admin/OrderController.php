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
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(50);

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
}
