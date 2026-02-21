@extends('layouts.app')

@section('title', $listing->title . ' - Persian Auction Marketplace')

@section('content')
<main class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
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


        </div>

        <!-- Right Column - Auction Info -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg p-6 lg:p-8 sticky top-24">
                <!-- Product Title & Meta -->
                <div class="mb-6">
                    <h1 class="text-2xl lg:text-3xl font-black text-gray-900 mb-3 leading-tight">{{ $listing->title }}</h1>
                    
                    <!-- Tags Section (if exists) -->
                    @if($listing->tags && count($listing->tags) > 0)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($listing->tags as $tag)
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
                    @if($listing->status === 'active' && $listing->ends_at)
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 border-dashed">
                            <span class="text-gray-600 font-medium">زمان باقیمانده:</span>
                            <div class="flex items-center gap-2 text-secondary font-bold text-xl tabular-nums dir-ltr">
                                @livewire('auction-countdown', ['listing' => $listing])
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-end">
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
                                    @if($listing->bids->count() > 0)
                                        @price($listing->current_price)
                                    @else
                                        @price($listing->starting_price)
                                    @endif
                                </span>
                                <span class="text-gray-500 font-medium">تومان</span>
                            </div>
                        </div>
                        <div class="text-left">
                            <span class="block text-xs text-gray-400 mb-1">تعداد پیشنهادها</span>
                            <span class="font-bold text-gray-800 text-lg">@persian($listing->bids->count()) نفر</span>
                        </div>
                    </div>
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
                    @livewire('auction-bidding', ['listing' => $listing])
                @else
                    <div class="space-y-4 mb-6">
                        <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-200 text-center">
                            <p class="text-sm text-yellow-800 mb-3">برای شرکت در مزایده باید وارد شوید</p>
                            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-primary text-white font-bold rounded-lg hover:bg-blue-700 transition-colors">
                                ورود / ثبت نام
                            </a>
                        </div>
                    </div>
                @endauth

                <!-- Seller Info -->
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center overflow-hidden">
                        @if($listing->seller->store && $listing->seller->store->logo_path)
                            <img src="{{ Storage::url($listing->seller->store->logo_path) }}" alt="{{ $listing->seller->store->store_name }}" class="w-full h-full object-cover"/>
                        @else
                            <span class="material-symbols-outlined text-gray-400 text-3xl">storefront</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">{{ $listing->seller->store->store_name ?? $listing->seller->name }}</h4>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-yellow-500 text-sm">star</span>
                            <span class="text-xs font-bold text-gray-700">۴.۸</span>
                            <span class="text-xs text-gray-400">(@persian(rand(50, 200)) فروش موفق)</span>
                        </div>
                    </div>
                    @if($listing->seller->store)
                        <a href="{{ route('stores.show', $listing->seller->store->slug) }}" class="text-primary text-sm font-bold hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors">
                            مشاهده فروشگاه
                        </a>
                    @endif
                </div>

                <!-- Shipping Methods -->
                @if($listing->shippingMethods && $listing->shippingMethods->count() > 0)
                <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <h4 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                        روش‌های ارسال
                    </h4>
                    <div class="space-y-2">
                        @foreach($listing->shippingMethods as $method)
                        <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-blue-100">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]">local_shipping</span>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $method->name }}</span>
                                    @if($method->estimated_days)
                                        <span class="text-xs text-gray-500 mr-2">
                                            ({{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز کاری)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-900">
                                {{ \App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost + $method->pivot->custom_cost_adjustment)) }} تومان
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Details & Bid History -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-8">
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
                                @if($listing->required_deposit > 0)
                                    <li>سپرده شرکت در مزایده: @price($listing->required_deposit) تومان</li>
                                @endif
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


