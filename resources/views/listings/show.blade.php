@extends('layouts.app')

@section('title', $listing->title . ' - Persian Auction Marketplace')

@section('content')
<main class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-red-600 text-2xl">error</span>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- بنر تعلیق شده -->
    @if($listing->status === 'suspended')
        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-red-600 text-2xl">block</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-1">این آگهی تعلیق شده است</h3>
                    <p class="text-red-700 text-sm">
                        @if($listing->suspension_reason)
                            دلیل: {{ $listing->suspension_reason }}
                        @else
                            این آگهی توسط مدیریت تعلیق شده و برای عموم قابل مشاهده نیست.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- بنر حراجی هنوز شروع نشده -->
    @if($listing->isPending())
        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">schedule</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-blue-900 mb-1">این حراجی هنوز شروع نشده است</h3>
                    <p class="text-blue-700 text-sm">
                        زمان شروع: <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(\App\Services\JalaliDateService::toJalali($listing->starts_at, 'Y/m/d H:i')) }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- بنر حراجی به پایان رسیده -->
    @if($listing->hasEnded() && $listing->status !== 'suspended')
        <div class="bg-gray-50 border-2 border-gray-200 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-gray-600 text-2xl">event_busy</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">این حراجی به پایان رسیده است</h3>
                    <p class="text-gray-700 text-sm">
                        زمان پایان: <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(\App\Services\JalaliDateService::toJalali($listing->ends_at, 'Y/m/d H:i')) }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex text-sm text-gray-500 mb-4">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
            <li class="inline-flex items-center">
                <a class="inline-flex items-center hover:text-primary transition-colors" href="{{ route('listings.index') }}">
                    <span class="material-symbols-outlined text-lg ml-1">home</span>
                    خانه
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-gray-300 mx-1 rtl:rotate-180">chevron_right</span>
                    <a class="hover:text-primary transition-colors" href="{{ route('listings.index') }}">کالای دیجیتال</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-gray-300 mx-1 rtl:rotate-180">chevron_right</span>
                    @if($listing->category)
                        <a class="hover:text-primary transition-colors" href="{{ route('listings.index', ['category' => $listing->category->slug]) }}">{{ $listing->category->name }}</a>
                    @else
                        <span class="text-gray-500">بدون دسته‌بندی</span>
                    @endif
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-gray-300 mx-1 rtl:rotate-180">chevron_right</span>
                    <span class="text-gray-900 font-medium">{{ $listing->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column - Images & Details -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Main Image -->
            <div class="relative bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm group">
                <div class="absolute top-4 right-4 z-10 flex gap-2">
                    @if($listing->status === 'active')
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md animate-pulse">مزایده داغ</span>
                    @endif
                    <button class="bg-white/90 hover:bg-white text-gray-600 hover:text-red-500 p-2 rounded-full shadow-sm transition-colors backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xl">favorite</span>
                    </button>
                </div>
                <div class="aspect-[4/3] w-full bg-gray-50 flex items-center justify-center cursor-pointer" onclick="openLightboxFromMain()">
                    @if($listing->images->count() > 0)
                        <img alt="{{ $listing->title }}" class="w-full h-full object-cover" src="{{ url('storage/' . $listing->images->first()->file_path) }}" id="mainImage"/>
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-5xl opacity-0 group-hover:opacity-100 transition-opacity">zoom_in</span>
                        </div>
                    @else
                        <span class="material-symbols-outlined text-gray-300" style="font-size: 120px;">image</span>
                    @endif
                </div>
            </div>

            <!-- Thumbnail Carousel -->
            @if($listing->images->count() > 0)
            <div class="relative">
                @if($listing->images->count() > 4)
                <button onclick="scrollGallery(-1)" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-700 p-2 rounded-full shadow-md transition-all hover:scale-110">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <button onclick="scrollGallery(1)" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-700 p-2 rounded-full shadow-md transition-all hover:scale-110">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                @endif
                
                <div id="imageGallery" class="flex gap-3 overflow-x-auto scroll-smooth no-scrollbar pb-2">
                    @foreach($listing->images as $index => $image)
                        <button class="relative rounded-xl overflow-hidden border-2 {{ $loop->first ? 'border-primary' : 'border-gray-200 hover:border-primary/50' }} transition-all thumbnail-btn flex-shrink-0 w-20 h-20 md:w-24 md:h-24 group" 
                                onclick="changeMainImage('{{ url('storage/' . $image->file_path) }}', {{ $index }}, this)"
                                ondblclick="openLightbox({{ $index }})">
                            <img alt="Thumbnail {{ $loop->iteration }}" class="w-full h-full object-cover {{ $loop->first ? '' : 'opacity-80 hover:opacity-100' }}" src="{{ url('storage/' . $image->file_path) }}"/>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity">zoom_in</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Lightbox Modal -->
            <div id="lightbox" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4" onclick="closeLightbox()">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">
                    <!-- Header -->
                    <div class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/50 to-transparent p-4 z-10 flex items-center justify-between">
                        <div class="text-white text-sm font-medium bg-black/30 px-3 py-1.5 rounded-full backdrop-blur-sm">
                            <span id="imageCounter">1 / {{ $listing->images->count() }}</span>
                        </div>
                        <button onclick="closeLightbox()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors backdrop-blur-sm">
                            <span class="material-symbols-outlined text-2xl">close</span>
                        </button>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    @if($listing->images->count() > 1)
                    <button onclick="event.stopPropagation(); previousImage()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white hover:bg-gray-100 text-gray-700 p-3 rounded-full shadow-lg transition-all hover:scale-110 z-10">
                        <span class="material-symbols-outlined text-2xl">chevron_right</span>
                    </button>
                    
                    <button onclick="event.stopPropagation(); nextImage()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white hover:bg-gray-100 text-gray-700 p-3 rounded-full shadow-lg transition-all hover:scale-110 z-10">
                        <span class="material-symbols-outlined text-2xl">chevron_left</span>
                    </button>
                    @endif
                    
                    <!-- Main Image -->
                    <div class="flex items-center justify-center bg-gray-50 p-8" style="height: 70vh;">
                        <img id="lightboxImage" class="max-w-full max-h-full object-contain rounded-lg" src="" alt=""/>
                    </div>
                    
                    <!-- Thumbnail Strip -->
                    @if($listing->images->count() > 1)
                    <div class="bg-white border-t border-gray-200 p-4">
                        <div class="flex gap-2 overflow-x-auto no-scrollbar">
                            @foreach($listing->images as $index => $image)
                            <button onclick="event.stopPropagation(); openLightbox({{ $index }})" 
                                    class="lightbox-thumb flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all {{ $loop->first ? 'border-primary' : 'border-gray-200 hover:border-primary/50' }}"
                                    data-index="{{ $index }}">
                                <img src="{{ url('storage/' . $image->file_path) }}" class="w-full h-full object-cover" alt="Thumbnail {{ $loop->iteration }}"/>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Buy Now, Seller Info, Shipping - Below Images --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Buy Now Section --}}
                @if($listing->buy_now_price && $listing->buy_now_price > 0 && $listing->isActive())
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl border border-orange-200 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-orange-600">shopping_bag</span>
                            <h4 class="font-bold text-gray-900 text-sm">خرید فوری</h4>
                        </div>
                        <div class="mb-3">
                            <div class="text-2xl font-black text-orange-600 mb-1">@price($listing->buy_now_price) <span class="text-sm font-medium">تومان</span></div>
                            <p class="text-xs text-gray-600">بدون انتظار برنده شوید!</p>
                        </div>
                        @auth
                            @if(auth()->user()->role !== 'admin' && $listing->seller_id !== auth()->id())
                                <form action="{{ route('listings.participate', $listing) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="buy_now" value="1">
                                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-sm font-bold rounded-lg shadow-md flex items-center justify-center gap-2 transition-all">
                                        <span class="material-symbols-outlined text-lg">shopping_bag</span>
                                        <span>خرید فوری</span>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full py-2.5 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white text-sm font-bold rounded-lg shadow-md flex items-center justify-center gap-2 transition-all">
                                <span class="material-symbols-outlined text-lg">shopping_bag</span>
                                <span>خرید فوری</span>
                            </a>
                        @endauth
                    </div>
                @endif

                {{-- Seller Info --}}
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary">storefront</span>
                        <h4 class="font-bold text-gray-900 text-sm">فروشگاه</h4>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($listing->seller->store && $listing->seller->store->logo_path)
                                <img src="{{ Storage::url($listing->seller->store->logo_path) }}" alt="{{ $listing->seller->store->store_name }}" class="w-full h-full object-cover"/>
                            @else
                                <span class="material-symbols-outlined text-gray-400 text-2xl">storefront</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-gray-900 text-sm truncate">{{ $listing->seller->store->store_name ?? $listing->seller->name }}</h5>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-yellow-500 text-sm">star</span>
                                <span class="text-xs font-bold text-gray-700">@persian(number_format($listing->seller->seller_rating ?? 0, 1))</span>
                                <span class="text-xs text-gray-400">(@persian($listing->seller->successful_sales ?? 0) فروش موفق)</span>
                            </div>
                        </div>
                    </div>
                    @if($listing->seller->store)
                        <a href="{{ route('stores.show', $listing->seller->store->slug) }}" class="block w-full text-center py-2 text-primary text-sm font-bold hover:bg-primary/5 rounded-lg transition-colors border border-primary/20">
                            مشاهده فروشگاه
                        </a>
                    @endif
                </div>

                {{-- Shipping Methods --}}
                @if($listing->shippingMethods && $listing->shippingMethods->count() > 0)
                <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                        <h4 class="font-bold text-gray-900 text-sm">روش‌های ارسال</h4>
                    </div>
                    <div class="space-y-2">
                        @foreach($listing->shippingMethods as $method)
                        <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <span class="material-symbols-outlined text-blue-600 text-base flex-shrink-0">local_shipping</span>
                                    <span class="text-xs font-medium text-gray-900 truncate">{{ $method->name }}</span>
                                </div>
                                <span class="text-xs font-bold text-gray-900 whitespace-nowrap mr-2">
                                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost + $method->pivot->custom_cost_adjustment)) }} تومان
                                </span>
                            </div>
                            @if($method->estimated_days)
                                <p class="text-xs text-gray-500 mt-1 mr-6">
                                    {{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز کاری
                                </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>


        </div>

        <!-- Right Column - Auction Info -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg p-4 lg:p-6 sticky top-24">
                <!-- Product Title & Meta -->
                <div class="mb-6">
                    <h1 class="text-2xl lg:text-3xl font-black text-gray-900 mb-3 leading-tight">{{ $listing->title }}</h1>
                    
                    <!-- Tags Section (if exists) -->
                    @php
                        $tagsArray = is_array($listing->tags) ? $listing->tags : (is_string($listing->tags) ? json_decode($listing->tags, true) : []);
                    @endphp
                    @if($tagsArray && count($tagsArray) > 0)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($tagsArray as $tag)
                            <a href="{{ route('listings.index', ['tag' => trim($tag)]) }}" class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-100 transition-colors border border-blue-100">
                                #{{ trim($tag) }}
                            </a>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Meta Info -->
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-lg">category</span>
                            {{ $listing->category ? $listing->category->name : 'بدون دسته‌بندی' }}
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                            {{ \App\Services\PersianNumberService::convertToPersian($listing->views ?? 0) }} بازدید
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded text-xs">نو - آکبند</span>
                    </div>
                </div>

                <!-- Auction Timer & Price -->
                <div class="bg-background-light rounded-xl p-5 mb-6 border border-gray-200">
                    @if($listing->isActive() && $listing->ends_at)
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 border-dashed">
                            <span class="text-gray-600 font-medium">زمان باقیمانده:</span>
                            <div class="flex items-center gap-2 text-secondary font-bold text-xl tabular-nums dir-ltr">
                                @livewire('auction-countdown', ['listing' => $listing])
                            </div>
                        </div>
                    @elseif($listing->isPending())
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 border-dashed">
                            <span class="text-gray-600 font-medium">زمان شروع:</span>
                            <div class="flex items-center gap-2 text-blue-600 font-bold text-lg">
                                <span class="material-symbols-outlined">schedule</span>
                                <span>{{ \App\Services\PersianNumberService::convertToPersian(\App\Services\JalaliDateService::toJalali($listing->starts_at, 'Y/m/d H:i')) }}</span>
                            </div>
                        </div>
                    @elseif($listing->hasEnded())
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 border-dashed">
                            <span class="text-gray-600 font-medium">وضعیت:</span>
                            <div class="flex items-center gap-2 text-gray-600 font-bold text-lg">
                                <span class="material-symbols-outlined">event_busy</span>
                                <span>به پایان رسیده</span>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <span class="block text-gray-500 text-sm mb-1">
                                @if($listing->bids->count() > 0)
                                    بالاترین پیشنهاد فعلی
                                @else
                                    قیمت پایه
                                @endif
                            </span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-primary">
                                    @php
                                        $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
                                        $currentPrice = $highestBid ? $highestBid->amount : $listing->starting_price;
                                        $isUserWinning = auth()->check() && $highestBid && $highestBid->user_id === auth()->id();
                                    @endphp
                                    @price($currentPrice)
                                </span>
                                <span class="text-gray-500 font-medium">تومان</span>
                            </div>
                            @if($isUserWinning && $listing->status === 'active')
                                <div class="flex items-center gap-1 mt-2 text-sm text-green-600">
                                    <span class="material-symbols-outlined text-base">check_circle</span>
                                    <span class="font-medium">شما در حال حاضر بالاترین پیشنهاد را دارید</span>
                                </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <span class="block text-xs text-gray-400 mb-1">تعداد پیشنهادها</span>
                            <span class="font-bold text-gray-800 text-lg">@persian($listing->bids->count()) پیشنهاد</span>
                        </div>
                    </div>
                    
                    {{-- Deposit Info --}}
                    @php
                        $depositSetting = \App\Models\SiteSetting::where('key', 'deposit_type')->first();
                        $depositType = $depositSetting ? $depositSetting->value : 'none';
                        
                        $displayDepositAmount = 0;
                        if ($depositType === 'fixed') {
                            $fixedSetting = \App\Models\SiteSetting::where('key', 'deposit_fixed_amount')->first();
                            $displayDepositAmount = $fixedSetting ? (int)$fixedSetting->value : 0;
                        } elseif ($depositType === 'percentage') {
                            $percentageSetting = \App\Models\SiteSetting::where('key', 'deposit_percentage')->first();
                            $percentage = $percentageSetting ? (float)$percentageSetting->value : 0;
                            $displayDepositAmount = (int)($listing->starting_price * ($percentage / 100));
                        }
                    @endphp
                    @if($displayDepositAmount > 0)
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-lg">lock</span>
                                    سپرده شرکت در مزایده:
                                </span>
                                <span class="font-bold text-gray-900">@persian(number_format($displayDepositAmount)) تومان</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Bid Form -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <div class="mb-4">
                            <a href="{{ route('admin.listings.manage', $listing) }}" class="block w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-bold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-md hover:shadow-lg text-center">
                                <span class="material-symbols-outlined text-lg align-middle ml-1">admin_panel_settings</span>
                                مدیریت حراجی (ادمین)
                            </a>
                        </div>
                    @endif
                    
                    @if($listing->status === 'suspended')
                        <div class="p-6 bg-red-50 rounded-xl border-2 border-red-200 text-center">
                            <span class="material-symbols-outlined text-red-600 text-5xl mb-3">block</span>
                            <p class="text-lg text-red-900 font-bold mb-2">این آگهی تعلیق شده است</p>
                            @if($listing->suspension_reason)
                                <p class="text-sm text-red-700 bg-red-100 px-4 py-2 rounded-lg inline-block">
                                    <span class="font-bold">دلیل:</span> {{ $listing->suspension_reason }}
                                </p>
                            @endif
                        </div>
                    @elseif(auth()->check() && $listing->seller_id === auth()->id())
                        {{-- Owner of the listing --}}
                        <div class="p-6 bg-green-50 rounded-xl border-2 border-green-200 text-center">
                            <span class="material-symbols-outlined text-green-600 text-5xl mb-3">storefront</span>
                            <p class="text-lg text-green-900 font-bold mb-3">این حراجی متعلق به شماست</p>
                            <a href="{{ route('listings.edit', $listing) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                                ویرایش حراجی
                            </a>
                        </div>
                    @elseif($listing->isActive())
                        {{-- Show bidding form directly (no separate participation needed) --}}
                        <div class="space-y-4">
                                @if(session('bid_success'))
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        <span>{{ session('bid_success') }}</span>
                                    </div>
                                @endif

                                @if(session('bid_error'))
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined">error</span>
                                        <span>{{ session('bid_error') }}</span>
                                    </div>
                                @endif

                                @php
                                    $highestBid = $listing->bids->sortByDesc('amount')->first();
                                    $minimumBid = $highestBid 
                                        ? $highestBid->amount + ($listing->bid_increment ?? 1000)
                                        : $listing->starting_price;
                                    $userWallet = auth()->user()->wallet;
                                    $walletBalance = $userWallet ? $userWallet->balance : 0;
                                    
                                    // Get deposit from site settings
                                    $depositSetting = \App\Models\SiteSetting::where('key', 'deposit_type')->first();
                                    $depositType = $depositSetting ? $depositSetting->value : 'none';
                                    
                                    $depositAmount = 0;
                                    if ($depositType === 'fixed') {
                                        $fixedSetting = \App\Models\SiteSetting::where('key', 'deposit_fixed_amount')->first();
                                        $depositAmount = $fixedSetting ? (int)$fixedSetting->value : 0;
                                    } elseif ($depositType === 'percentage') {
                                        $percentageSetting = \App\Models\SiteSetting::where('key', 'deposit_percentage')->first();
                                        $percentage = $percentageSetting ? (float)$percentageSetting->value : 0;
                                        $depositAmount = (int)($listing->starting_price * ($percentage / 100));
                                    }
                                    
                                    // Check if user has already placed a bid (deposit already paid)
                                    $userHasBid = $listing->bids()->where('user_id', auth()->id())->exists();
                                    
                                    // Calculate required balance (bid + deposit if first bid)
                                    $requiredBalance = $minimumBid;
                                    if (!$userHasBid && $depositAmount > 0) {
                                        $requiredBalance += $depositAmount;
                                    }
                                @endphp

                                <form action="{{ route('bids.store') }}" method="POST" id="bidForm" onsubmit="return validateBid()">
                                    @csrf
                                    <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                    
                                    <label class="block text-sm font-bold text-gray-700 mb-3">پیشنهاد خود را وارد کنید</label>
                                    
                                    {{-- Deposit Info --}}
                                    @if($depositAmount > 0 && !$userHasBid)
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3 text-sm">
                                            <div class="flex items-center gap-2 text-blue-800">
                                                <span class="material-symbols-outlined text-lg">info</span>
                                                <span>سپرده شرکت در مزایده: <strong>@persian(number_format($depositAmount))</strong> تومان</span>
                                            </div>
                                            <p class="text-xs text-blue-700 mt-1">این مبلغ برای اولین پیشنهاد شما بلوک می‌شود و پس از پایان مزایده بازگردانده می‌شود</p>
                                        </div>
                                    @endif
                                    
                                    {{-- Wallet Balance Warning --}}
                                    @if($walletBalance < $requiredBalance)
                                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-3 text-sm">
                                            <div class="flex items-start gap-2 text-orange-800">
                                                <span class="material-symbols-outlined text-lg">warning</span>
                                                <div>
                                                    <p class="font-bold mb-1">موجودی کیف پول شما کافی نیست!</p>
                                                    <p class="text-xs">موجودی فعلی: <strong>@persian(number_format($walletBalance))</strong> تومان</p>
                                                    <p class="text-xs">حداقل پیشنهاد: <strong>@persian(number_format($minimumBid))</strong> تومان</p>
                                                    @if($depositAmount > 0 && !$userHasBid)
                                                        <p class="text-xs">سپرده مزایده: <strong>@persian(number_format($depositAmount))</strong> تومان</p>
                                                        <p class="text-xs mt-1 font-bold">مجموع مورد نیاز: <strong>@persian(number_format($requiredBalance))</strong> تومان</p>
                                                    @else
                                                        <p class="text-xs mt-1 font-bold">مورد نیاز: <strong>@persian(number_format($requiredBalance))</strong> تومان</p>
                                                    @endif
                                                    <a href="{{ route('wallet.show') }}" class="inline-block mt-2 px-3 py-1 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-xs font-bold">
                                                        شارژ کیف پول
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="relative mb-4">
                                        <input 
                                            type="number" 
                                            name="amount"
                                            id="bidAmount"
                                            class="block w-full text-center h-14 px-16 bg-white border-2 border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 text-xl font-bold transition-all hover:border-gray-400"
                                            value="{{ $minimumBid }}"
                                            min="{{ $minimumBid }}"
                                            step="1000"
                                            required
                                        />
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500 font-medium pointer-events-none">تومان</span>
                                    </div>
                                    
                                    <div id="bidError" class="hidden text-red-500 text-sm mb-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">error</span>
                                        <span id="bidErrorText"></span>
                                    </div>
                                    
                                    @error('amount') 
                                        <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                                    @enderror
                                    
                                    <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar mb-4">
                                        <button type="button" onclick="incrementBid(50000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                                            + @persian(number_format(50000))
                                        </button>
                                        <button type="button" onclick="incrementBid(100000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                                            + @persian(number_format(100000))
                                        </button>
                                        <button type="button" onclick="incrementBid(200000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                                            + @persian(number_format(200000))
                                        </button>
                                    </div>
                                    
                                    <button 
                                        type="submit"
                                        id="submitBidBtn"
                                        class="w-full h-14 bg-primary hover:bg-blue-600 text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all transform active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed"
                                        @if($walletBalance < $requiredBalance) disabled @endif
                                    >
                                        <span class="material-symbols-outlined">gavel</span>
                                        <span>ثبت پیشنهاد</span>
                                    </button>
                                    
                                    <p class="text-xs text-center text-gray-500 mt-2">
                                        با ثبت پیشنهاد، <a class="text-primary hover:underline" href="#">قوانین مزایده</a> را می‌پذیرید.
                                    </p>
                                </form>

                                <script>
                                const minimumBid = {{ $minimumBid }};
                                const requiredBalance = {{ $requiredBalance }};
                                const walletBalance = {{ $walletBalance }};
                                const depositAmount = {{ $depositAmount }};
                                const hasParticipated = {{ $userHasBid ? 'true' : 'false' }};
                                
                                // Persian number conversion
                                const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
                                function toPersianNumber(num) {
                                    return num.toString().replace(/\d/g, x => persianDigits[parseInt(x)]);
                                }
                                
                                function formatNumber(num) {
                                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                }
                                
                                function incrementBid(amount) {
                                    const input = document.getElementById('bidAmount');
                                    const currentValue = parseInt(input.value) || minimumBid;
                                    input.value = currentValue + amount;
                                    input.focus();
                                    validateBidAmount();
                                }
                                
                                function validateBidAmount() {
                                    const input = document.getElementById('bidAmount');
                                    const errorDiv = document.getElementById('bidError');
                                    const errorText = document.getElementById('bidErrorText');
                                    const submitBtn = document.getElementById('submitBidBtn');
                                    const value = parseInt(input.value) || 0;
                                    
                                    if (value < minimumBid) {
                                        errorDiv.classList.remove('hidden');
                                        errorText.textContent = 'مبلغ پیشنهاد باید حداقل ' + toPersianNumber(formatNumber(minimumBid)) + ' تومان باشد';
                                        submitBtn.disabled = true;
                                        return false;
                                    }
                                    
                                    // Calculate required balance for this bid
                                    let requiredForThisBid = value;
                                    if (depositAmount > 0 && !hasParticipated) {
                                        requiredForThisBid += depositAmount;
                                    }
                                    
                                    if (walletBalance < requiredForThisBid) {
                                        errorDiv.classList.remove('hidden');
                                        if (depositAmount > 0 && !hasParticipated) {
                                            errorText.textContent = 'موجودی کیف پول شما کافی نیست. مبلغ مورد نیاز: ' + toPersianNumber(formatNumber(requiredForThisBid)) + ' تومان (شامل ' + toPersianNumber(formatNumber(depositAmount)) + ' تومان سپرده)';
                                        } else {
                                            errorText.textContent = 'موجودی کیف پول شما کافی نیست. مبلغ مورد نیاز: ' + toPersianNumber(formatNumber(requiredForThisBid)) + ' تومان';
                                        }
                                        submitBtn.disabled = true;
                                        return false;
                                    }
                                    
                                    errorDiv.classList.add('hidden');
                                    submitBtn.disabled = false;
                                    return true;
                                }
                                
                                function validateBid() {
                                    return validateBidAmount();
                                }
                                
                                // Update validation on input change
                                document.getElementById('bidAmount').addEventListener('input', validateBidAmount);
                                
                                // Initial validation
                                validateBidAmount();
                                </script>
                        </div>
                    @elseif($listing->isPending())
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 text-center">
                            <span class="material-symbols-outlined text-blue-600 text-4xl mb-2">schedule</span>
                            <p class="text-sm text-blue-800 font-medium">این حراجی هنوز شروع نشده است</p>
                            <p class="text-xs text-blue-600 mt-1">لطفاً در زمان مقرر مراجعه کنید</p>
                        </div>
                    @elseif($listing->hasEnded())
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
                            <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">event_busy</span>
                            <p class="text-sm text-gray-700 font-medium">این حراجی به پایان رسیده است</p>
                        </div>
                    @endif
                @else
                    @if($listing->status === 'suspended')
                        <div class="p-6 bg-red-50 rounded-xl border-2 border-red-200 text-center">
                            <span class="material-symbols-outlined text-red-600 text-5xl mb-3">block</span>
                            <p class="text-lg text-red-900 font-bold mb-2">این آگهی تعلیق شده است</p>
                            @if($listing->suspension_reason)
                                <p class="text-sm text-red-700 bg-red-100 px-4 py-2 rounded-lg inline-block">
                                    <span class="font-bold">دلیل:</span> {{ $listing->suspension_reason }}
                                </p>
                            @endif
                        </div>
                    @elseif($listing->isActive())
                        <div class="space-y-4 mb-6">
                            <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200 text-center">
                                <p class="text-sm text-yellow-800 mb-3">برای شرکت در مزایده باید وارد شوید</p>
                                <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-primary text-white font-bold rounded-lg hover:bg-blue-700 transition-colors">
                                    ورود / ثبت نام
                                </a>
                            </div>
                        </div>
                    @elseif($listing->isPending())
                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-200 text-center">
                            <span class="material-symbols-outlined text-blue-600 text-4xl mb-2">schedule</span>
                            <p class="text-sm text-blue-800 font-medium">این حراجی هنوز شروع نشده است</p>
                        </div>
                    @elseif($listing->hasEnded())
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
                            <span class="material-symbols-outlined text-gray-400 text-4xl mb-2">event_busy</span>
                            <p class="text-sm text-gray-700 font-medium">این حراجی به پایان رسیده است</p>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- بنر برنده شدن -->
    @if($listing->status === 'ended' && auth()->check() && $listing->current_winner_id === auth()->id())
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-6 shadow-lg mb-6">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-green-600 text-3xl">celebration</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-black text-green-900 mb-2">🎉 تبریک! شما برنده این حراجی شدید!</h3>
                    @php
                        $winningBid = $listing->bids()->where('user_id', auth()->id())->orderBy('amount', 'desc')->first();
                        $totalAmount = $winningBid ? $winningBid->amount : 0;
                        $depositAmount = $listing->required_deposit;
                        $remainingAmount = $totalAmount - $depositAmount;
                    @endphp
                    <div class="bg-white rounded-lg p-4 mb-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">مبلغ برنده شده:</span>
                            <span class="font-bold text-gray-900">{{ \App\Services\PersianNumberService::convertToPersian(number_format($totalAmount)) }} تومان</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">سپرده پرداخت شده:</span>
                            <span class="font-bold text-green-600">{{ \App\Services\PersianNumberService::convertToPersian(number_format($depositAmount)) }} تومان</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                            <span class="text-gray-900 font-medium">مبلغ باقیمانده:</span>
                            <span class="font-black text-primary text-lg">{{ \App\Services\PersianNumberService::convertToPersian(number_format($remainingAmount)) }} تومان</span>
                        </div>
                    </div>
                    <p class="text-green-800 text-sm mb-4">
                        برای تکمیل خرید، مبلغ باقیمانده را از کیف پول خود پرداخت کنید. 
                        مهلت پرداخت: <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(\App\Services\JalaliDateService::toJalali($listing->finalization_deadline, 'Y/m/d H:i')) }}</span>
                    </p>
                    <form action="{{ route('listings.finalize', $listing) }}" method="POST" id="finalizeForm">
                        @csrf
                        <button type="button" onclick="showFinalizeModal()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined">payment</span>
                            تکمیل خرید و پرداخت
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Product Details & Bid History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-4">
        <!-- Product Details -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Tabs Section -->
            <section class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-gray-200 mb-6 pb-2">
                    <div class="flex gap-8 overflow-x-auto no-scrollbar">
                        <button class="pb-4 border-b-2 border-primary text-primary font-bold text-lg whitespace-nowrap" onclick="showTab('description')">توضیحات محصول</button>
                        <button class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-gray-800 font-medium text-lg whitespace-nowrap transition-colors" onclick="showTab('specs')">مشخصات محصول</button>
                        <button class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-gray-800 font-medium text-lg whitespace-nowrap transition-colors" onclick="showTab('comments')">نظرات و پرسش‌ها</button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="descriptionTab" class="tab-content">
                    <div class="prose prose-blue max-w-none text-gray-600 leading-loose">
                        <p>{{ $listing->description }}</p>
                        
                        @if($listing->condition)
                            <ul class="list-disc list-inside space-y-2 marker:text-primary mt-4">
                                <li>وضعیت کالا: {{ condition_label($listing->condition) }}</li>
                                <li>زمان شروع: {{ \Morilog\Jalali\Jalalian::fromCarbon($listing->starts_at)->format('Y/m/d H:i') }}</li>
                                <li>زمان پایان: {{ \Morilog\Jalali\Jalalian::fromCarbon($listing->ends_at)->format('Y/m/d H:i') }}</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <div id="specsTab" class="tab-content hidden">
                    @if($listing->attributeValues && $listing->attributeValues->count() > 0)
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">settings</span>
                            مشخصات محصول
                        </h3>
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="w-full text-sm text-right">
                                <tbody class="divide-y divide-gray-200">
                                    <!-- Category -->
                                    <tr class="bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 w-1/3">دسته‌بندی</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $listing->category ? $listing->category->name : 'بدون دسته‌بندی' }}</td>
                                    </tr>
                                    
                                    <!-- Custom Attributes -->
                                    @foreach($listing->attributeValues as $attrValue)
                                    <tr class="{{ $loop->iteration % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-4 font-medium text-gray-900 w-1/3">{{ $attrValue->attribute->name }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $attrValue->value }}</td>
                                    </tr>
                                    @endforeach
                                    
                                    <!-- Condition -->
                                    <tr class="{{ ($listing->attributeValues->count() + 1) % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-6 py-4 font-medium text-gray-900 w-1/3">وضعیت</td>
                                        <td class="px-6 py-4 text-gray-600">{{ condition_label($listing->condition) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">inventory_2</span>
                            <p class="text-gray-500 text-lg">مشخصات فنی برای این محصول ثبت نشده است</p>
                        </div>
                    @endif
                </div>

                <div id="commentsTab" class="tab-content hidden">
                    <div class="space-y-6">
                        <!-- Questions Section -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined">help</span>
                                پرسش‌ها (@persian($listing->comments->where('type', 'question')->count()))
                            </h4>
                            
                            @auth
                                <form method="POST" action="{{ route('listings.comments.store', $listing) }}" class="bg-gray-50 rounded-xl p-4 mb-6">
                                    @csrf
                                    <input type="hidden" name="type" value="question">
                                    <textarea name="content" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary resize-none" placeholder="پرسش خود را بنویسید..." required minlength="10" maxlength="1000"></textarea>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-xs text-gray-500">پرسش شما پس از تایید مدیر منتشر خواهد شد</span>
                                        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            ارسال پرسش
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="bg-blue-50 rounded-xl p-4 mb-6 text-center">
                                    <p class="text-gray-700">برای ثبت پرسش، لطفا <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">وارد شوید</a></p>
                                </div>
                            @endauth
                            
                            <div class="space-y-4">
                                @forelse($listing->comments->where('type', 'question') as $question)
                                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-gray-500">person</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="font-bold text-gray-900">{{ $question->user->name }}</span>
                                                    <span class="text-xs text-gray-400">{{ $question->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-700 leading-relaxed">{{ $question->content }}</p>
                                                
                                                @if($question->replies->count() > 0)
                                                    <div class="mt-4 mr-8 space-y-3">
                                                        @foreach($question->replies as $reply)
                                                            <div class="bg-green-50 rounded-lg p-3 border-r-4 border-green-500">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <span class="material-symbols-outlined text-green-600 text-sm">reply</span>
                                                                    <span class="font-medium text-gray-900 text-sm">{{ $reply->user->name }}</span>
                                                                    @if($reply->user_id === $listing->seller_id)
                                                                        <span class="text-xs bg-green-600 text-white px-2 py-0.5 rounded-full">فروشنده</span>
                                                                    @endif
                                                                    <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <p class="text-gray-700 text-sm">{{ $reply->content }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                @auth
                                                    @if(auth()->id() === $listing->seller_id && $question->replies->count() === 0)
                                                        <button onclick="toggleReplyForm('q{{ $question->id }}')" class="mt-3 text-sm text-primary hover:underline flex items-center gap-1">
                                                            <span class="material-symbols-outlined text-sm">reply</span>
                                                            پاسخ
                                                        </button>
                                                        
                                                        <form id="replyFormq{{ $question->id }}" method="POST" action="{{ route('listings.comments.store', $listing) }}" class="hidden mt-3 bg-gray-50 rounded-lg p-3">
                                                            @csrf
                                                            <input type="hidden" name="type" value="question">
                                                            <input type="hidden" name="parent_id" value="{{ $question->id }}">
                                                            <textarea name="content" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary resize-none text-sm" placeholder="پاسخ خود را بنویسید..." required minlength="10" maxlength="1000"></textarea>
                                                            <div class="flex gap-2 mt-2">
                                                                <button type="submit" class="px-4 py-1.5 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                                    ارسال پاسخ
                                                                </button>
                                                                <button type="button" onclick="toggleReplyForm('q{{ $question->id }}')" class="px-4 py-1.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                                                                    انصراف
                                                                </button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">help</span>
                                        <p class="text-gray-500">هنوز پرسشی ثبت نشده است</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Bid History Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <section class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm lg:sticky lg:top-28">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">تاریخچه پیشنهادات</h3>
                    <span class="text-xs bg-blue-50 text-primary px-2 py-1 rounded-md font-bold">@persian($listing->bids->count()) پیشنهاد</span>
                </div>
                
                @if($listing->bids->count() > 0)
                    <div class="relative pl-4 border-r-2 border-gray-100 space-y-6 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                        @foreach($listing->bids->sortByDesc('created_at')->take(10) as $bid)
                            <div class="relative">
                                <span class="absolute top-1 -right-[13px] w-4 h-4 {{ $loop->first ? 'bg-green-500 ring-2 ring-green-100' : 'bg-gray-300' }} rounded-full border-2 border-white"></span>
                                <div class="mr-4 {{ $loop->first ? '' : 'opacity-' . (100 - ($loop->iteration * 10)) }}">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-{{ $loop->first ? 'bold' : 'medium' }} text-gray-{{ $loop->first ? '900' : '700' }} text-sm">
                                            کاربر ***@persian(substr($bid->user->phone ?? '0000', -4))
                                        </span>
                                        @if($loop->first)
                                            <span class="text-xs text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full">برنده احتمالی</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-{{ $loop->first ? 'primary' : 'gray-600' }}">@price($bid->amount) تومان</span>
                                        <span class="text-xs text-gray-400">{{ $bid->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-2">gavel</span>
                        <p class="text-sm">هنوز پیشنهادی ثبت نشده</p>
                    </div>
                @endif
            </section>

            <!-- Security Badge -->
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex items-start gap-3">
                <span class="material-symbols-outlined text-primary mt-1">shield</span>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm mb-1">امنیت خرید شما تضمین شده است</h4>
                    <p class="text-xs text-gray-600 leading-relaxed">مبلغ پرداختی شما تا زمان تایید سلامت کالا توسط شما، نزد پرشین آکشن به امانت می‌ماند.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Image gallery data
const images = [
    @foreach($listing->images as $image)
    '{{ url('storage/' . $image->file_path) }}',
    @endforeach
];
let currentImageIndex = 0;

function changeMainImage(imageSrc, index, element) {
    currentImageIndex = index;
    document.getElementById('mainImage').src = imageSrc;
    
    // Update active thumbnail border
    document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.classList.remove('border-primary');
        btn.classList.add('border-gray-200');
        const img = btn.querySelector('img');
        if (img) {
            img.classList.add('opacity-80');
            img.classList.remove('opacity-100');
        }
    });
    
    if (element) {
        element.classList.remove('border-gray-200');
        element.classList.add('border-primary');
        const img = element.querySelector('img');
        if (img) {
            img.classList.remove('opacity-80');
            img.classList.add('opacity-100');
        }
    }
}

function scrollGallery(direction) {
    const gallery = document.getElementById('imageGallery');
    const scrollAmount = 120; // Width of thumbnail + gap
    gallery.scrollBy({
        left: direction * scrollAmount,
        behavior: 'smooth'
    });
}

// Open lightbox from main image (uses current displayed image)
function openLightboxFromMain() {
    openLightbox(currentImageIndex);
}

// Lightbox functions
function openLightbox(index) {
    if (images.length === 0) return;
    
    currentImageIndex = index;
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    
    lightboxImage.src = images[currentImageIndex];
    updateImageCounter();
    updateLightboxThumbnails();
    
    lightbox.classList.remove('hidden');
    lightbox.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.add('hidden');
    lightbox.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    document.getElementById('lightboxImage').src = images[currentImageIndex];
    updateImageCounter();
    updateLightboxThumbnails();
}

function previousImage() {
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    document.getElementById('lightboxImage').src = images[currentImageIndex];
    updateImageCounter();
    updateLightboxThumbnails();
}

function updateImageCounter() {
    document.getElementById('imageCounter').textContent = `${currentImageIndex + 1} / ${images.length}`;
}

function updateLightboxThumbnails() {
    document.querySelectorAll('.lightbox-thumb').forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.remove('border-gray-200');
            thumb.classList.add('border-primary');
        } else {
            thumb.classList.add('border-gray-200');
            thumb.classList.remove('border-primary');
        }
    });
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            nextImage();
        } else if (e.key === 'ArrowRight') {
            previousImage();
        }
    }
});

// Close lightbox on background click
document.getElementById('lightbox')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});


function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.border-b-2').forEach(btn => {
        btn.classList.remove('border-primary', 'text-primary', 'font-bold');
        btn.classList.add('border-transparent', 'text-gray-500', 'font-medium');
    });
    
    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.remove('hidden');
    
    // Add active state to clicked button
    event.currentTarget.classList.remove('border-transparent', 'text-gray-500', 'font-medium');
    event.currentTarget.classList.add('border-primary', 'text-primary', 'font-bold');
}

// Toggle reply form
function toggleReplyForm(commentId) {
    const form = document.getElementById('replyForm' + commentId);
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}

// Custom scrollbar styles
const style = document.createElement('style');
style.textContent = `
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
`;
document.head.appendChild(style);
</script>
@endsection



<!-- Finalize Payment Modal -->
<div id="finalizeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4" onclick="if(event.target === this) closeFinalizeModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 p-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">payment</span>
                </div>
                <div>
                    <h3 class="text-xl font-black">تکمیل خرید</h3>
                    <p class="text-sm text-white/90">پرداخت مبلغ باقیمانده</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-2xl flex-shrink-0">info</span>
                    <div class="text-sm text-gray-700 leading-relaxed">
                        با تایید این عملیات، مبلغ <span class="font-bold text-primary">{{ \App\Services\PersianNumberService::convertToPersian(number_format($remainingAmount)) }} تومان</span> از کیف پول شما کسر شده و سفارش شما ثبت خواهد شد.
                    </div>
                </div>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">مبلغ برنده شده:</span>
                    <span class="font-bold text-gray-900">{{ \App\Services\PersianNumberService::convertToPersian(number_format($totalAmount)) }} تومان</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200">
                    <span class="text-gray-600">سپرده پرداخت شده:</span>
                    <span class="font-bold text-green-600">{{ \App\Services\PersianNumberService::convertToPersian(number_format($depositAmount)) }} تومان</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-900 font-medium">مبلغ باقیمانده:</span>
                    <span class="font-black text-primary text-xl">{{ \App\Services\PersianNumberService::convertToPersian(number_format($remainingAmount)) }} تومان</span>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-6 text-center">
                آیا از تکمیل خرید و پرداخت مبلغ باقیمانده اطمینان دارید؟
            </p>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="button" onclick="closeFinalizeModal()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors">
                    انصراف
                </button>
                <button type="button" onclick="submitFinalizeForm()" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    تایید و پرداخت
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showFinalizeModal() {
    const modal = document.getElementById('finalizeModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeFinalizeModal() {
    const modal = document.getElementById('finalizeModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function submitFinalizeForm() {
    document.getElementById('finalizeForm').submit();
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFinalizeModal();
    }
});
</script>
