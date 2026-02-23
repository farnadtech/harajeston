<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ویرایش حراجی - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}?v={{ now()->timestamp }}">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#135bec",
                        "secondary": "#f97316",
                        "background-light": "#f8f9fc",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Vazirmatn", "sans-serif"],
                        "body": ["Vazirmatn", "sans-serif"],
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "2xl": "1.5rem",
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }
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
        
        /* فیکس فلش select برای RTL */
        select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-color: white !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5em !important;
            background-position: left 0.5rem center !important;
            padding-left: 2.5rem !important;
            padding-right: 0.75rem !important;
        }
    </style>
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-l border-gray-200 hidden lg:flex flex-col h-screen fixed right-0 top-0 z-30">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-100">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">storefront</span>
            </div>
            <h1 class="text-xl font-black tracking-tight text-[#0d121b]">
                حراج<span class="text-primary">آنلاین</span>
            </h1>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">dashboard</span>
                <span>داشبورد</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('my-listings') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">inventory_2</span>
                <span>مزایده‌های من</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-primary bg-primary/5 rounded-xl font-bold transition-colors" href="{{ route('listings.create') }}">
                <span class="material-symbols-outlined">add_circle</span>
                <span>افزودن مزایده جدید</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('wallet.show') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">account_balance_wallet</span>
                <span>کیف پول مالی</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('orders.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">shopping_bag</span>
                <span>سفارشات</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('stores.edit') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">store</span>
                <span>تنظیمات فروشگاه</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span>خروج از حساب</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:mr-64 flex flex-col h-screen overflow-hidden relative w-full">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-8 shrink-0">
            <div class="hidden lg:block">
                <h2 class="text-xl font-bold text-gray-800">ایجاد حراجی جدید</h2>
                <p class="text-sm text-gray-500">افزودن محصول جدید به فروشگاه</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden md:block">
                    <input type="text" placeholder="جستجو..." class="w-64 px-4 py-2 pr-10 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                </div>
                
                <button class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <div class="flex items-center gap-3 pr-4 border-r border-gray-200 mr-2">
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">مدیر فروشگاه</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Form Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-4xl mx-auto">
                {{-- نمایش خطاهای validation --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-r-4 border-red-500 rounded-2xl p-6 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-2xl">error</span>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-red-800 mb-2">لطفاً خطاهای زیر را برطرف کنید:</h3>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('listings.update', $listing) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Basic Info Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                            <span class="material-symbols-outlined text-primary text-2xl">info</span>
                            <h3 class="text-lg font-bold text-gray-900">اطلاعات پایه</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                عنوان حراجی <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $listing->title) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                   placeholder="مثال: گوشی آیفون ۱۳ پرو مکس">
                            @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                توضیحات کامل <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="6" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                      placeholder="توضیحات کامل محصول، ویژگی‌ها، و نکات مهم را وارد کنید...">{{ old('description', $listing->description) }}</textarea>
                            @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                دسته‌بندی <span class="text-red-500">*</span>
                            </label>
                            <x-category-selector :selected="old('category_id')" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                وضعیت کالا <span class="text-red-500">*</span>
                            </label>
                            <select name="condition" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>نو</option>
                                <option value="like_new" {{ old('condition') === 'like_new' ? 'selected' : '' }}>در حد نو</option>
                                <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>دست دوم</option>
                            </select>
                            @error('condition')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Attributes Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-6">
                            <span class="material-symbols-outlined text-primary text-2xl">tune</span>
                            <h3 class="text-lg font-bold text-gray-900">مشخصات فنی</h3>
                        </div>
                        <x-listing-attributes />
                    </div>

                    <!-- Auction Settings Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                            <span class="material-symbols-outlined text-primary text-2xl">gavel</span>
                            <h3 class="text-lg font-bold text-gray-900">تنظیمات مزایده</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    قیمت شروع (تومان) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="starting_price" value="{{ old('starting_price') }}" required min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                @error('starting_price')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">قیمت خرید فوری (تومان)</label>
                                <input type="number" name="buy_now_price" value="{{ old('buy_now_price') }}" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                @error('buy_now_price')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ سپرده (تومان)</label>
                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', 0) }}" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                @error('deposit_amount')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">حداقل افزایش پیشنهاد (تومان)</label>
                                <input type="number" name="bid_increment" value="{{ old('bid_increment', 10000) }}" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                @error('bid_increment')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @php
                                $forceDuration = \App\Models\SiteSetting::get('force_auction_duration', false);
                                $durationDays = \App\Models\SiteSetting::get('auction_duration_days', 7);
                            @endphp

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    زمان شروع <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="starts_at" 
                                       id="starts_at" 
                                       value="{{ old('starts_at') }}" 
                                       required
                                       class="persian-datepicker-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="انتخاب تاریخ و زمان"
                                       autocomplete="off"
                                       onchange="calculateEndDate()">
                                @error('starts_at')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="ends_at_container" class="{{ $forceDuration ? 'hidden' : '' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    زمان پایان <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="ends_at" 
                                       id="ends_at" 
                                       value="{{ old('ends_at') }}" 
                                       {{ $forceDuration ? '' : 'required' }}
                                       class="persian-datepicker-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="انتخاب تاریخ و زمان"
                                       autocomplete="off">
                                @error('ends_at')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if($forceDuration)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">محاسبه خودکار زمان پایان</p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        زمان پایان حراجی به صورت خودکار {{ \App\Services\PersianNumberService::convertToPersian($durationDays) }} روز بعد از زمان شروع محاسبه می‌شود.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="ends_at" id="ends_at_hidden" value="{{ old('ends_at') }}">
                        @endif

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_extend" value="1" {{ old('auto_extend') ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="text-sm text-gray-700">تمدید خودکار در صورت پیشنهاد در دقایق پایانی</span>
                            </label>
                        </div>
                    </div>

                    <!-- Shipping Methods Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-6">
                            <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
                            <h3 class="text-lg font-bold text-gray-900">روش‌های ارسال <span class="text-red-500">*</span></h3>
                        </div>
                        
                        @php
                            $shippingMethods = \App\Models\ShippingMethod::where('is_active', true)->get();
                        @endphp
                        
                        @if($shippingMethods->count() > 0)
                        <div class="space-y-3" id="shippingMethodsContainer">
                            @foreach($shippingMethods as $method)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors" data-method-id="{{ $method->id }}">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" 
                                           name="shipping_methods[]" 
                                           value="{{ $method->id }}"
                                           class="w-4 h-4 text-primary rounded focus:ring-primary mt-1 shipping-method-checkbox"
                                           onchange="togglePriceInput(this, {{ $method->id }})">
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
                                        
                                        <div class="price-adjustment-container hidden" id="price-container-{{ $method->id }}">
                                            <label class="block text-xs text-gray-600 mb-1">قیمت سفارشی برای این محصول (تومان)</label>
                                            <input type="number" 
                                                   name="shipping_costs[{{ $method->id }}]" 
                                                   id="price-input-{{ $method->id }}"
                                                   value="{{ $method->base_cost }}"
                                                   min="0"
                                                   step="1000"
                                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                                   placeholder="قیمت ارسال برای این محصول">
                                            <p class="text-xs text-gray-500 mt-1">می‌توانید قیمت ارسال را برای این محصول تغییر دهید</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500">هیچ روش ارسالی تعریف نشده است.</p>
                        @endif
                        @error('shipping_methods')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Images Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-6">
                            <span class="material-symbols-outlined text-primary text-2xl">image</span>
                            <h3 class="text-lg font-bold text-gray-900">تصاویر محصول</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">حداکثر 8 تصویر می‌توانید آپلود کنید. اولین تصویر به عنوان تصویر اصلی نمایش داده می‌شود.</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب تصاویر</label>
                                <input type="file" 
                                       name="images[]" 
                                       id="images" 
                                       multiple 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary file:text-white
                                              hover:file:bg-blue-600
                                              cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">فرمت‌های مجاز: JPG, PNG, GIF - حداکثر حجم هر تصویر: 2MB</p>
                                @error('images')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                @error('images.*')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Tags Section -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-6">
                            <span class="material-symbols-outlined text-primary text-2xl">label</span>
                            <h3 class="text-lg font-bold text-gray-900">برچسب‌ها</h3>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">برچسب‌ها (با کاما جدا کنید)</label>
                            <input type="text" name="tags" value="{{ old('tags') }}"
                                   placeholder="مثال: لپتاپ, گیمینگ, ارزان"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                            <p class="text-xs text-gray-500 mt-1">حداکثر 5 برچسب</p>
                            @error('tags')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-bold shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">check</span>
                            ثبت حراجی
                        </button>
                        <a href="{{ route('my-listings') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Alpine.js (بدون defer) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="{{ url('js/persian-datepicker-package.js') }}?v={{ now()->timestamp }}"></script>
    <script>
const FORCE_DURATION = {{ $forceDuration ? 'true' : 'false' }};
const DURATION_DAYS = {{ $durationDays }};

// Initialize datepickers
document.addEventListener('DOMContentLoaded', function() {
    const startsAtInput = document.getElementById('starts_at');
    if (startsAtInput && !startsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(startsAtInput, {
            minDate: 'today'
        });
    }
    
    const endsAtInput = document.getElementById('ends_at');
    if (endsAtInput && !endsAtInput.dataset.pickerInitialized && !FORCE_DURATION) {
        new PersianDatePicker(endsAtInput);
    }
});

// Calculate end date automatically if force duration is enabled
function calculateEndDate() {
    if (!FORCE_DURATION) return;
    
    const startsAtInput = document.getElementById('starts_at');
    const endsAtHidden = document.getElementById('ends_at_hidden');
    
    if (!startsAtInput || !endsAtHidden) return;
    
    const startsAtValue = startsAtInput.value;
    if (!startsAtValue) return;
    
    const match = startsAtValue.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})\s+(\d{1,2}):(\d{1,2})$/);
    if (!match) return;
    
    const jy = parseInt(match[1]);
    const jm = parseInt(match[2]);
    const jd = parseInt(match[3]);
    const hour = parseInt(match[4]);
    const minute = parseInt(match[5]);
    
    let newJd = jd + DURATION_DAYS;
    let newJm = jm;
    let newJy = jy;
    
    const daysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    while (newJd > daysInMonth[newJm - 1]) {
        newJd -= daysInMonth[newJm - 1];
        newJm++;
        if (newJm > 12) {
            newJm = 1;
            newJy++;
        }
    }
    
    const endsAtValue = `${newJy}/${String(newJm).padStart(2, '0')}/${String(newJd).padStart(2, '0')} ${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
    endsAtHidden.value = endsAtValue;
}

// Toggle price input visibility
function togglePriceInput(checkbox, methodId) {
    const container = document.getElementById('price-container-' + methodId);
    const input = document.getElementById('price-input-' + methodId);
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
        input.disabled = false;
    } else {
        container.classList.add('hidden');
        input.disabled = true;
    }
}

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    const checkedMethods = document.querySelectorAll('.shipping-method-checkbox:checked');
    if (checkedMethods.length === 0) {
        e.preventDefault();
        alert('لطفاً حداقل یک روش ارسال را انتخاب کنید.');
        document.getElementById('shippingMethodsContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    const numberInputs = this.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        if (input.value) {
            input.value = input.value.replace(/,/g, '');
        }
    });
});

// Scroll to error if exists
@if ($errors->any())
    window.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.querySelector('.bg-red-50');
        if (errorBox) {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
@endif

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('imagePreview');
    
    previewContainer.innerHTML = '';
    
    if (files.length === 0) {
        previewContainer.style.display = 'none';
        return;
    }
    
    if (files.length > 8) {
        alert('حداکثر 8 تصویر می‌توانید انتخاب کنید.');
        e.target.value = '';
        return;
    }
    
    previewContainer.style.display = 'grid';
    
    files.forEach((file, index) => {
        if (file.size > 2 * 1024 * 1024) {
            alert(`حجم تصویر "${file.name}" بیش از 2MB است.`);
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${event.target.result}" 
                     class="w-full h-32 object-cover rounded-lg border-2 border-gray-200"
                     alt="Preview ${index + 1}">
                <div class="absolute top-2 right-2 bg-primary text-white text-xs px-2 py-1 rounded">
                    ${index === 0 ? 'تصویر اصلی' : index + 1}
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                    <span class="text-white opacity-0 group-hover:opacity-100 text-sm">
                        ${(file.size / 1024).toFixed(0)} KB
                    </span>
                </div>
            `;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
    </script>
</body>
</html>
