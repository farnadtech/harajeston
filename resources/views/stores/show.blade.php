@extends('layouts.app')

@section('content')
<main class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    {{-- Store Profile Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Banner --}}
        <div class="h-48 md:h-64 w-full bg-gradient-to-r from-blue-100 to-indigo-100 relative">
            @if($store->banner_url)
                <img src="{{ $store->banner_url }}" alt="بنر فروشگاه" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gray-900/10"></div>
        </div>

        <div class="px-6 pb-6 relative">
            <div class="flex flex-col md:flex-row items-start md:items-end -mt-16 mb-4 gap-6">
                {{-- Logo --}}
                <div class="w-32 h-32 rounded-2xl bg-white p-2 shadow-lg border border-gray-100 shrink-0 relative z-10">
                    <div class="w-full h-full rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 overflow-hidden">
                        @if($store->logo_url)
                            <img src="{{ $store->logo_url }}" alt="{{ $store->store_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-primary text-5xl">storefront</span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-1 border-2 border-white shadow-sm" title="فروشنده تایید شده">
                        <span class="material-symbols-outlined text-sm">verified</span>
                    </div>
                </div>

                {{-- Store Info --}}
                <div class="flex-1 w-full md:w-auto pt-2 md:pt-0">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                                {{ $store->store_name }}
                                @if($store->is_active)
                                    <span class="bg-blue-100 text-primary text-xs px-2 py-0.5 rounded border border-blue-200 font-medium">رسمی</span>
                                @endif
                            </h1>
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-yellow-500 text-lg">star</span>
                                    <span class="font-bold text-gray-900">۴.۸</span>
                                    <span class="text-xs text-gray-400">(۳۴۲ رای)</span>
                                </span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span>{{ $store->user->name }}</span>
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="text-green-600 font-medium">پاسخگویی سریع</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <button class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors shadow-lg shadow-primary/20 flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl">add</span>
                                دنبال کردن
                            </button>
                            <button class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl">chat</span>
                                پیام
                            </button>
                            <button class="p-2.5 bg-white border border-gray-200 text-gray-500 rounded-xl hover:text-red-500 hover:bg-red-50 hover:border-red-100 transition-colors">
                                <span class="material-symbols-outlined text-xl">flag</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Store Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">تعداد محصولات</span>
                    <span class="block text-lg font-black text-gray-900">{{ $listings->total() }}</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">فروش موفق</span>
                    <span class="block text-lg font-black text-gray-900 text-green-600">۱۲۶</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">عضویت از</span>
                    <span class="block text-lg font-black text-gray-900">{{ \Morilog\Jalali\Jalalian::fromCarbon($store->created_at)->format('Y/m') }}</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">رضایت مشتریان</span>
                    <span class="block text-lg font-black text-gray-900 text-primary">۹۸٪</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200">
        <nav aria-label="Tabs" class="flex gap-8 overflow-x-auto no-scrollbar">
            <a class="border-b-2 border-primary py-4 px-1 text-sm font-bold text-primary whitespace-nowrap flex items-center gap-2" href="#">
                <span class="material-symbols-outlined">gavel</span>
                مزایده‌های فعال
                <span class="bg-primary/10 text-primary text-xs py-0.5 px-2 rounded-full mr-1">{{ $listings->total() }}</span>
            </a>
            <a class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center gap-2 transition-colors" href="#">
                <span class="material-symbols-outlined">sell</span>
                فروش‌های تکمیل شده
            </a>
            <a class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center gap-2 transition-colors" href="#">
                <span class="material-symbols-outlined">info</span>
                درباره فروشگاه
            </a>
        </nav>
    </div>

    {{-- Filters and Sort --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 py-2">
        <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto no-scrollbar pb-2 sm:pb-0">
            <button class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg font-medium whitespace-nowrap transition-colors">همه حراج‌ها</button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm rounded-lg font-medium whitespace-nowrap transition-colors">با خرید فوری</button>
            <button class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm rounded-lg font-medium whitespace-nowrap transition-colors">در حال پایان</button>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span class="text-sm text-gray-500 whitespace-nowrap">مرتب‌سازی:</span>
            <select class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-white">
                <option>جدیدترین</option>
                <option>زمان باقیمانده (کم به زیاد)</option>
                <option>قیمت فعلی (زیاد به کم)</option>
                <option>بیشترین پیشنهاد</option>
            </select>
        </div>
    </div>

    {{-- Products Grid --}}
    @if($listings->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($listings as $listing)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group overflow-hidden flex flex-col">
                    {{-- Product Image --}}
                    <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                        @if($listing->ends_at && $listing->ends_at->diffInHours() < 6)
                            <div class="absolute top-3 right-3 z-10">
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm animate-pulse">فوری</span>
                            </div>
                        @endif

                        @if($listing->hasBuyNowPrice())
                            <div class="absolute top-3 left-3 z-10">
                                <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">bolt</span>
                                    خرید فوری
                                </span>
                            </div>
                        @endif

                        @if($listing->images->count() > 0)
                            <img src="{{ url('storage/' . $listing->images->first()->file_path) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-300 text-6xl">image</span>
                            </div>
                        @endif

                        {{-- Countdown Timer --}}
                        @if($listing->ends_at)
                            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent">
                                <div class="flex items-center justify-center gap-1 text-white font-bold text-sm bg-black/40 backdrop-blur-sm rounded-lg py-1.5 mx-8 border border-white/10">
                                    @if($listing->ends_at->diffInHours() < 24)
                                        <span class="material-symbols-outlined text-sm animate-spin-slow text-secondary">hourglass_top</span>
                                    @endif
                                    <span class="dir-ltr">{{ $listing->ends_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-gray-900 line-clamp-2 mb-2 group-hover:text-primary transition-colors">
                            {{ $listing->title }}
                        </h3>

                        <div class="mt-auto pt-4 border-t border-gray-50">
                            <div class="flex justify-between items-end mb-3">
                                <span class="text-xs text-gray-500">پیشنهاد فعلی</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg font-black text-primary">@price($listing->current_price ?? $listing->starting_price)</span>
                                    <span class="text-xs text-gray-400">تومان</span>
                                </div>
                            </div>

                            @if($listing->hasBuyNowPrice())
                                <div class="flex justify-between items-end mb-3 pb-3 border-b border-gray-100">
                                    <span class="text-xs text-gray-500">خرید فوری</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-green-600">@price($listing->buy_now_price)</span>
                                        <span class="text-xs text-gray-400">تومان</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('listings.show', $listing) }}" class="flex-1 py-2.5 bg-primary/10 hover:bg-primary hover:text-white text-primary font-bold rounded-xl transition-all text-sm text-center">
                                    ثبت پیشنهاد
                                </a>
                                @if($listing->hasBuyNowPrice())
                                    <a href="{{ route('listings.show', $listing) }}#buy-now" class="flex-1 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all text-sm text-center flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-lg">bolt</span>
                                        خرید
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-8">
            {{ $listings->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <span class="material-symbols-outlined text-gray-300 text-8xl mb-4">inventory_2</span>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">هنوز محصولی وجود ندارد</h3>
            <p class="text-gray-500">این فروشگاه هنوز محصولی منتشر نکرده است.</p>
        </div>
    @endif
</main>

<style>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endsection
