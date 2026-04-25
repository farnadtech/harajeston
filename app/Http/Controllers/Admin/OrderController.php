<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\CsvExportTrait;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use CsvExportTrait;
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

    public function export(Request $request)
    {
        $statusLabels = [
            'pending' => 'در انتظار', 'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده', 'delivered' => 'تحویل داده شده',
            'completed' => 'تکمیل شده', 'cancelled' => 'لغو شده',
            'refunded' => 'بازگشت وجه', 'paid' => 'پرداخت شده',
        ];
        $query = Order::with('buyer', 'seller');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('order_number', 'like', "%$s%")
                ->orWhereHas('buyer', fn($q) => $q->where('name', 'like', "%$s%")));
        }
        $orders = $query->orderBy('created_at', 'desc')->get();

        $rows = $orders->map(fn($o) => [
            $o->order_number, $o->buyer->name ?? '', $o->seller->name ?? '',
            $statusLabels[$o->status] ?? $o->status,
            number_format($o->subtotal ?? 0),
            number_format($o->shipping_cost ?? 0),
            number_format($o->total ?? 0),
            $o->shipping_city ?? '', $o->tracking_number ?? '',
            $this->jalali($o->created_at),
        ]);

        return $this->csvResponse('orders-' . date('Y-m-d') . '.csv', [
            'شماره سفارش', 'خریدار', 'فروشنده', 'وضعیت',
            'جمع کالا', 'هزینه ارسال', 'مجموع',
            'شهر', 'کد رهگیری', 'تاریخ ثبت',
        ], $rows);
    }
}
