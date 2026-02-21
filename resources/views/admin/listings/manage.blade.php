@extends('layouts.admin')

@section('title', 'مدیریت مزایده - ' . $listing->title)

@push('styles')
<link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}?v={{ now()->timestamp }}">
<style>
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    .image-preview-hover:hover .image-overlay {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                {{ $listing->title }}
                @if($listing->status === 'active')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        فعال
                    </span>
                @elseif($listing->status === 'pending')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        در انتظار تایید
                    </span>
                @elseif($listing->status === 'completed' || $listing->status === 'ended')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        تمام شده
                    </span>
                @elseif($listing->status === 'suspended')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        معلق شده
                    </span>
                @elseif($listing->status === 'failed')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        ناموفق
                    </span>
                @endif
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                شناسه مزایده: <span class="font-mono text-gray-700">#{{ $listing->id }}-A</span> • 
                تاریخ ایجاد: <span dir="ltr">{{ \App\Services\JalaliDateService::toJalali($listing->created_at) }}</span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button onclick="openEditModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                ویرایش جزئیات
            </button>
            
            @if($listing->status === 'active')
            <button onclick="confirmEndEarly()" class="px-4 py-2 bg-orange-50 border border-orange-200 text-orange-700 rounded-lg hover:bg-orange-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">timer_off</span>
                پایان زودتر
            </button>
            
            <button onclick="confirmSuspend()" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">block</span>
                توقیف مزایده
            </button>
            @elseif($listing->status === 'suspended')
            <button onclick="confirmActivate()" class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg hover:bg-green-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                فعال‌سازی مجدد
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Product Details Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Images Section -->
                    <div class="space-y-4">
                        <div class="aspect-square rounded-xl bg-gray-100 overflow-hidden border border-gray-200 relative group image-preview-hover">
                            @php
                                $mainImage = $listing->images->sortBy('display_order')->first();
                            @endphp
                            @if($mainImage)
                                <img src="{{ url('storage/' . $mainImage->file_path) }}" 
                                     alt="{{ $listing->title }}" 
                                     class="w-full h-full object-cover"
                                     id="mainImage">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-6xl">image</span>
                                </div>
                            @endif
                            
                            <div class="image-overlay absolute inset-0 bg-black/40 opacity-0 transition-opacity flex items-center justify-center gap-2">
                                <button onclick="viewImage()" class="p-2 bg-white rounded-full text-gray-700 hover:text-primary">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                                <button onclick="deleteMainImage()" class="p-2 bg-white rounded-full text-gray-700 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 max-h-[400px] overflow-y-auto">
                            @foreach($listing->images->sortBy('display_order') as $image)
                            <div class="aspect-square rounded-lg bg-gray-100 overflow-hidden border {{ $image->display_order === 1 ? 'border-primary ring-2 ring-primary ring-offset-2' : 'border-gray-200' }} cursor-pointer hover:border-primary transition-colors relative group"
                                 data-image-id="{{ $image->id }}"
                                 onclick="changeMainImage('{{ url('storage/' . $image->file_path) }}', {{ $image->id }}, this)">
                                <img src="{{ url('storage/' . $image->file_path) }}" 
                                     class="w-full h-full object-cover {{ $image->display_order === 1 ? '' : 'grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all' }}">
                                
                                @if($image->display_order === 1)
                                <div class="absolute top-1 right-1 bg-primary text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                                    عکس اصلی
                                </div>
                                @else
                                <button onclick="event.stopPropagation(); setAsMainImage({{ $image->id }})" 
                                        class="absolute top-1 right-1 p-1.5 bg-blue-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-600"
                                        title="تنظیم به عنوان عکس اصلی">
                                    <span class="material-symbols-outlined text-sm">star</span>
                                </button>
                                @endif
                                
                                <button onclick="event.stopPropagation(); deleteThumbnailImage({{ $image->id }})" 
                                        class="absolute top-1 left-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                                        title="حذف تصویر">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>
                            @endforeach
                            
                            @if($listing->images->count() < 8)
                            <div class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 border-dashed text-gray-400 hover:text-primary hover:border-primary cursor-pointer transition-colors"
                                 onclick="document.getElementById('newImageInput').click()">
                                <span class="material-symbols-outlined">add_photo_alternate</span>
                            </div>
                            @endif
                            <input type="file" id="newImageInput" class="hidden" accept="image/*" onchange="uploadNewImage(this)">
                        </div>
                    </div>
                    
                    <!-- Product Info Section -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">توضیحات محصول</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mt-2 text-justify">
                                {{ $listing->description }}
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">دسته‌بندی</span>
                                <span class="font-bold text-gray-900 text-sm">{{ $listing->category ? $listing->category->name : 'بدون دسته‌بندی' }}</span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">وضعیت کالا</span>
                                <span class="font-bold text-gray-900 text-sm">{{ condition_label($listing->condition) }}</span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">نوع فروش</span>
                                <span class="font-bold text-gray-900 text-sm">
                                    @if($listing->type === 'auction')
                                        مزایده
                                    @elseif($listing->type === 'direct_sale')
                                        فروش مستقیم
                                    @else
                                        مزایده + خرید فوری
                                    @endif
                                </span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">محل کالا</span>
                                <span class="font-bold text-gray-900 text-sm">{{ $listing->store->city ?? 'تهران' }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 text-sm">برچسب‌ها</h4>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $tags = is_array($listing->tags) ? $listing->tags : [];
                                @endphp
                                @if(count($tags) > 0)
                                    @foreach($tags as $tag)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                        #{{ trim($tag) }}
                                    </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">برچسبی تعریف نشده است</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-2">برای ویرایش برچسب‌ها از دکمه "ویرایش جزئیات" استفاده کنید.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Auction Settings Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">tune</span>
                        تنظیمات مزایده
                    </h3>
                    <button onclick="saveAuctionSettings()" class="text-sm text-primary font-bold hover:underline">
                        ذخیره تغییرات
                    </button>
                </div>
                
                <form id="auctionSettingsForm">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- زمان شروع - فقط برای حراجی‌های pending یا آینده -->
                        @if($listing->isPending())
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">زمان شروع</label>
                            <input type="text" 
                                   name="starts_at" 
                                   id="manage_starts_at"
                                   value="{{ \App\Services\JalaliDateService::toDatepickerFormat($listing->starts_at) }}"
                                   class="persian-datepicker-input w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-primary focus:border-primary"
                                   placeholder="انتخاب تاریخ و زمان"
                                   autocomplete="off">
                            <p class="text-[10px] text-blue-600 mt-1">
                                <span class="material-symbols-outlined text-[12px] align-middle">info</span>
                                قابل تغییر تا زمان شروع حراجی
                            </p>
                        </div>
                        @endif
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت پایه (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="starting_price" 
                                       id="starting_price"
                                       value="{{ number_format($listing->starting_price) }}"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت رزرو (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="reserve_price" 
                                       id="reserve_price"
                                       value="{{ $listing->reserve_price ? number_format($listing->reserve_price) : '' }}"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">گام افزایش (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="bid_increment" 
                                       id="bid_increment"
                                       value="{{ number_format($listing->bid_increment) }}"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">زمان پایان</label>
                            <input type="text" 
                                   name="ends_at" 
                                   id="manage_ends_at"
                                   value="{{ \App\Services\JalaliDateService::toDatepickerFormat($listing->ends_at) }}"
                                   class="persian-datepicker-input w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-primary focus:border-primary"
                                   placeholder="انتخاب تاریخ و زمان"
                                   autocomplete="off">
                        </div>
                        
                        @if($listing->buy_now_price)
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت خرید فوری (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="buy_now_price" 
                                       id="buy_now_price"
                                       value="{{ number_format($listing->buy_now_price) }}"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        @endif
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">مبلغ سپرده (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="deposit_amount" 
                                       id="deposit_amount"
                                       value="{{ number_format($listing->deposit_amount) }}"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="auto_extend" 
                                       id="auto_extend"
                                       {{ $listing->auto_extend ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="auto_extend" class="font-medium text-gray-900">تمدید خودکار</label>
                                <p class="text-gray-500 text-xs">اگر پیشنهادی در ۵ دقیقه آخر ثبت شود، زمان مزایده ۵ دقیقه تمدید می‌شود.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Activity Log Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">history</span>
                    تاریخچه فعالیت‌ها
                </h3>
                
                <div class="space-y-3">
                    @forelse($activityLogs ?? [] as $log)
                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]">{{ $log->icon ?? 'info' }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400 mt-1" dir="ltr">
                                {{ \App\Services\JalaliDateService::toJalali($log->created_at) }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2">history</span>
                        <p class="text-sm">هیچ فعالیتی ثبت نشده است</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Bids History Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-[500px]">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">gavel</span>
                        تاریخچه پیشنهادات
                    </h3>
                    
                    <div class="mt-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">
                                @if($listing->bids->count() > 0)
                                    بالاترین پیشنهاد فعلی
                                @else
                                    قیمت پایه
                                @endif
                            </p>
                            <p class="text-xl font-black text-primary mt-1">
                                @if($listing->bids->count() > 0)
                                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->current_price)) }}
                                @else
                                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->starting_price)) }}
                                @endif
                                <span class="text-xs font-normal text-gray-400">تومان</span>
                            </p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-gray-500">تعداد پیشنهادها</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">
                                {{ \App\Services\PersianNumberService::convertToPersian($listing->bids->count()) }} نفر
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-2 space-y-2" id="bids-container">
                    @forelse($listing->bids->sortByDesc('created_at') as $index => $bid)
                    <div class="p-3 {{ $index === 0 ? 'bg-blue-50 border border-blue-100' : 'bg-white border border-gray-100' }} rounded-xl transition-all hover:shadow-md group {{ $index > 0 ? 'relative' : '' }}">
                        @if($index > 0)
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-200 rounded-l-xl group-hover:bg-gray-300"></div>
                        @endif
                        
                        <div class="flex justify-between items-start mb-2 {{ $index > 0 ? 'pl-2' : '' }}">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-blue-200' : 'bg-gray-100' }} flex items-center justify-center {{ $index === 0 ? 'text-blue-700' : 'text-gray-600' }} text-xs font-bold">
                                    {{ mb_substr($bid->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold {{ $index === 0 ? 'text-gray-900' : 'text-gray-700' }}">
                                        {{ $bid->user->name }}
                                    </p>
                                    <p class="text-[10px] {{ $index === 0 ? 'text-gray-500' : 'text-gray-400' }}">
                                        User ID: #{{ $bid->user->id }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] {{ $index === 0 ? 'bg-white px-2 py-1 rounded-full border border-gray-100' : '' }} text-gray-500">
                                {{ $bid->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center {{ $index > 0 ? 'pl-2' : '' }}">
                            <span class="font-bold {{ $index === 0 ? 'text-primary' : 'text-gray-600' }} text-sm">
                                {{ \App\Services\PersianNumberService::convertToPersian(number_format($bid->amount)) }} تومان
                            </span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="cancelBid({{ $bid->id }})" 
                                        class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg" 
                                        title="ابطال پیشنهاد">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                                <button onclick="contactUser({{ $bid->user->id }})" 
                                        class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg" 
                                        title="تماس با کاربر">
                                    <span class="material-symbols-outlined text-[16px]">chat</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2">gavel</span>
                        <p class="text-sm">هنوز پیشنهادی ثبت نشده است</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Seller Info Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">storefront</span>
                    اطلاعات فروشنده
                </h3>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 text-xl font-bold border-2 border-white shadow-sm">
                        {{ mb_substr($listing->seller->name, 0, 2) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">{{ $listing->seller->name }}</h4>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-yellow-400 text-[16px] fill-current">star</span>
                            <span class="text-xs font-bold text-gray-700">
                                {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->seller->seller_rating ?? 0, 1)) }}
                            </span>
                            <span class="text-xs text-gray-400">
                                ({{ \App\Services\PersianNumberService::convertToPersian($listing->seller->seller_rating_count ?? 0) }} نظر)
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm py-2 border-b border-gray-50">
                        <span class="text-gray-500">وضعیت حساب</span>
                        <span class="{{ $listing->seller->seller_status === 'active' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50' }} font-bold px-2 py-0.5 rounded text-xs">
                            @if($listing->seller->seller_status === 'active')
                                تایید شده
                            @elseif($listing->seller->seller_status === 'pending')
                                در انتظار تایید
                            @elseif($listing->seller->seller_status === 'suspended')
                                تعلیق شده
                            @else
                                غیرفعال
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm py-2 border-b border-gray-50">
                        <span class="text-gray-500">شماره تماس</span>
                        <span class="text-gray-900 font-mono">{{ $listing->seller->phone ?? 'ثبت نشده' }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm py-2">
                        <span class="text-gray-500">عضویت</span>
                        <span class="text-gray-900" dir="ltr">
                            {{ \App\Services\JalaliDateService::toJalali($listing->seller->created_at, 'Y/m/d') }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mt-5">
                    <a href="{{ route('admin.users.show', $listing->seller) }}" 
                       class="flex items-center justify-center gap-2 py-2 px-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        پروفایل کاربر
                    </a>
                    <button onclick="contactSeller()" 
                            class="flex items-center justify-center gap-2 py-2 px-3 bg-primary/10 hover:bg-primary/20 text-primary rounded-xl text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                        ارسال پیام
                    </button>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">analytics</span>
                    آمار سریع
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-700">بازدیدها</span>
                        <span class="text-lg font-bold text-blue-600">
                            {{ \App\Services\PersianNumberService::convertToPersian($listing->views ?? 0) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-700">شرکت‌کنندگان</span>
                        <span class="text-lg font-bold text-green-600">
                            {{ \App\Services\PersianNumberService::convertToPersian($listing->participations->count()) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-700">علاقه‌مندی‌ها</span>
                        <span class="text-lg font-bold text-purple-600">
                            {{ \App\Services\PersianNumberService::convertToPersian($listing->favorites ?? 0) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                        <span class="text-sm text-gray-700">اشتراک‌گذاری</span>
                        <span class="text-lg font-bold text-orange-600">
                            {{ \App\Services\PersianNumberService::convertToPersian($listing->shares ?? 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-xl font-bold text-gray-900">ویرایش جزئیات مزایده</h3>
            <button onclick="closeEditModal()" class="p-2 hover:bg-gray-100 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="editForm" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">عنوان محصول</label>
                <input type="text" 
                       name="title" 
                       value="{{ $listing->title }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label>
                <textarea name="description" 
                          rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">{{ $listing->description }}</textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-category-selector 
                        :selected="$listing->category_id" 
                        name="category_id"
                        label="دسته‌بندی" 
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت کالا</label>
                    <select name="condition" class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <option value="new" {{ $listing->condition === 'new' ? 'selected' : '' }}>نو</option>
                        <option value="like_new" {{ $listing->condition === 'like_new' ? 'selected' : '' }}>در حد نو</option>
                        <option value="used" {{ $listing->condition === 'used' ? 'selected' : '' }}>دست دوم</option>
                    </select>
                </div>
            </div>

            <!-- Attributes Section -->
            <div id="editAttributesSection" style="display: none;" class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-bold text-gray-800 mb-3">ویژگی‌های محصول</h4>
                <div id="editAttributesContainer" class="space-y-3">
                    <!-- ویژگی‌ها به صورت داینامیک اضافه می‌شوند -->
                </div>
            </div>
            
            <!-- Shipping Methods -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">روش‌های ارسال * <span class="text-xs text-gray-500">(حداقل یک روش را انتخاب کنید)</span></label>
                @php
                    $allShippingMethods = \App\Models\ShippingMethod::where('is_active', true)->get();
                    $selectedMethods = $listing->shippingMethods->keyBy('id');
                @endphp
                
                <div class="space-y-3" id="editShippingMethodsContainer">
                    @foreach($allShippingMethods as $method)
                    @php
                        $isSelected = $selectedMethods->has($method->id);
                        $currentCost = $isSelected ? ($method->base_cost + $selectedMethods[$method->id]->pivot->custom_cost_adjustment) : $method->base_cost;
                    @endphp
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="shipping_methods[]" 
                                   value="{{ $method->id }}"
                                   {{ $isSelected ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary rounded focus:ring-primary mt-1 edit-shipping-method-checkbox"
                                   onchange="toggleEditPriceInput(this, {{ $method->id }})">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $method->name }}</span>
                                        @if($method->estimated_days)
                                            <span class="text-xs text-gray-500 mr-2">({{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز)</span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        قیمت پایه: {{ \App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost)) }} تومان
                                    </span>
                                </div>
                                
                                <div class="price-adjustment-container {{ $isSelected ? '' : 'hidden' }}" id="edit-price-container-{{ $method->id }}">
                                    <label class="block text-xs text-gray-600 mb-1">قیمت سفارشی برای این محصول (تومان)</label>
                                    <input type="number" 
                                           name="shipping_costs[{{ $method->id }}]" 
                                           id="edit-price-input-{{ $method->id }}"
                                           value="{{ $currentCost }}"
                                           min="0"
                                           step="1000"
                                           {{ $isSelected ? '' : 'disabled' }}
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="قیمت ارسال برای این محصول">
                                    <p class="text-xs text-gray-500 mt-1">می‌توانید قیمت ارسال را برای این محصول تغییر دهید</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">برچسب‌ها (حداکثر 5 تگ، با کاما جدا کنید)</label>
                <input type="text" 
                       name="tags" 
                       id="tagsInput"
                       value="{{ is_array($listing->tags) ? implode(', ', $listing->tags) : '' }}"
                       placeholder="مثال: لپتاپ, گیمینگ, ارزان"
                       class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">برچسب‌ها را با کاما (,) از هم جدا کنید. حداکثر 5 برچسب مجاز است.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="closeEditModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    انصراف
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ url('js/persian-datepicker-package.js') }}?v={{ now()->timestamp }}"></script>
<script>
// Initialize datepickers
document.addEventListener('DOMContentLoaded', function() {
    // Starts at - only for pending auctions, can't be in the past
    const startsAtInput = document.getElementById('manage_starts_at');
    if (startsAtInput && !startsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(startsAtInput, {
            minDate: 'today'
        });
    }
    
    // Ends at
    const endsAtInput = document.getElementById('manage_ends_at');
    if (endsAtInput && !endsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(endsAtInput);
    }
});

// Base URL for API calls
const baseUrl = '{{ url("/") }}';

// Image Management
let currentImageId = {{ $listing->images->sortBy('display_order')->first()->id ?? 0 }};

function changeMainImage(src, imageId, element) {
    document.getElementById('mainImage').src = src;
    currentImageId = imageId;
    
    // Remove active state from all thumbnails
    document.querySelectorAll('.grid.grid-cols-4 > div').forEach(div => {
        div.classList.remove('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2');
        div.classList.add('border-gray-200');
        const img = div.querySelector('img');
        if (img) {
            img.classList.add('grayscale', 'opacity-70');
            img.classList.remove('grayscale-0', 'opacity-100');
        }
    });
    
    // Add active state to clicked thumbnail
    element.classList.add('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2');
    element.classList.remove('border-gray-200');
    const img = element.querySelector('img');
    if (img) {
        img.classList.remove('grayscale', 'opacity-70');
        img.classList.add('grayscale-0', 'opacity-100');
    }
}


function viewImage() {
    const mainImage = document.getElementById('mainImage');
    window.open(mainImage.src, '_blank');
}

function deleteMainImage() {
    if (!currentImageId) {
        showNotification('لطفاً یک تصویر انتخاب کنید', 'error');
        return;
    }
    
    showConfirmModal(
        'حذف تصویر',
        'آیا از حذف این تصویر اطمینان دارید؟',
        'حذف',
        'انصراف',
        () => {
            // Send delete request
            const deleteUrl = `{{ url('/admin/listings') }}/{{ $listing->slug }}/images/${currentImageId}`;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تصویر با موفقیت حذف شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در حذف تصویر', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در حذف تصویر'));
        }
    );
}

function deleteThumbnailImage(imageId) {
    showConfirmModal(
        'حذف تصویر',
        'آیا از حذف این تصویر اطمینان دارید؟',
        'حذف',
        'انصراف',
        () => {
            const deleteUrl = `{{ url('/admin/listings') }}/{{ $listing->slug }}/images/${imageId}`;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تصویر با موفقیت حذف شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در حذف تصویر', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در حذف تصویر'));
        }
    );
}

function setAsMainImage(imageId) {
    const setMainUrl = `{{ url('/admin/listings') }}/{{ $listing->slug }}/images/${imageId}/set-main`;
    fetch(setMainUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('عکس اصلی با موفقیت تغییر کرد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در تنظیم عکس اصلی', 'error');
        }
    })
    .catch(error => handleFetchError(error, 'خطا در تنظیم عکس اصلی'));
}

function uploadNewImage(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch(`{{ route('admin.listings.images.upload', $listing) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'خطا در آپلود تصویر');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('تصویر با موفقیت آپلود شد', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'خطا در آپلود تصویر', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'خطا در آپلود تصویر', 'error');
        });
        
        // Reset input
        input.value = '';
    }
}

// Auction Settings
function saveAuctionSettings() {
    const form = document.getElementById('auctionSettingsForm');
    const formData = new FormData(form);
    
    // Add method spoofing for PUT request
    formData.append('_method', 'PUT');
    
    // Handle checkbox - if not checked, add false value
    if (!formData.has('auto_extend')) {
        formData.append('auto_extend', '0');
    } else {
        formData.set('auto_extend', '1');
    }
    
    // Remove commas from numbers
    ['starting_price', 'reserve_price', 'bid_increment', 'buy_now_price', 'deposit_amount'].forEach(field => {
        const value = formData.get(field);
        if (value) {
            formData.set(field, value.replace(/,/g, ''));
        }
    });
    
    fetch(`{{ route('admin.listings.settings', $listing) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تنظیمات با موفقیت ذخیره شد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در ذخیره تنظیمات', 'error');
        }
    })
    .catch(error => {
        handleFetchError(error, 'خطا در ارتباط با سرور');
    });
}

// Format numbers with commas on input
document.querySelectorAll('input[type="text"][name*="price"], input[type="text"][name*="amount"]').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            e.target.value = parseInt(value).toLocaleString('en-US');
        }
    });
});

// Bid Management
function cancelBid(bidId) {
    showConfirmModal(
        'ابطال پیشنهاد',
        'آیا از ابطال این پیشنهاد اطمینان دارید؟ این عمل قابل بازگشت نیست.',
        'ابطال',
        'انصراف',
        () => {
            fetch(`{{ url('/admin/bids') }}/${bidId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('پیشنهاد با موفقیت ابطال شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در ابطال پیشنهاد', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در ابطال پیشنهاد'));
        }
    );
}


function contactUser(userId) {
    window.location.href = `/admin/users/${userId}/message`;
}

// Auction Actions
function confirmEndEarly() {
    showConfirmModal(
        'پایان زودتر مزایده',
        'آیا از پایان زودتر این مزایده اطمینان دارید؟ برنده فعلی به عنوان برنده نهایی انتخاب خواهد شد.',
        'پایان مزایده',
        'انصراف',
        () => {
            fetch(`{{ route('admin.listings.end-early', $listing) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('مزایده با موفقیت پایان یافت', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'خطا در پایان مزایده', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در پایان مزایده'));
        }
    );
}

function confirmSuspend() {
    const reason = prompt('لطفاً دلیل توقیف مزایده را وارد کنید:');
    if (reason && reason.trim()) {
        fetch(`{{ route('admin.listings.suspend', $listing) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('مزایده با موفقیت توقیف شد', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('خطا در توقیف مزایده', 'error');
            }
        })
        .catch(error => handleFetchError(error, 'خطا در توقیف مزایده'));
    }
}

function confirmActivate() {
    showConfirmModal(
        'فعال‌سازی مزایده',
        'آیا از فعال‌سازی مجدد این مزایده اطمینان دارید؟',
        'فعال‌سازی',
        'انصراف',
        () => {
            fetch(`{{ route('admin.listings.activate', $listing) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('مزایده با موفقیت فعال شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در فعال‌سازی مزایده', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در فعال‌سازی مزایده'));
        }
    );
}

// Modal Management
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}


// Edit Form Submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Check if category is selected
    const categoryId = document.getElementById('categorySelect').value;
    if (!categoryId) {
        showNotification('لطفاً دسته‌بندی را انتخاب کنید', 'error');
        return;
    }
    
    // Check if at least one shipping method is selected
    const checkedMethods = document.querySelectorAll('.edit-shipping-method-checkbox:checked');
    if (checkedMethods.length === 0) {
        showNotification('لطفاً حداقل یک روش ارسال را انتخاب کنید', 'error');
        document.getElementById('editShippingMethodsContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    formData.append('_method', 'PUT');
    
    fetch(`{{ route('admin.listings.update', $listing) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تغییرات با موفقیت ذخیره شد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در ذخیره تغییرات', 'error');
        }
    })
    .catch(error => handleFetchError(error, 'خطا در ذخیره تغییرات'));
});

// Toggle price input for edit form
function toggleEditPriceInput(checkbox, methodId) {
    const container = document.getElementById('edit-price-container-' + methodId);
    const input = document.getElementById('edit-price-input-' + methodId);
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
        input.disabled = false;
    } else {
        container.classList.add('hidden');
        input.disabled = true;
    }
}

// Seller Contact
function contactSeller() {
    window.location.href = `/admin/stores/{{ $listing->store->id }}/message`;
}

// Close modal on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Auto-refresh bids every 30 seconds
setInterval(() => {
    fetch(`{{ route('admin.listings.bids', $listing) }}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.bids && data.bids.length > 0) {
            updateBidsContainer(data.bids);
        }
    });
}, 30000);

function updateBidsContainer(bids) {
    // Update bids container with new data
    // This is a simplified version - adjust based on your needs
    const container = document.getElementById('bids-container');
    // Update logic here
}

const editAttributesSection = document.getElementById('editAttributesSection');
const editAttributesContainer = document.getElementById('editAttributesContainer');

// بارگذاری ویژگی‌های موجود
const currentAttributes = @json($listing->attributeValues->mapWithKeys(function($av) {
    return [$av->category_attribute_id => $av->value];
}));

// گوش دادن به تغییرات category_id (hidden input از component)
const categoryInput = document.getElementById('categorySelect');
if (categoryInput) {
    // بارگذاری اولیه
    if (categoryInput.value) {
        loadEditAttributes(categoryInput.value);
    }
    
    // گوش دادن به تغییرات با MutationObserver
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                const newValue = categoryInput.value;
                if (newValue) {
                    loadEditAttributes(newValue);
                }
            }
        });
    });
    
    observer.observe(categoryInput, {
        attributes: true,
        attributeFilter: ['value']
    });
    
    // همچنین با Alpine.js
    document.addEventListener('alpine:initialized', () => {
        Alpine.effect(() => {
            const val = categoryInput.value;
            if (val) {
                loadEditAttributes(val);
            }
        });
    });
}

function loadEditAttributes(categoryId) {
    if (!categoryId) {
        editAttributesSection.style.display = 'none';
        editAttributesContainer.innerHTML = '';
        return;
    }
    
    fetch(`{{ url('/api/categories') }}/${categoryId}/attributes`)
        .then(response => response.json())
        .then(data => {
            if (data.attributes && data.attributes.length > 0) {
                editAttributesContainer.innerHTML = '';
                
                data.attributes.forEach(attr => {
                    const div = document.createElement('div');
                    const fieldName = `attributes[${attr.id}]`;
                    const required = attr.is_required ? 'required' : '';
                    const requiredLabel = attr.is_required ? '<span class="text-red-500">*</span>' : '';
                    const currentValue = currentAttributes[attr.id] || '';
                    
                    let inputHtml = '';
                    
                    if (attr.type === 'select' && attr.options) {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <select name="${fieldName}" ${required}
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">انتخاب کنید</option>
                                ${attr.options.map(opt => `<option value="${opt}" ${currentValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                            </select>
                        `;
                    } else if (attr.type === 'number') {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <input type="number" name="${fieldName}" value="${currentValue}" ${required}
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="${attr.name}">
                        `;
                    } else {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <input type="text" name="${fieldName}" value="${currentValue}" ${required}
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="${attr.name}">
                        `;
                    }
                    
                    div.innerHTML = inputHtml;
                    editAttributesContainer.appendChild(div);
                });
                
                editAttributesSection.style.display = 'block';
            } else {
                editAttributesSection.style.display = 'none';
                editAttributesContainer.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error fetching attributes:', error);
            editAttributesSection.style.display = 'none';
        });
}
</script>
@endpush

