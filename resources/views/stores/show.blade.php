@extends('layouts.app')

@section('content')
<main class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    {{-- Store Profile Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Banner --}}
        <div class="h-48 md:h-64 w-full bg-gradient-to-r from-blue-100 to-indigo-100 relative">
            @if($store->banner_image)
                <img src="{{ url('storage/' . $store->banner_image) }}" alt="بنر فروشگاه" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gray-900/10"></div>
        </div>

        <div class="px-6 py-6">
            <div class="flex flex-col md:flex-row items-start gap-6 mb-6">
                {{-- Logo --}}
                <div class="w-32 h-32 rounded-2xl bg-white p-2 shadow-lg border border-gray-100 shrink-0 relative">
                    <div class="w-full h-full rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 overflow-hidden">
                        @if($store->logo_image)
                            <img src="{{ url('storage/' . $store->logo_image) }}" alt="{{ $store->store_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-primary text-5xl">storefront</span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full p-1 border-2 border-white shadow-sm" title="فروشنده تایید شده">
                        <span class="material-symbols-outlined text-sm">verified</span>
                    </div>
                </div>

                {{-- Store Info --}}
                <div class="flex-1 w-full md:w-auto">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-black text-gray-900">
                                {{ $store->store_name }}
                            </h1>
                            <div class="flex items-center gap-3 mt-2">
                                @if($seller->seller_rating > 0)
                                    <span class="flex items-center gap-1 text-sm">
                                        <span class="material-symbols-outlined text-yellow-500 text-lg">star</span>
                                        <span class="font-bold text-gray-900">@persian(number_format($seller->seller_rating, 1))</span>
                                        @if($seller->seller_rating_count > 0)
                                            <span class="text-xs text-gray-400">(@persian($seller->seller_rating_count) نظر)</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="bg-blue-50 text-primary text-xs px-2 py-1 rounded-lg font-bold">فروشگاه جدید</span>
                                @endif
                                @if($seller->seller_rating >= 4.5)
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span class="text-green-600 font-medium text-sm">پاسخگویی سریع</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 flex-wrap">
                            <button onclick="alert('قابلیت دنبال کردن به زودی اضافه خواهد شد')" class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors shadow-lg shadow-primary/20 flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl">add</span>
                                دنبال کردن
                            </button>
                            <button onclick="alert('قابلیت پیام‌رسانی به زودی اضافه خواهد شد')" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl">chat</span>
                                پیام
                            </button>
                            <button onclick="alert('قابلیت گزارش تخلف به زودی اضافه خواهد شد')" class="p-2.5 bg-white border border-gray-200 text-gray-500 rounded-xl hover:text-red-500 hover:bg-red-50 hover:border-red-100 transition-colors" title="گزارش تخلف">
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
                    <span class="block text-lg font-black text-gray-900">@persian($listings->total())</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">فروش موفق</span>
                    <span class="block text-lg font-black text-gray-900 text-green-600">@persian($completedSales)</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">عضویت از</span>
                    <span class="block text-lg font-black text-gray-900">@persian(\Morilog\Jalali\Jalalian::fromCarbon($store->created_at)->format('Y'))/@persian(\Morilog\Jalali\Jalalian::fromCarbon($store->created_at)->format('m'))</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 text-center border border-gray-100">
                    <span class="block text-xs text-gray-500 mb-1">رضایت مشتریان</span>
                    <span class="block text-lg font-black text-gray-900 text-primary">
                        @if($seller->seller_rating > 0)
                            @persian(round(($seller->seller_rating / 5) * 100))٪
                        @else
                            جدید
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div x-data="{ activeTab: 'products', filter: 'all' }">
        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200">
            <nav aria-label="Tabs" class="flex gap-8 overflow-x-auto no-scrollbar">
                <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="border-b-2 py-4 px-1 text-sm font-bold whitespace-nowrap flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">gavel</span>
                    مزایده‌های فعال
                    <span class="bg-primary/10 text-primary text-xs py-0.5 px-2 rounded-full mr-1">@persian($listings->total())</span>
                </button>
                <button @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="border-b-2 py-4 px-1 text-sm font-bold whitespace-nowrap flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">rate_review</span>
                    نظرات خریداران
                    <span class="bg-primary/10 text-primary text-xs py-0.5 px-2 rounded-full mr-1">@persian($reviews->total())</span>
                </button>
                <button @click="activeTab = 'about'" :class="activeTab === 'about' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="border-b-2 py-4 px-1 text-sm font-bold whitespace-nowrap flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined">info</span>
                    درباره فروشگاه
                </button>
            </nav>
        </div>

        {{-- Products Tab --}}
        <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        {{-- Filters and Sort --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 py-4 mt-6">
            <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto no-scrollbar pb-2 sm:pb-0">
                <button 
                    @click="filter = 'all'" 
                    :class="filter === 'all' ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'" 
                    class="px-4 py-2 text-sm rounded-lg font-medium whitespace-nowrap transition-colors">
                    همه حراج‌ها
                </button>
                <button 
                    @click="filter = 'buy_now'" 
                    :class="filter === 'buy_now' ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'" 
                    class="px-4 py-2 text-sm rounded-lg font-medium whitespace-nowrap transition-colors">
                    با خرید فوری
                </button>
                <button 
                    @click="filter = 'ending'" 
                    :class="filter === 'ending' ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'" 
                    class="px-4 py-2 text-sm rounded-lg font-medium whitespace-nowrap transition-colors">
                    در حال پایان
                </button>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-sm text-gray-500 whitespace-nowrap">مرتب‌سازی:</span>
                <select 
                    onchange="window.location.href = '{{ route('stores.show', $store->slug) }}?sort=' + this.value + '{{ request('filter') ? '&filter=' . request('filter') : '' }}'"
                    class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-200 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-white">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>جدیدترین</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>قیمت (کم به زیاد)</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>قیمت (زیاد به کم)</option>
                    <option value="ending_soon" {{ $sort === 'ending_soon' ? 'selected' : '' }}>زمان باقیمانده</option>
                </select>
            </div>
        </div>

    {{-- Products Grid --}}
    @if($listings->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($listings as $listing)
                @php
                    $hasBuyNow = $listing->hasBuyNowPrice();
                    $isEnding = $listing->ends_at && $listing->ends_at->diffInHours() < 24;
                @endphp
                <div 
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group overflow-hidden flex flex-col"
                    x-show="filter === 'all' || (filter === 'buy_now' && {{ $hasBuyNow ? 'true' : 'false' }}) || (filter === 'ending' && {{ $isEnding ? 'true' : 'false' }})"
                    style="display: none;"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                >
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
    </div>

    {{-- Reviews Tab --}}
    <div x-show="activeTab === 'reviews'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="mt-6">
        @if($reviews->count() > 0)
            <div class="space-y-4">
                {{-- Overall Rating Summary --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="text-center">
                            <div class="text-5xl font-black text-gray-900 mb-2">@persian(number_format($seller->seller_rating, 1))</div>
                            <div class="flex items-center justify-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-{{ $i <= round($seller->seller_rating) ? 'yellow-400' : 'gray-300' }} text-2xl">★</span>
                                @endfor
                            </div>
                            <div class="text-sm text-gray-500">از @persian($seller->seller_rating_count) نظر</div>
                        </div>
                        
                        <div class="flex-1 w-full">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $ratingCounts->get($i, 0);
                                    $percentage = $seller->seller_rating_count > 0 ? ($count / $seller->seller_rating_count) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-sm text-gray-600 w-12">@persian($i) ستاره</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-yellow-400 h-full rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-500 w-12 text-left">@persian($count)</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Reviews List --}}
                @foreach($reviews as $review)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-2xl">person</span>
                            </div>
                            
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $review->buyer->name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="text-{{ $i <= $review->rating ? 'yellow-400' : 'gray-300' }} text-lg">★</span>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500">{{ \Morilog\Jalali\Jalalian::fromCarbon($review->created_at)->format('d F Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($review->order)
                                        <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-medium">خرید تایید شده</span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-700 leading-relaxed mt-3">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Reviews Pagination --}}
                @if($reviews->hasPages())
                    <div class="flex justify-center mt-6">
                        {{ $reviews->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <span class="material-symbols-outlined text-gray-300 text-8xl mb-4">rate_review</span>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">هنوز نظری ثبت نشده</h3>
                <p class="text-gray-500">این فروشنده هنوز نظری دریافت نکرده است.</p>
            </div>
        @endif
    </div>

    {{-- About Tab --}}
    <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">info</span>
                درباره فروشگاه
            </h2>
            
            @if($store->description)
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed mb-6">
                    {!! nl2br(e($store->description)) !!}
                </div>
            @else
                <p class="text-gray-500 mb-6">فروشنده هنوز توضیحاتی درباره فروشگاه خود ننوشته است.</p>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-2xl">schedule</span>
                    <div>
                        <div class="font-bold text-gray-900 mb-1">تاریخ عضویت</div>
                        <div class="text-gray-600">{{ \Morilog\Jalali\Jalalian::fromCarbon($store->created_at)->format('d F Y') }}</div>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-2xl">verified</span>
                    <div>
                        <div class="font-bold text-gray-900 mb-1">وضعیت فروشگاه</div>
                        <div class="text-gray-600">
                            @if($store->is_active)
                                <span class="text-green-600">فعال و تایید شده</span>
                            @else
                                <span class="text-gray-500">غیرفعال</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-2xl">shopping_bag</span>
                    <div>
                        <div class="font-bold text-gray-900 mb-1">تعداد فروش موفق</div>
                        <div class="text-gray-600">@persian($completedSales) سفارش</div>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-2xl">star</span>
                    <div>
                        <div class="font-bold text-gray-900 mb-1">امتیاز فروشنده</div>
                        <div class="text-gray-600">
                            @if($seller->seller_rating > 0)
                                @persian(number_format($seller->seller_rating, 1)) از ۵ (@persian($seller->seller_rating_count) نظر)
                            @else
                                هنوز امتیازی ثبت نشده
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div> {{-- End of tabs x-data --}}
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
