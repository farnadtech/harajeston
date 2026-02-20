@extends('layouts.app')

@section('title', 'داشبورد خریدار')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">داشبورد خریدار</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">مزایده‌های فعال</h3>
            <p class="text-4xl font-bold">@persian($stats['active_bids'] ?? 0)</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">خریدهای اخیر</h3>
            <p class="text-4xl font-bold">@persian($stats['recent_purchases'] ?? 0)</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">سپرده مسدود</h3>
            <p class="text-2xl font-bold">@currency($stats['frozen_deposits'] ?? 0)</p>
        </div>
    </div>

    <!-- Active Bids -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">پیشنهادات فعال من</h2>
        <div class="space-y-4">
            @forelse($activeBids ?? [] as $bid)
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold mb-2">{{ $bid->listing->title }}</h3>
                            <p class="text-gray-600 mb-2">
                                پیشنهاد شما: <span class="font-bold text-blue-600">@currency($bid->amount)</span>
                            </p>
                            <p class="text-gray-600">
                                بالاترین پیشنهاد: <span class="font-bold">@currency($bid->listing->current_highest_bid)</span>
                            </p>
                        </div>
                        <a href="{{ route('listings.show', $bid->listing_id) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            مشاهده
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-8">شما در هیچ مزایده‌ای شرکت نکرده‌اید</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">سفارشات اخیر</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right">شماره سفارش</th>
                        <th class="px-4 py-3 text-right">فروشنده</th>
                        <th class="px-4 py-3 text-right">مبلغ</th>
                        <th class="px-4 py-3 text-right">وضعیت</th>
                        <th class="px-4 py-3 text-right">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->seller->name }}</td>
                            <td class="px-4 py-3">@currency($order->total)</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">
                                    {{ __('orders.' . $order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:underline">
                                    جزئیات
                                </a>
                            </td>
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
</div>
@endsection
