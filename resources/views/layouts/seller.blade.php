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
        $colorPrimary = \App\Models\SiteSetting::get('color_primary', '#135bec');
        $colorPrimaryHover = \App\Models\SiteSetting::get('color_primary_hover', '#0e4bc7');
        $colorSecondary = \App\Models\SiteSetting::get('color_secondary', '#f97316');
        $titleSuffix = $siteTagline ? ' | ' . $siteName . ' - ' . $siteTagline : ' | ' . $siteName;
    @endphp
    <title>@yield('title', 'داشبورد فروشنده'){{ $titleSuffix }}</title>
    @if($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>
    :root {
        --color-primary: {{ $colorPrimary }};
        --color-primary-hover: {{ $colorPrimaryHover }};
        --color-secondary: {{ $colorSecondary }};
    }
    .text-primary { color: var(--color-primary) !important; }
    .bg-primary { background-color: var(--color-primary) !important; }
    .border-primary { border-color: var(--color-primary) !important; }
    .bg-primary\/10 { background-color: color-mix(in srgb, var(--color-primary) 10%, transparent) !important; }
    .hover\:bg-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
    </style>
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
    </style>
    @stack('styles')
    <style>
        @media (max-width: 1023px) {
            #app-sidebar { transform: translateX(100%); transition: transform 0.3s ease; }
            #app-sidebar.sidebar-open { transform: translateX(0); }
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
                $dLogo = \App\Models\SiteSetting::get('theme_dashboard_logo', '');
                $dText = \App\Models\SiteSetting::get('theme_dashboard_logo_text', 'حراجآنلاین');
                $dIcon = \App\Models\SiteSetting::get('theme_dashboard_logo_icon', 'storefront');
            @endphp
            @if($dLogo)
                <img src="{{ url('storage/'.$dLogo) }}" class="h-10 w-auto object-contain" alt="{{ $dText }}">
            @else
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl">{{ $dIcon }}</span>
                </div>
            @endif
            <h1 class="text-xl font-black tracking-tight text-[#0d121b]">{{ $dText }}</h1>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('dashboard') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? '' : 'group-hover:text-primary transition-colors' }}">dashboard</span>
                <span>داشبورد</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('my-listings') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('my-listings') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('my-listings') ? '' : 'group-hover:text-primary transition-colors' }}">inventory_2</span>
                <span>مزایده‌های من</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('listings.create') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('listings.create') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('listings.create') ? '' : 'group-hover:text-primary transition-colors' }}">add_circle</span>
                <span>افزودن مزایده جدید</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('wallet.show') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('wallet.show') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('wallet.show') ? '' : 'group-hover:text-primary transition-colors' }}">account_balance_wallet</span>
                <span>کیف پول مالی</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('orders.*') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('orders.index') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('orders.*') ? '' : 'group-hover:text-primary transition-colors' }}">shopping_bag</span>
                <span>سفارشات</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('tickets.*') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('tickets.index') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('tickets.*') ? '' : 'group-hover:text-primary transition-colors' }}">confirmation_number</span>
                <span>تیکت‌های پشتیبانی</span>
                @php
                    $unreadTickets = \App\Models\Ticket::where(function($q){ $q->where('creator_id', auth()->id())->orWhere('recipient_id', auth()->id()); })->whereHas('messages', fn($q) => $q->where('user_id', '!=', auth()->id())->where('is_read', false))->count();
                @endphp
                @if($unreadTickets > 0)
                    <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($unreadTickets)</span>
                @endif
            </a>
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('stores.edit') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group" href="{{ route('stores.edit') }}">
                <span class="material-symbols-outlined {{ request()->routeIs('stores.edit') ? '' : 'group-hover:text-primary transition-colors' }}">store</span>
                <span>تنظیمات فروشگاه</span>
            </a>
            @if(auth()->user()->store)
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 font-medium rounded-xl transition-colors group" href="{{ route('stores.show', auth()->user()->store->slug) }}" target="_blank">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">storefront</span>
                <span>صفحه فروشگاه من</span>
            </a>
            @endif
            
            <div class="pt-4 mt-4 border-t border-gray-200">
                <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('home') }}">
                    <span class="material-symbols-outlined group-hover:text-primary transition-colors">home</span>
                    <span>بازگشت به صفحه اصلی</span>
                </a>
            </div>
        </nav>
        
        <div class="p-4 border-t border-gray-100 space-y-1">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('profile.*') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium' }} rounded-xl transition-colors group">
                @if(auth()->user()->avatar)
                    <img src="{{ url('storage/'.auth()->user()->avatar) }}"
                         class="rounded-full object-cover flex-shrink-0" style="width:20px;height:20px;">
                @else
                    <span class="material-symbols-outlined group-hover:text-primary transition-colors">manage_accounts</span>
                @endif
                <span>ویرایش پروفایل</span>
            </a>
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
            <div class="flex items-center gap-3">
                <button onclick="openSidebar()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg lg:hidden">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div class="hidden lg:block">
                    <h2 class="text-xl font-bold text-gray-800">@yield('page-title', 'داشبورد')</h2>
                    <p class="text-sm text-gray-500">@yield('page-subtitle', 'خوش آمدید')</p>
                </div>
                <div class="lg:hidden">
                    <h2 class="text-base font-bold text-gray-800">@yield('page-title', 'داشبورد')</h2>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Notification Dropdown - same as main layout -->
                <div class="relative" id="sellerNotifDropdown">
                    <button onclick="toggleSellerNotif()" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span id="sellerNotifBadge" class="hidden absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <div id="sellerNotifPanel"
                         class="hidden absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 text-sm">اعلان‌ها</h3>
                            <a href="{{ route('user.notifications.index') }}" class="text-xs text-primary hover:underline">همه</a>
                        </div>
                        <div id="sellerNotifList" class="max-h-80 overflow-y-auto">
                            <div class="px-4 py-8 text-center text-gray-400">
                                <span class="material-symbols-outlined text-3xl block mb-2">notifications</span>
                                <p class="text-sm">در حال بارگذاری...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                var sellerNotifOpen = false;
                function toggleSellerNotif() {
                    sellerNotifOpen = !sellerNotifOpen;
                    var panel = document.getElementById('sellerNotifPanel');
                    if (sellerNotifOpen) {
                        panel.classList.remove('hidden');
                        loadSellerNotifs();
                    } else {
                        panel.classList.add('hidden');
                    }
                }
                document.addEventListener('click', function(e) {
                    var dropdown = document.getElementById('sellerNotifDropdown');
                    if (dropdown && !dropdown.contains(e.target) && sellerNotifOpen) {
                        sellerNotifOpen = false;
                        document.getElementById('sellerNotifPanel').classList.add('hidden');
                    }
                });
                function loadSellerNotifs() {
                    fetch('{{ route('user.notifications.recent') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        credentials: 'same-origin'
                    })
                    .then(function(r){ return r.json(); })
                    .then(function(data) {
                        var badge = document.getElementById('sellerNotifBadge');
                        if (data.unread_count > 0) {
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                        var list = document.getElementById('sellerNotifList');
                        if (!data.notifications || data.notifications.length === 0) {
                            list.innerHTML = '<div class="px-4 py-8 text-center text-gray-400"><span class="material-symbols-outlined text-3xl block mb-2">notifications_off</span><p class="text-sm">اعلانی وجود ندارد</p></div>';
                            return;
                        }
                        list.innerHTML = data.notifications.map(function(n) {
                            return '<a href="' + (n.link || '#') + '" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors' + (!n.is_read ? ' bg-blue-50/30' : '') + '">' +
                                '<div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 bg-' + n.color + '-100">' +
                                '<span class="material-symbols-outlined text-base text-' + n.color + '-600">' + n.icon + '</span></div>' +
                                '<div class="flex-1 min-w-0">' +
                                '<p class="text-sm ' + (!n.is_read ? 'font-semibold' : 'font-medium') + ' text-gray-900 leading-tight">' + n.title + '</p>' +
                                '<p class="text-xs text-gray-500 mt-0.5 line-clamp-2">' + n.message + '</p>' +
                                '<p class="text-xs text-gray-400 mt-1">' + n.time_ago + '</p></div>' +
                                (!n.is_read ? '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>' : '') +
                                '</a>';
                        }).join('');
                    })
                    .catch(function() {
                        document.getElementById('sellerNotifList').innerHTML = '<div class="px-4 py-8 text-center text-red-400 text-sm">خطا در بارگذاری</div>';
                    });
                }
                document.addEventListener('DOMContentLoaded', function() {
                    fetch('{{ route('user.notifications.recent') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                        credentials: 'same-origin'
                    })
                    .then(function(r){ return r.json(); })
                    .then(function(data) {
                        var badge = document.getElementById('sellerNotifBadge');
                        if (data.unread_count > 0) { badge.classList.remove('hidden'); }
                    }).catch(function(){});
                });
                </script>
                
                <div class="relative flex-shrink-0" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 pr-3 border-r border-gray-200 mr-2 focus:outline-none">
                        <div class="text-left hidden sm:block">
                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->role === 'seller' ? 'فروشنده' : 'کاربر' }}</p>
                        </div>
                        <div class="rounded-full overflow-hidden flex-shrink-0 border-2 border-gray-200 hover:border-primary transition-colors" style="width:32px;height:32px;min-width:32px;min-height:32px;">
                            @if(auth()->user()->avatar)
                                <img src="{{ url('storage/'.auth()->user()->avatar) }}"
                                     class="w-full h-full object-cover"
                                     alt="{{ auth()->user()->name }}">
                            @else
                                <div class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <span class="material-symbols-outlined text-gray-400 text-base hidden sm:block" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                            <div class="rounded-full overflow-hidden flex-shrink-0 border border-gray-200" style="width:32px;height:32px;min-width:32px;min-height:32px;">
                                @if(auth()->user()->avatar)
                                    <img src="{{ url('storage/'.auth()->user()->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-primary">ویرایش پروفایل</p>
                            </div>
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <span class="material-symbols-outlined text-base text-gray-400">dashboard</span>
                            داشبورد
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full text-right px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-base">logout</span>
                                خروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <x-verification-banner />
            @yield('content')
        </div>
    </main>

    <!-- Alpine.js for dropdown -->
    <script>
        function openSidebar() {
            document.getElementById('app-sidebar').classList.add('sidebar-open');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }
        function closeSidebar() {
            document.getElementById('app-sidebar').classList.remove('sidebar-open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            var sidebar = document.getElementById('app-sidebar');
            if (sidebar && !sidebar.contains(e.target) && sidebar.classList.contains('sidebar-open')) {
                var btn = document.querySelector('[onclick="openSidebar()"]');
                if (!btn || !btn.contains(e.target)) closeSidebar();
            }
        });
    </script>
    <script src="/haraj/public/js/alpine.min.js"></script>
    @stack('scripts')
</body>
</html>
