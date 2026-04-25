<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
        $siteTagline = \App\Models\SiteSetting::get('site_tagline', '');
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon', '');
        $faviconUrl = $siteFavicon ? rtrim(config('app.url'), '/') . '/storage/' . $siteFavicon : '';
        $titleSuffix = $siteTagline ? ' | ' . $siteName . ' - ' . $siteTagline : ' | ' . $siteName;
    @endphp
    <title>@yield('title', 'داشبورد مدیریت'){{ $titleSuffix }}</title>
    @if($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
    
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>
    @font-face {
        font-family: 'Material Symbols Outlined';
        font-style: normal;
        font-weight: 100 700;
        font-display: block;
        src: url('/haraj/public/fonts/MaterialSymbolsOutlined[FILL,GRAD,opsz,wght].woff2') format('woff2');
    }
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
    </style>
    
    <style>
        body {
            font-family: 'Vazirmatn', 'Manrope', sans-serif;
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
        
        /* حذف کامل فلش پیش‌فرض مرورگر */
        select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        
        /* حذف فلش Tailwind Forms */
        [type='text'],
        [type='email'],
        [type='url'],
        [type='password'],
        [type='number'],
        [type='date'],
        [type='datetime-local'],
        [type='month'],
        [type='search'],
        [type='tel'],
        [type='time'],
        [type='week'],
        [multiple],
        textarea,
        select {
            background-image: none !important;
            background-position: 0 0 !important;
            padding-right: 0.75rem !important;
        }
        
        /* فلش سفارشی در سمت چپ */
        select:not(.no-arrow) {
            background-color: white !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: left 0.5rem center !important;
            background-size: 1.5em 1.5em !important;
            padding-left: 2.5rem !important;
            padding-right: 0.75rem !important;
        }
        
        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 300px;
            max-width: 500px;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            animation: slideDown 0.3s ease-out;
        }
        
        .notification.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .notification.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .notification.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .notification.info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
        
        @keyframes slideUp {
            from {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
        }
        
        .notification.hiding {
            animation: slideUp 0.3s ease-out forwards;
        }
    </style>
    
    @stack('styles')
    <style>
        /* Mobile sidebar */
        @media (max-width: 1023px) {
            #app-sidebar {
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }
            #app-sidebar.sidebar-open {
                transform: translateX(0);
            }
        }
        @media (min-width: 1024px) {
            #app-sidebar { transform: translateX(0) !important; }
            #sidebar-overlay { display: none !important; }
        }
    </style>
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex overflow-hidden">
    <!-- Mobile Overlay -->
    <div id="sidebar-overlay" onclick="closeSidebar()"
         class="hidden fixed inset-0 bg-black/50 z-40"></div>

    <!-- Sidebar -->
    <aside id="app-sidebar"
           class="w-64 border-l border-gray-200 flex flex-col h-screen fixed right-0 top-0 z-50 lg:z-30"
           style="background:{{ \App\Models\SiteSetting::get('theme_dashboard_sidebar_bg', '#ffffff') }};">
        <!-- Close button on mobile -->
        <button onclick="closeSidebar()" class="absolute top-4 left-4 p-1.5 text-gray-400 hover:text-gray-600 lg:hidden">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-100">
            @php
                $adLogo = \App\Models\SiteSetting::get('theme_dashboard_logo', '');
                $adText = \App\Models\SiteSetting::get('theme_dashboard_logo_text', 'پرشینآدمین');
                $adIcon = \App\Models\SiteSetting::get('theme_dashboard_logo_icon', 'gavel');
            @endphp
            @if($adLogo)
                <img src="{{ url('storage/'.$adLogo) }}" class="h-10 w-auto object-contain" alt="{{ $adText }}">
            @else
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl">{{ $adIcon }}</span>
                </div>
            @endif
            <h1 class="text-xl font-black tracking-tight text-[#0d121b]">{{ $adText }}</h1>
        </div>
        
        @php
            $isAuctionGroup   = request()->routeIs('admin.listings.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.category-attributes.*') || request()->routeIs('admin.bids.*');
            $isUsersGroup     = request()->routeIs('admin.users.*') || request()->routeIs('admin.sellers.*') || request()->routeIs('admin.store-name-requests.*') || request()->routeIs('admin.seller-reviews.*');
            $isOrdersGroup    = request()->routeIs('admin.orders.*') || request()->routeIs('admin.financial-reports.*') || request()->routeIs('admin.withdrawals.*') || request()->routeIs('admin.shipping-methods.*') || request()->routeIs('admin.payment-gateways.*');
            $isSupportGroup   = request()->routeIs('admin.tickets.*') || request()->routeIs('admin.comments.*') || request()->routeIs('admin.newsletter.*') || request()->routeIs('admin.notifications.*');
            $isSettingsGroup  = request()->routeIs('admin.settings.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.theme.*') || request()->routeIs('admin.pages.*') || request()->routeIs('admin.notification-settings.*') || request()->routeIs('admin.sms-gateways.*');
        @endphp

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- داشبورد --}}
            <a href="{{ route('admin.dashboard') }}"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors
                      {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span class="text-sm">داشبورد</span>
            </a>

            {{-- ── مزایده‌ها ── --}}
            <div x-data="{ open: {{ $isAuctionGroup ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 mt-3 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                    <span>مزایده‌ها</span>
                    <span class="material-symbols-outlined text-[16px] mr-auto transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" class="space-y-0.5 pr-3">
                    <a href="{{ route('admin.listings.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.listings.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">sell</span>
                        <span>مدیریت آگهی‌ها</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.category-attributes.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">category</span>
                        <span>دسته‌بندی‌ها</span>
                    </a>
                </div>
            </div>

            {{-- ── کاربران ── --}}
            <div x-data="{ open: {{ $isUsersGroup ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 mt-3 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                    <span>کاربران</span>
                    <span class="material-symbols-outlined text-[16px] mr-auto transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" class="space-y-0.5 pr-3">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                        <span>مدیریت کاربران</span>
                    </a>
                    <a href="{{ route('admin.sellers.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.sellers.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">storefront</span>
                        <span>فروشندگان</span>
                        @php $pendingSellersCount = \App\Models\User::where('seller_status', 'pending')->count(); @endphp
                        @if($pendingSellersCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingSellersCount)</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.store-name-requests.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.store-name-requests.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">edit_note</span>
                        <span>تغییر نام فروشگاه</span>
                        @php $pendingStoreNameCount = \App\Models\Store::whereNotNull('pending_store_name')->count(); @endphp
                        @if($pendingStoreNameCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingStoreNameCount)</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.seller-reviews.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.seller-reviews.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">rate_review</span>
                        <span>نظرات فروشندگان</span>
                        @php $pendingReviewsCount = \App\Models\SellerReview::pending()->count(); @endphp
                        @if($pendingReviewsCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingReviewsCount)</span>
                        @endif
                    </a>
                </div>
            </div>

            {{-- ── سفارشات و مالی ── --}}
            <div x-data="{ open: {{ $isOrdersGroup ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 mt-3 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">payments</span>
                    <span>سفارشات و مالی</span>
                    <span class="material-symbols-outlined text-[16px] mr-auto transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" class="space-y-0.5 pr-3">
                    <a href="{{ route('admin.orders.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        <span>سفارشات</span>
                    </a>
                    <a href="{{ route('admin.financial-reports.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.financial-reports.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                        <span>گزارشات مالی</span>
                    </a>
                    <a href="{{ route('admin.withdrawals.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.withdrawals.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                        <span>درخواست‌های برداشت</span>
                        @php $pendingWithdrawCount = \App\Models\WithdrawalRequest::where('status','pending')->count(); @endphp
                        @if($pendingWithdrawCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingWithdrawCount)</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.shipping-methods.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.shipping-methods.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        <span>روش‌های ارسال</span>
                    </a>
                    <a href="{{ route('admin.payment-gateways.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.payment-gateways.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">credit_card</span>
                        <span>درگاه‌های پرداخت</span>
                    </a>
                </div>
            </div>

            {{-- ── پشتیبانی ── --}}
            <div x-data="{ open: {{ $isSupportGroup ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 mt-3 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                    <span>پشتیبانی</span>
                    <span class="material-symbols-outlined text-[16px] mr-auto transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" class="space-y-0.5 pr-3">
                    <a href="{{ route('admin.tickets.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.tickets.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                        <span>تیکت‌های پشتیبانی</span>
                        @php $openTickets = \App\Models\Ticket::where('status', 'open')->count(); @endphp
                        @if($openTickets > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($openTickets)</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.comments.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.comments.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">help</span>
                        <span>پرسش‌های محصولات</span>
                        @php $pendingCommentsCount = \App\Models\ListingComment::pending()->whereNull('parent_id')->count(); @endphp
                        @if($pendingCommentsCount > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingCommentsCount)</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.newsletter.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.newsletter.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                        <span>خبرنامه</span>
                    </a>
                </div>
            </div>

            {{-- ── تنظیمات ── --}}
            <div x-data="{ open: {{ $isSettingsGroup ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2 mt-3 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">settings</span>
                    <span>تنظیمات</span>
                    <span class="material-symbols-outlined text-[16px] mr-auto transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" class="space-y-0.5 pr-3">
                    <a href="{{ route('admin.settings.general') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.settings.general') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">tune</span>
                        <span>تنظیمات عمومی</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.settings.index') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">manage_history</span>
                        <span>تنظیمات سیستم</span>
                    </a>
                    <a href="{{ route('admin.homepage.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.homepage.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">dashboard_customize</span>
                        <span>صفحه اصلی</span>
                    </a>
                    <a href="{{ route('admin.theme.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.theme.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">style</span>
                        <span>هدر و فوتر</span>
                    </a>
                    <a href="{{ route('admin.pages.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.pages.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">article</span>
                        <span>مدیریت صفحات</span>
                    </a>
                    <a href="{{ route('admin.notification-settings.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.notification-settings.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">notifications_active</span>
                        <span>تنظیمات اعلان‌ها</span>
                    </a>
                    <a href="{{ route('admin.sms-gateways.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.sms-gateways.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">sms</span>
                        <span>درگاه‌های پیامک</span>
                    </a>
                    <a href="{{ route('admin.update.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors text-sm
                              {{ request()->routeIs('admin.update.*') ? 'bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 font-medium' }}">
                        <span class="material-symbols-outlined text-[18px]">system_update</span>
                        <span>آپدیت سیستم</span>
                    </a>
                </div>
            </div>

        </nav>

        <div class="p-4 border-t border-gray-100">
            <form method="POST" action="{{ route('logout') }}">
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
            <div class="flex items-center gap-3">
                <button onclick="openSidebar()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg lg:hidden">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="lg:hidden">
                    <h1 class="text-base font-bold text-gray-900">@yield('page-title', 'داشبورد')</h1>
                </div>
                <div class="hidden lg:block">
                <h2 class="text-xl font-bold text-gray-800">@yield('header-title', 'خوش آمدید، ادمین عزیز 👋')</h2>
                <p class="text-sm text-gray-500">@yield('header-subtitle', 'گزارش کلی وضعیت بازار امروز')</p>
            </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block" x-data="adminSearch()" @click.away="open = false">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[20px] pointer-events-none">search</span>
                    <input
                        x-model="query"
                        @input.debounce.200ms="search()"
                        @focus="if(query.length > 0) open = true"
                        @keydown.escape="open = false"
                        @keydown.arrow-down.prevent="moveDown()"
                        @keydown.arrow-up.prevent="moveUp()"
                        @keydown.enter.prevent="goTo()"
                        class="w-64 h-10 pl-10 pr-4 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none"
                        placeholder="جستجو در پنل..."
                        type="text"
                        autocomplete="off"
                    />
                    {{-- Dropdown --}}
                    <div x-show="open && results.length > 0"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute top-12 left-0 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden"
                         style="display:none">
                        <template x-for="(item, index) in results" :key="index">
                            <a :href="item.url"
                               :class="index === active ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-50'"
                               class="flex items-center gap-3 px-4 py-3 transition-colors border-b border-gray-100 last:border-0">
                                <span class="material-symbols-outlined text-[18px] text-gray-400" x-text="item.icon"></span>
                                <div>
                                    <p class="text-sm font-medium" x-text="item.label"></p>
                                    <p class="text-xs text-gray-400" x-text="item.group"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                    <div x-show="open && query.length > 0 && results.length === 0"
                         class="absolute top-12 left-0 w-72 bg-white rounded-xl shadow-xl border border-gray-200 z-50 px-4 py-6 text-center text-sm text-gray-400"
                         style="display:none">
                        نتیجه‌ای یافت نشد
                    </div>
                </div>
                
                <!-- Notifications Dropdown -->
                <div class="relative" x-data="notificationDropdown()">
                    <button @click="toggleDropdown()" class="p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span x-show="unreadCount > 0" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50"
                         style="display: none;">
                        
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900">اعلان‌ها</h3>
                            <span x-text="unreadCount > 0 ? unreadCount + ' اعلان جدید' : 'اعلانی جدید نیست'" class="text-xs text-gray-500"></span>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="loading">
                                <div class="px-4 py-8 text-center">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                                    <p class="text-sm text-gray-500 mt-2">در حال بارگذاری...</p>
                                </div>
                            </template>
                            
                            <template x-if="!loading && (!notifications || notifications.length === 0)">
                                <div class="px-4 py-8 text-center">
                                    <span class="material-symbols-outlined text-gray-300 text-5xl mb-2">notifications_off</span>
                                    <p class="text-sm text-gray-500">اعلانی وجود ندارد</p>
                                </div>
                            </template>
                            
                            <template x-for="notification in notifications" :key="notification.id">
                                <a :href="notification.link ? '{{ route('admin.notifications.read', '') }}/' + notification.id : '#'" 
                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                    <div class="flex items-start gap-3">
                                        <div :class="'w-10 h-10 rounded-full bg-' + notification.color + '-100 flex items-center justify-center flex-shrink-0'">
                                            <span :class="'material-symbols-outlined text-' + notification.color + '-600 text-xl'" x-text="notification.icon"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 mb-1" x-text="notification.title"></p>
                                            <p class="text-xs text-gray-600 mb-1" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-400" x-text="notification.time_ago"></p>
                                        </div>
                                        <span x-show="!notification.is_read" :class="'w-2 h-2 bg-' + notification.color + '-500 rounded-full flex-shrink-0 mt-2'"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                        
                        <!-- Footer -->
                        <div class="px-4 py-3 border-t border-gray-200 text-center">
                            <a href="{{ route('admin.notifications.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">مشاهده همه اعلان‌ها</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 pr-4 border-r border-gray-200 mr-2 hover:bg-gray-50 rounded-lg py-2 px-3 transition-colors">
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">مدیر سیستم</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden border-2 border-white shadow-sm flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-500">person</span>
                        </div>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 z-50 py-2"
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="material-symbols-outlined text-gray-500 text-xl">home</span>
                            <span class="text-sm">بازگشت به سایت</span>
                        </a>
                        
                        <a href="{{ route('wallet.show') }}" class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="material-symbols-outlined text-gray-500 text-xl">account_balance_wallet</span>
                            <span class="text-sm">کیف پول</span>
                        </a>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-xl">logout</span>
                                <span class="text-sm">خروج از حساب</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // Sidebar toggle
        function openSidebar() {
            document.getElementById('app-sidebar').classList.add('sidebar-open');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('app-sidebar').classList.remove('sidebar-open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
        // Close sidebar on outside click
        document.addEventListener('click', function(e) {
            var sidebar = document.getElementById('app-sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            if (sidebar && !sidebar.contains(e.target) && sidebar.classList.contains('sidebar-open')) {
                var hamburger = document.querySelector('[onclick="openSidebar()"]');
                if (!hamburger || !hamburger.contains(e.target)) {
                    closeSidebar();
                }
            }
        });
    </script>

    <script>
        // Alpine.js Notification Dropdown Component
        function notificationDropdown() {
            return {
                open: false,
                loading: false,
                notifications: [],
                unreadCount: 0,
                
                init() {
                    this.loadNotifications();
                },
                
                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open && (!this.notifications || this.notifications.length === 0)) {
                        this.loadNotifications();
                    }
                },
                
                loadNotifications() {
                    this.loading = true;
                    
                    fetch('{{ route('admin.notifications.recent') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        this.notifications = [];
                        this.unreadCount = 0;
                        this.loading = false;
                    });
                }
            }
        }
    </script>
    
    <!-- Confirm Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600 text-3xl">warning</span>
                    </div>
                    <h3 id="confirmTitle" class="text-xl font-bold text-gray-900"></h3>
                </div>
                <p id="confirmMessage" class="text-gray-600 mb-6 leading-relaxed"></p>
                <div class="flex gap-3">
                    <button id="confirmCancel" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        انصراف
                    </button>
                    <button id="confirmOk" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium transition-colors">
                        تایید
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Prompt Modal (for text input) -->
    <div id="promptModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-3xl">edit_note</span>
                    </div>
                    <h3 id="promptTitle" class="text-xl font-bold text-gray-900"></h3>
                </div>
                <p id="promptMessage" class="text-gray-600 mb-4 leading-relaxed"></p>
                <textarea id="promptInput" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent mb-4"
                          placeholder="متن خود را وارد کنید..."></textarea>
                <div class="flex gap-3">
                    <button id="promptCancel" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        انصراف
                    </button>
                    <button id="promptOk" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 font-medium transition-colors">
                        تایید
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Notification System
        function showNotification(message, type = 'success') {
            // Remove existing notifications
            const existing = document.querySelectorAll('.notification');
            existing.forEach(n => n.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            // Icon based on type
            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };
            
            notification.innerHTML = `
                <span class="material-symbols-outlined text-2xl">${icons[type] || 'info'}</span>
                <span class="flex-1 font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="hover:opacity-70 transition-opacity">
                    <span class="material-symbols-outlined">close</span>
                </button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('hiding');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
        
        // Confirm Modal System
        function showConfirmModal(title, message, okText = 'تایید', cancelText = 'انصراف', onConfirm = null) {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const okBtn = document.getElementById('confirmOk');
            const cancelBtn = document.getElementById('confirmCancel');
            
            titleEl.textContent = title;
            messageEl.textContent = message;
            okBtn.textContent = okText;
            cancelBtn.textContent = cancelText;
            
            modal.classList.remove('hidden');
            
            // Remove old event listeners
            const newOkBtn = okBtn.cloneNode(true);
            const newCancelBtn = cancelBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
            
            // Add new event listeners
            newOkBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                if (onConfirm) onConfirm();
            });
            
            newCancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
            
            // Close on outside click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }
        
        // Prompt Modal System (for text input)
        function showPromptModal(title, message, okText = 'تایید', cancelText = 'انصراف', onConfirm = null) {
            const modal = document.getElementById('promptModal');
            const titleEl = document.getElementById('promptTitle');
            const messageEl = document.getElementById('promptMessage');
            const inputEl = document.getElementById('promptInput');
            const okBtn = document.getElementById('promptOk');
            const cancelBtn = document.getElementById('promptCancel');
            
            titleEl.textContent = title;
            messageEl.textContent = message;
            okBtn.textContent = okText;
            cancelBtn.textContent = cancelText;
            inputEl.value = '';
            
            modal.classList.remove('hidden');
            inputEl.focus();
            
            // Remove old event listeners
            const newOkBtn = okBtn.cloneNode(true);
            const newCancelBtn = cancelBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
            
            // Add new event listeners
            newOkBtn.addEventListener('click', () => {
                const value = inputEl.value.trim();
                modal.classList.add('hidden');
                if (onConfirm) onConfirm(value);
            });
            
            newCancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
            
            // Close on outside click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
            
            // Submit on Enter key
            inputEl.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const value = inputEl.value.trim();
                    modal.classList.add('hidden');
                    if (onConfirm) onConfirm(value);
                }
            });
        }
        
        // Global error handler for fetch
        window.handleFetchError = function(error, defaultMessage = 'خطا در ارتباط با سرور') {
            console.error('Error:', error);
            showNotification(defaultMessage, 'error');
        };
    </script>
    
    <script>
    function adminSearch() {
        return {
            query: '',
            open: false,
            active: 0,
            results: [],
            pages: [
                { label: 'داشبورد', url: '/haraj/public/admin/dashboard', icon: 'dashboard', group: 'عمومی', keywords: 'خانه آمار' },
                // مزایده‌ها
                { label: 'مدیریت آگهی‌ها', url: '/haraj/public/admin/listings', icon: 'sell', group: 'مزایده‌ها', keywords: 'حراج مزایده آگهی لیست' },
                { label: 'دسته‌بندی‌ها', url: '/haraj/public/admin/categories', icon: 'category', group: 'مزایده‌ها', keywords: 'کتگوری گروه' },
                // کاربران
                { label: 'مدیریت کاربران', url: '/haraj/public/admin/users', icon: 'manage_accounts', group: 'کاربران', keywords: 'یوزر اکانت' },
                { label: 'فروشندگان', url: '/haraj/public/admin/sellers', icon: 'storefront', group: 'کاربران', keywords: 'فروشگاه seller' },
                { label: 'تغییر نام فروشگاه', url: '/haraj/public/admin/store-name-requests', icon: 'edit_note', group: 'کاربران', keywords: 'store name' },
                { label: 'نظرات فروشندگان', url: '/haraj/public/admin/seller-reviews', icon: 'rate_review', group: 'کاربران', keywords: 'review امتیاز' },
                // سفارشات
                { label: 'سفارشات', url: '/haraj/public/admin/orders', icon: 'shopping_bag', group: 'سفارشات و مالی', keywords: 'order خرید' },
                { label: 'گزارشات مالی', url: '/haraj/public/admin/financial-reports', icon: 'bar_chart', group: 'سفارشات و مالی', keywords: 'درآمد کمیسیون report' },
                { label: 'درخواست‌های برداشت', url: '/haraj/public/admin/withdrawals', icon: 'account_balance', group: 'سفارشات و مالی', keywords: 'پول کیف پول withdrawal' },
                { label: 'روش‌های ارسال', url: '/haraj/public/admin/shipping-methods', icon: 'local_shipping', group: 'سفارشات و مالی', keywords: 'پست shipping حمل' },
                { label: 'درگاه‌های پرداخت', url: '/haraj/public/admin/payment-gateways', icon: 'credit_card', group: 'سفارشات و مالی', keywords: 'زرین پال payment gateway بانک' },
                // پشتیبانی
                { label: 'تیکت‌های پشتیبانی', url: '/haraj/public/admin/tickets', icon: 'confirmation_number', group: 'پشتیبانی', keywords: 'ticket support' },
                { label: 'پرسش‌های محصولات', url: '/haraj/public/admin/comments', icon: 'help', group: 'پشتیبانی', keywords: 'سوال comment نظر' },
                { label: 'خبرنامه', url: '/haraj/public/admin/newsletter', icon: 'mail', group: 'پشتیبانی', keywords: 'ایمیل email newsletter' },
                // تنظیمات عمومی — با keywords کامل برای هر بخش
                { label: 'تنظیمات عمومی', url: '/haraj/public/admin/settings/general', icon: 'tune', group: 'تنظیمات', keywords: 'نام سایت لوگو فاویکون آدرس تلفن ایمیل' },
                { label: 'رنگ‌بندی و تم سایت', url: '/haraj/public/admin/settings/general', icon: 'palette', group: 'تنظیمات عمومی', keywords: 'رنگ تم color theme پس‌زمینه primary secondary' },
                { label: 'شبکه‌های اجتماعی', url: '/haraj/public/admin/settings/general', icon: 'share', group: 'تنظیمات عمومی', keywords: 'اینستاگرام تلگرام واتساپ social instagram telegram' },
                { label: 'فوتر سایت', url: '/haraj/public/admin/settings/general', icon: 'web', group: 'تنظیمات عمومی', keywords: 'footer متن پایین' },
                // تنظیمات سیستم
                { label: 'تنظیمات سیستم', url: '/haraj/public/admin/settings', icon: 'manage_history', group: 'تنظیمات', keywords: 'سپرده کمیسیون مزایده otp احراز هویت' },
                { label: 'تنظیمات سپرده', url: '/haraj/public/admin/settings', icon: 'savings', group: 'تنظیمات سیستم', keywords: 'deposit سپرده ضمانت' },
                { label: 'تنظیمات کمیسیون', url: '/haraj/public/admin/settings', icon: 'percent', group: 'تنظیمات سیستم', keywords: 'commission درصد کارمزد' },
                { label: 'تنظیمات کیف پول', url: '/haraj/public/admin/settings', icon: 'account_balance_wallet', group: 'تنظیمات سیستم', keywords: 'wallet شارژ برداشت مالیات' },
                { label: 'تنظیمات OTP و پیامک', url: '/haraj/public/admin/settings', icon: 'sms', group: 'تنظیمات سیستم', keywords: 'otp sms کد تایید موبایل' },
                { label: 'تنظیمات فروشندگان', url: '/haraj/public/admin/settings', icon: 'storefront', group: 'تنظیمات سیستم', keywords: 'seller approval تایید' },
                { label: 'تنظیمات آگهی‌ها', url: '/haraj/public/admin/settings', icon: 'sell', group: 'تنظیمات سیستم', keywords: 'listing approval bid increment' },
                // سایر
                { label: 'صفحه اصلی', url: '/haraj/public/admin/homepage', icon: 'dashboard_customize', group: 'تنظیمات', keywords: 'home بلوک بنر slider' },
                { label: 'هدر و فوتر', url: '/haraj/public/admin/theme', icon: 'style', group: 'تنظیمات', keywords: 'header footer menu navbar' },
                { label: 'مدیریت صفحات', url: '/haraj/public/admin/pages', icon: 'article', group: 'تنظیمات', keywords: 'page درباره ما تماس' },
                { label: 'تنظیمات اعلان‌ها', url: '/haraj/public/admin/notification-settings', icon: 'notifications_active', group: 'تنظیمات', keywords: 'notification email اعلان' },
                { label: 'درگاه‌های پیامک', url: '/haraj/public/admin/sms-gateways', icon: 'sms', group: 'تنظیمات', keywords: 'sms gateway kavenegar melipayamak' },
            ],
            search() {
                const q = this.query.trim().toLowerCase();
                if (q.length === 0) { this.open = false; this.results = []; return; }
                this.results = this.pages.filter(p =>
                    p.label.toLowerCase().includes(q) ||
                    p.group.toLowerCase().includes(q) ||
                    (p.keywords && p.keywords.toLowerCase().includes(q))
                ).slice(0, 8);
                this.active = 0;
                this.open = true;
            },
            moveDown() { if (this.active < this.results.length - 1) this.active++; },
            moveUp()   { if (this.active > 0) this.active--; },
            goTo()     { if (this.results[this.active]) window.location.href = this.results[this.active].url; },
        };
    }
    </script>
    <script src="/haraj/public/js/alpine.min.js"></script>
    @stack('scripts')
</body>
</html>
