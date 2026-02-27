@extends('layouts.app')

@section('title', 'داشبورد خریدار')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 mb-2">داشبورد خریدار</h1>
        <p class="text-gray-600">خوش آمدید، {{ auth()->user()->name }} عزیز 👋</p>
    </div>

    <!-- Become Seller Card -->
    @if(auth()->user()->seller_status === 'none')
        <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1 text-center md:text-right">
                    <h2 class="text-3xl font-black mb-3">فروشنده شوید!</h2>
                    <p class="mb-6 text-lg opacity-90">با ثبت‌نام به عنوان فروشنده، محصولات خود را در پلتفرم ما به فروش برسانید و درآمد کسب کنید.</p>
                    <a href="{{ route('seller-request.create') }}" class="inline-flex items-center gap-2 bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl">
                        <span class="material-symbols-outlined">storefront</span>
                        درخواست فروشندگی
                    </a>
                </div>
                <div class="hidden md:block">
                    <span class="material-symbols-outlined" style="font-size: 120px; opacity: 0.2;">storefront</span>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->seller_status === 'pending')
        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-8 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-yellow-600 text-4xl">schedule</span>
                </div>
                <div class="flex-1 text-center md:text-right">
                    <h3 class="text-2xl font-black text-yellow-900 mb-2">درخواست فروشندگی در انتظار تایید</h3>
                    <p class="text-yellow-800 text-lg">درخواست شما در حال بررسی است. پس از تایید مدیریت، می‌توانید محصولات خود را اضافه کنید.</p>
                </div>
                <a href="{{ route('seller-request.status') }}" class="inline-flex items-center gap-2 bg-yellow-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-yellow-700 transition-all shadow-md hover:shadow-lg shrink-0">
                    <span class="material-symbols-outlined">visibility</span>
                    مشاهده وضعیت
                </a>
            </div>
        </div>
    @elseif(auth()->user()->seller_status === 'rejected')
        <div class="bg-gradient-to-br from-red-50 to-rose-50 border-2 border-red-200 rounded-2xl p-8 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-red-600 text-4xl">cancel</span>
                </div>
                <div class="flex-1 text-center md:text-right">
                    <h3 class="text-2xl font-black text-red-900 mb-2">درخواست فروشندگی رد شد</h3>
                    <p class="text-red-800 mb-3">
                        <span class="font-bold">دلیل رد:</span> {{ auth()->user()->seller_rejection_reason ?? 'اطلاعات ارسالی کامل نبود' }}
                    </p>
                    <p class="text-red-700">می‌توانید با رفع مشکلات، مجدداً درخواست فروشندگی دهید.</p>
                </div>
                <a href="{{ route('seller-request.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition-all shadow-md hover:shadow-lg shrink-0">
                    <span class="material-symbols-outlined">refresh</span>
                    درخواست مجدد
                </a>
            </div>
        </div>
    @elseif(auth()->user()->seller_status === 'suspended')
        <div class="bg-gradient-to-br from-orange-50 to-red-50 border-2 border-orange-200 rounded-2xl p-8 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-orange-600 text-4xl">block</span>
                </div>
                <div class="flex-1 text-center md:text-right">
                    <h3 class="text-2xl font-black text-orange-900 mb-2">حساب فروشندگی شما تعلیق شده است</h3>
                    <p class="text-orange-800 mb-3">
                        <span class="font-bold">دلیل تعلیق:</span> {{ auth()->user()->seller_rejection_reason ?? 'نقض قوانین پلتفرم' }}
                    </p>
                    <p class="text-orange-700 mb-4">برای اطلاعات بیشتر با پشتیبانی تماس بگیرید.</p>
                    <a href="{{ route('seller-request.create') }}" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg">
                        <span class="material-symbols-outlined">refresh</span>
                        درخواست مجدد
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-purple-600 text-3xl">gavel</span>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500 font-medium mb-1">مزایده‌های فعال</h3>
                    <p class="text-3xl font-black text-gray-900">@persian($stats['active_bids'] ?? 0)</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-green-600 text-3xl">shopping_bag</span>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500 font-medium mb-1">خریدهای اخیر</h3>
                    <p class="text-3xl font-black text-gray-900">@persian($stats['recent_purchases'] ?? 0)</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-orange-600 text-3xl">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500 font-medium mb-1">سپرده مسدود</h3>
                    <p class="text-2xl font-black text-gray-900">@price($stats['frozen_deposits'] ?? 0)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Bids -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900">پیشنهادات فعال من</h2>
            <a href="{{ route('listings.index') }}" class="text-primary hover:text-blue-700 font-bold text-sm flex items-center gap-1">
                <span>مشاهده همه مزایده‌ها</span>
                <span class="material-symbols-outlined text-lg">arrow_back</span>
            </a>
        </div>
        <div class="space-y-4">
            @forelse($activeBids ?? [] as $bid)
                <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md hover:border-primary transition-all">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $bid->listing->title }}</h3>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">پیشنهاد شما:</span>
                                    <span class="font-bold text-blue-600">@price($bid->amount) تومان</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">بالاترین پیشنهاد:</span>
                                    <span class="font-bold text-gray-900">@price($bid->listing->current_price) تومان</span>
                                </div>
                                @if($bid->listing->ends_at)
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-gray-400 text-sm">schedule</span>
                                        <span class="text-gray-600">پایان: {{ \Morilog\Jalali\Jalalian::fromDateTime($bid->listing->ends_at)->format('Y/m/d H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('listings.show', $bid->listing) }}" 
                           class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-sm hover:shadow-md shrink-0">
                            <span class="material-symbols-outlined">visibility</span>
                            مشاهده
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-gray-300 mb-4" style="font-size: 80px;">gavel</span>
                    <p class="text-gray-500 text-lg mb-4">شما در هیچ مزایده‌ای شرکت نکرده‌اید</p>
                    <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all">
                        <span class="material-symbols-outlined">search</span>
                        مشاهده مزایده‌ها
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-2xl font-black text-gray-900">سفارشات اخیر</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">شماره سفارش</th>
                        <th class="px-6 py-4">فروشنده</th>
                        <th class="px-6 py-4">مبلغ</th>
                        <th class="px-6 py-4 text-center">وضعیت</th>
                        <th class="px-6 py-4 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders ?? [] as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">#@persian($order->id)</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-700">{{ $order->seller->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">@price($order->total)</span>
                                <span class="text-xs text-gray-500">تومان</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($order->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">در انتظار پرداخت</span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">پرداخت شده</span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">در حال پردازش</span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">ارسال شده</span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">تکمیل شده</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">لغو شده</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ order_status_color($order->status) }}">
                                        {{ order_status_label($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 text-primary hover:text-blue-700 font-bold text-sm">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                    جزئیات
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-gray-300 mb-2" style="font-size: 60px;">receipt_long</span>
                                <p class="text-gray-500">هیچ سفارشی یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
