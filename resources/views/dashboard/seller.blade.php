@extends('layouts.app')

@section('title', 'داشبورد فروشنده')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">داشبورد فروشنده</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">مزایده‌های فعال</h3>
            <p class="text-4xl font-bold">@persian($stats['active_auctions'] ?? 0)</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">فروش مستقیم</h3>
            <p class="text-4xl font-bold">@persian($stats['direct_sales'] ?? 0)</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">سفارشات جدید</h3>
            <p class="text-4xl font-bold">@persian($stats['pending_orders'] ?? 0)</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">کل فروش</h3>
            <p class="text-2xl font-bold">@currency($stats['total_sales'] ?? 0)</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">عملیات سریع</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ url('/listings/create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center">
                ایجاد آگهی جدید
            </a>
            <a href="{{ url('/store/' . auth()->user()->username) }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition text-center">
                مشاهده فروشگاه
            </a>
            <a href="{{ route('wallet.show') }}" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition text-center">
                کیف پول
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">سفارشات اخیر</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right">شماره سفارش</th>
                        <th class="px-4 py-3 text-right">خریدار</th>
                        <th class="px-4 py-3 text-right">مبلغ</th>
                        <th class="px-4 py-3 text-right">وضعیت</th>
                        <th class="px-4 py-3 text-right">تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->buyer->name }}</td>
                            <td class="px-4 py-3">@currency($order->total)</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                    {{ __('orders.' . $order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">@jalali($order->created_at)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                هیچ سفارشی یافت نشد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    @if(isset($lowStockItems) && count($lowStockItems) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4 text-yellow-800">هشدار موجودی کم</h2>
            <div class="space-y-2">
                @foreach($lowStockItems as $item)
                    <div class="flex justify-between items-center p-3 bg-white rounded">
                        <span>{{ $item->title }}</span>
                        <span class="text-red-600 font-bold">موجودی: @persian($item->stock)</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
