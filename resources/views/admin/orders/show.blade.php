@extends('layouts.app')

@section('title', 'جزئیات سفارش')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">جزئیات سفارش</h1>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            بازگشت به لیست
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">سفارش {{ $order->order_number }}</h2>
            <div class="space-y-3">
                <div><strong>خریدار:</strong> {{ $order->buyer->name }}</div>
                <div><strong>فروشنده:</strong> {{ $order->seller->name }}</div>
                <div><strong>وضعیت:</strong> {{ $order->status }}</div>
                <div><strong>مبلغ کل:</strong> @currency($order->total)</div>
                <div><strong>هزینه ارسال:</strong> @currency($order->shipping_cost)</div>
                <div><strong>تاریخ:</strong> @jalali($order->created_at)</div>
                @if($order->shipping_address)
                    <div><strong>آدرس:</strong> {{ $order->shipping_address }}</div>
                @endif
                @if($order->tracking_number)
                    <div><strong>کد رهگیری:</strong> {{ $order->tracking_number }}</div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold mb-4">اقلام سفارش</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="border-b pb-3">
                        <div class="font-bold">{{ $item->listing->title }}</div>
                        <div class="text-sm text-gray-600">
                            تعداد: @persian($item->quantity) × @currency($item->price_snapshot)
                            = @currency($item->subtotal)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
