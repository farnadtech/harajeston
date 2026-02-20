@extends('layouts.app')

@section('title', 'مدیریت سفارشات')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت سفارشات</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            بازگشت به داشبورد
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">شماره سفارش</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">خریدار</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">فروشنده</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مبلغ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وضعیت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عملیات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders ?? [] as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->buyer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->seller->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">@currency($order->total)</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">{{ $order->status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">@jalali($order->created_at)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">مشاهده</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">سفارشی یافت نشد</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($orders))
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
