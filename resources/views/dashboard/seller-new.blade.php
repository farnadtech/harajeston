@extends('layouts.seller')

@section('title', 'داشبورد فروشنده')

@section('page-title', 'خوش آمدید، ' . (optional(auth()->user()->store)->store_name ?? auth()->user()->name) . ' 👋')
@section('page-subtitle', 'خلاصه وضعیت فروشگاه شما امروز')

@section('content')
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">payments</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">درآمد کل</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">
                    @persian(number_format($stats['total_sales'] ?? 0))
                    <span class="text-xs font-normal text-gray-400">تومان</span>
                </h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">مزایده‌های فعال</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['active_auctions'] ?? 0)</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <span class="material-symbols-outlined">pending</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">در انتظار تایید</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['pending_listings'] ?? 0)</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">تکمیل شده</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['completed_auctions'] ?? 0)</h3>
            </div>
        </div>
    </div>

    <!-- Sales Chart & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">نمودار فروش هفتگی</h3>
                <p class="text-sm text-gray-500 mt-1">آمار فروش ۷ روز اخیر</p>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-end justify-between gap-2">
                    @php
                        $maxSales = 1000000;
                        $weekDays = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
                        $salesData = [300000, 450000, 200000, 600000, 800000, 350000, 500000];
                    @endphp
                    @foreach($salesData as $index => $sale)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-primary/10 rounded-t-lg relative group cursor-pointer hover:bg-primary/20 transition-colors" style="height: {{ ($sale / $maxSales) * 100 }}%">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    @persian(number_format($sale)) تومان
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">{{ $weekDays[$index] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">فعالیت‌های اخیر</h3>
                <p class="text-sm text-gray-500 mt-1">آخرین رویدادها</p>
            </div>
            <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-xl">shopping_bag</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">فروش جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۳۴ تکمیل شد</p>
                        <p class="text-xs text-gray-400 mt-1">۲ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-xl">gavel</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">پیشنهاد جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">پیشنهاد ۵۰۰,۰۰۰ تومان</p>
                        <p class="text-xs text-gray-400 mt-1">۳ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-orange-600 text-xl">local_shipping</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">ارسال محصول</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۳۰ ارسال شد</p>
                        <p class="text-xs text-gray-400 mt-1">۵ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-xl">shopping_bag</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">فروش جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۲۸ تکمیل شد</p>
                        <p class="text-xs text-gray-400 mt-1">۱ روز پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-xl">gavel</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">پیشنهاد جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">پیشنهاد ۷۵۰,۰۰۰ تومان</p>
                        <p class="text-xs text-gray-400 mt-1">۱ روز پیش</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Listings Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">مزایده‌های فعال من</h3>
                <p class="text-sm text-gray-500 mt-1">لیست مزایده‌های در حال برگزاری فروشگاه شما</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('listings.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">
                    افزودن مزایده
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">محصول</th>
                        <th class="px-6 py-4">قیمت فعلی</th>
                        <th class="px-6 py-4">زمان باقی‌مانده</th>
                        <th class="px-6 py-4 text-center">وضعیت</th>
                        <th class="px-6 py-4 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activeListings ?? [] as $listing)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($listing->images->count() > 0)
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                            <img alt="{{ $listing->title }}" class="w-full h-full object-cover" src="{{ Storage::url($listing->images->first()->image_path) }}"/>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-gray-400">image</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $listing->title }}</p>
                                        <p class="text-xs text-gray-500">شروع: @persian(number_format($listing->starting_price)) تومان</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">
                                    @persian(number_format($listing->current_price))
                                    <span class="text-xs font-normal text-gray-500">تومان</span>
                                </div>
                                @if($listing->current_price > $listing->starting_price)
                                    <div class="text-xs text-green-500 mt-0.5">
                                        +@persian(number_format((($listing->current_price - $listing->starting_price) / $listing->starting_price) * 100, 0))٪
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($listing->ends_at > now())
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ $listing->ends_at->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-sm font-medium text-red-600">پایان یافته</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($listing->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        در جریان
                                    </span>
                                @elseif($listing->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        در انتظار تایید
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $listing->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('listings.show', $listing) }}" class="p-1.5 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="مشاهده">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="material-symbols-outlined text-5xl text-gray-300">inventory_2</span>
                                    <p class="text-gray-500">هیچ مزایده فعالی وجود ندارد</p>
                                    <a href="{{ route('listings.create') }}" class="mt-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                                        ایجاد اولین مزایده
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Orders -->
    @if(isset($recentOrders) && count($recentOrders) > 0)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">سفارشات اخیر</h3>
            <p class="text-sm text-gray-500 mt-1">آخرین سفارشات دریافت شده</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">شماره سفارش</th>
                        <th class="px-6 py-4">خریدار</th>
                        <th class="px-6 py-4">مبلغ</th>
                        <th class="px-6 py-4">وضعیت</th>
                        <th class="px-6 py-4">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-sm">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $order->buyer->name }}</td>
                            <td class="px-6 py-4 text-sm font-bold">@persian(number_format($order->total)) تومان</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
