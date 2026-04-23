<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Persian Auction Marketplace')</title>
    
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <script defer src="/haraj/public/js/alpine.min.js"></script>
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
        /* Custom scrollbar for horizontal categories */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Slow spin animation for hourglass */
        @keyframes spin-slow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
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
    </style>
    
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex flex-col">
    @php
        $thBg     = \App\Models\SiteSetting::get('theme_header_bg', '#ffffff');
        $thHeight = \App\Models\SiteSetting::get('theme_header_height', '80');
        $thSticky = \App\Models\SiteSetting::get('theme_header_sticky', '1');
        $thLogo   = \App\Models\SiteSetting::get('theme_header_logo', '');
        $thText   = \App\Models\SiteSetting::get('theme_header_logo_text', 'پرشینآکشن');
        $thIcon   = \App\Models\SiteSetting::get('theme_header_logo_icon', 'gavel');
        $thLogoSz = max(20, (int)\App\Models\SiteSetting::get('theme_header_logo_size', '40'));
        $thSearch = \App\Models\SiteSetting::get('theme_header_show_search', '1');
        $thCats   = \App\Models\SiteSetting::get('theme_header_show_cats', '1');
        $thNavRaw = \App\Models\SiteSetting::get('theme_header_nav_links', '[]');
        $thNav    = is_array($thNavRaw) ? $thNavRaw : (json_decode($thNavRaw, true) ?? []);
    @endphp
    <!-- Sticky Header -->
    <header class="{{ $thSticky ? 'sticky top-0' : '' }} z-50 border-b border-[#e7ebf3] shadow-sm" style="background:{{ $thBg }};">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4" style="height:{{ $thHeight }}px;">
                <!-- Right Side: Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('listings.index') }}" class="flex items-center gap-3">
                        @if($thLogo)
                            <img src="{{ url('storage/'.$thLogo) }}" style="height:{{ $thLogoSz }}px;width:auto;object-fit:contain;" alt="{{ $thText }}">
                        @else
                            <div style="width:{{ $thLogoSz }}px;height:{{ $thLogoSz }}px;" class="bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined" style="font-size:{{ round($thLogoSz*0.6) }}px;">{{ $thIcon }}</span>
                            </div>
                        @endif
                        <h1 class="text-2xl font-black tracking-tight text-[#0d121b]">{{ $thText }}</h1>
                    </a>
                </div>
                
                <!-- Center: Search Bar (Hidden on mobile, visible on desktop) -->
                @if($thSearch)
                <div class="hidden md:flex flex-1 max-w-2xl px-8">
                    <div class="relative w-full group">
                        <form method="GET" action="{{ route('listings.index') }}" class="relative w-full" id="searchForm">
                            <input name="search" value="{{ request('search') }}" 
                                   class="block w-full h-12 pr-4 pl-12 bg-[#f1f3f7] border-transparent rounded-xl focus:bg-white focus:border-primary focus:ring-primary sm:text-sm transition-all duration-200" 
                                   placeholder="جستجو در بین هزاران کالا..." 
                                   type="text"
                                   autocomplete="off"
                                   id="searchInput"/>
                            <button type="submit" class="absolute inset-y-1 left-1 bg-primary text-white px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-lg">search</span>
                                <span class="hidden lg:inline">جستجو</span>
                            </button>
                        </form>
                        
                        <!-- Search Suggestions Dropdown -->
                        <div id="searchSuggestions" class="hidden absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-lg z-50 border border-gray-200 overflow-hidden">
                            <div id="suggestionsList" class="max-h-96 overflow-y-auto">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('searchInput');
                        const searchForm = document.getElementById('searchForm');
                        const suggestionsContainer = document.getElementById('searchSuggestions');
                        const suggestionsList = document.getElementById('suggestionsList');
                        
                        let searchTimeout;
                        let currentFocus = -1;
                        
                        searchInput.addEventListener('input', function() {
                            clearTimeout(searchTimeout);
                            
                            const query = this.value.trim();
                            
                            if (query.length === 0) {
                                suggestionsContainer.classList.add('hidden');
                                currentFocus = -1;
                                return;
                            }
                            
                            if (query.length < 2) {
                                return; // Don't search for less than 2 characters
                            }
                            
                            // Show loading state
                            suggestionsList.innerHTML = `
                                <div class="px-4 py-8 text-center text-gray-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 block animate-spin">progress_activity</span>
                                    <p class="text-sm">در حال جستجو...</p>
                                </div>
                            `;
                            suggestionsContainer.classList.remove('hidden');
                            
                            // Debounce the search
                            searchTimeout = setTimeout(() => {
                                performLiveSearch(query);
                            }, 300); // Wait 300ms after user stops typing
                        });
                        
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'ArrowDown') {
                                e.preventDefault();
                                currentFocus++;
                                addActive(currentFocus);
                            } else if (e.key === 'ArrowUp') {
                                e.preventDefault();
                                currentFocus--;
                                addActive(currentFocus);
                            } else if (e.key === 'Enter') {
                                const activeItem = document.querySelector('#suggestionsList .autocomplete-active');
                                if (activeItem) {
                                    e.preventDefault();
                                    activeItem.click();
                                }
                                // Otherwise let form submit normally
                            } else if (e.key === 'Escape') {
                                suggestionsContainer.classList.add('hidden');
                                currentFocus = -1;
                            }
                        });
                        
                        function performLiveSearch(query) {
                            // Use Laravel route
                            const apiUrl = '{{ url("/api/listings/search") }}?q=' + encodeURIComponent(query);
                            
                            fetch(apiUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                credentials: 'same-origin'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('خطا در جستجو');
                                }
                                return response.json();
                            })
                            .then(data => {
                                displaySuggestions(data);
                            })
                            .catch(error => {
                                console.error('Search error:', error);
                                suggestionsList.innerHTML = `
                                    <div class="px-4 py-8 text-center text-red-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 block">error</span>
                                        <p class="text-sm">خطا در جستجو</p>
                                    </div>
                                `;
                            });
                        }
                        
                        function displaySuggestions(results) {
                            if (!results || results.length === 0) {
                                suggestionsList.innerHTML = `
                                    <div class="px-4 py-8 text-center text-gray-400">
                                        <span class="material-symbols-outlined text-4xl mb-2 block">search_off</span>
                                        <p class="text-sm">نتیجه‌ای یافت نشد</p>
                                    </div>
                                `;
                                return;
                            }
                            
                            suggestionsList.innerHTML = '';
                            
                            results.forEach((result, index) => {
                                const suggestionItem = document.createElement('a');
                                suggestionItem.href = result.url;
                                suggestionItem.className = 'block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors';
                                
                                let imageHtml = '';
                                if (result.image_url) {
                                    imageHtml = `<div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                                    <img src="${result.image_url}" alt="${result.title}" class="w-full h-full object-cover">
                                                 </div>`;
                                } else {
                                    imageHtml = `<div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-gray-400">image</span>
                                                 </div>`;
                                }
                                
                                const priceText = result.price ? new Intl.NumberFormat('fa-IR').format(result.price) + ' تومان' : 'قیمت تماس';
                                
                                suggestionItem.innerHTML = `
                                    <div class="flex items-center gap-3">
                                        ${imageHtml}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">${result.title}</p>
                                            <p class="text-xs text-primary font-bold mt-0.5">${priceText}</p>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-400 text-sm">arrow_back</span>
                                    </div>
                                `;
                                
                                suggestionsList.appendChild(suggestionItem);
                            });
                            
                            currentFocus = -1;
                        }
                        
                        function addActive(i) {
                            const items = suggestionsList.getElementsByTagName('a');
                            if (!items || items.length === 0) return false;
                            
                            removeActive(items);
                            
                            if (i >= items.length) currentFocus = 0;
                            if (i < 0) currentFocus = (items.length - 1);
                            
                            if (items[currentFocus]) {
                                items[currentFocus].classList.add('autocomplete-active', 'bg-primary/10');
                                items[currentFocus].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                            }
                        }
                        
                        function removeActive(items) {
                            for (let i = 0; i < items.length; i++) {
                                items[i].classList.remove('autocomplete-active', 'bg-primary/10');
                            }
                        }
                        
                        // Hide suggestions when clicking outside
                        document.addEventListener('click', function(e) {
                            if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                                suggestionsContainer.classList.add('hidden');
                                currentFocus = -1;
                            }
                        });
                        
                        // Show suggestions when input is focused and has value
                        searchInput.addEventListener('focus', function() {
                            if (this.value.trim().length >= 2 && suggestionsList.children.length > 0) {
                                suggestionsContainer.classList.remove('hidden');
                            }
                        });
                    });
                </script>
                
                <!-- Left Side: User Actions -->
                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    @auth
                        <!-- Notifications Dropdown -->
                        <div class="relative" id="notificationDropdown">
                            <button onclick="toggleNotifications()" class="relative p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors">
                                <span class="material-symbols-outlined">notifications</span>
                                <span id="notificationBadge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"></span>
                            </button>
                            
                            <div id="notificationMenu" class="hidden absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="font-bold text-gray-900">اعلان‌ها</h3>
                                    <a href="{{ route('user.notifications.index') }}" class="text-xs text-primary hover:text-blue-700">مشاهده همه</a>
                                </div>
                                <div id="notificationList" class="max-h-96 overflow-y-auto">
                                    <div class="px-4 py-8 text-center text-gray-400">
                                        <span class="material-symbols-outlined text-4xl mb-2 block">notifications_off</span>
                                        <p class="text-sm">در حال بارگذاری...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        let notificationsOpen = false;
                        
                        function toggleNotifications() {
                            const menu = document.getElementById('notificationMenu');
                            notificationsOpen = !notificationsOpen;
                            
                            if (notificationsOpen) {
                                menu.classList.remove('hidden');
                                loadNotifications();
                            } else {
                                menu.classList.add('hidden');
                            }
                        }
                        
                        function loadNotifications() {
                            fetch('{{ route('user.notifications.recent') }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                credentials: 'same-origin'
                            })
                            .then(res => {
                                if (!res.ok) {
                                    throw new Error(`HTTP error! status: ${res.status}`);
                                }
                                return res.json();
                            })
                            .then(data => {
                                updateNotificationBadge(data.unread_count);
                                renderNotifications(data.notifications);
                            })
                            .catch(err => {
                                console.error('Error loading notifications:', err);
                                // Show error in notification list
                                const list = document.getElementById('notificationList');
                                list.innerHTML = `
                                    <div class="px-4 py-8 text-center text-red-500">
                                        <span class="material-symbols-outlined text-4xl mb-2 block">error</span>
                                        <p class="text-sm">خطا در بارگذاری اعلان‌ها</p>
                                        <p class="text-xs mt-1">${err.message}</p>
                                    </div>
                                `;
                            });
                        }
                        
                        function updateNotificationBadge(count) {
                            const badge = document.getElementById('notificationBadge');
                            if (count > 0) {
                                badge.textContent = count;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        }
                        
                        function renderNotifications(notifications) {
                            const list = document.getElementById('notificationList');
                            
                            if (notifications.length === 0) {
                                list.innerHTML = `
                                    <div class="px-4 py-8 text-center text-gray-400">
                                        <span class="material-symbols-outlined text-4xl mb-2 block">notifications_off</span>
                                        <p class="text-sm">اعلانی وجود ندارد</p>
                                    </div>
                                `;
                                return;
                            }
                            
                            list.innerHTML = notifications.map(notif => `
                                <a href="${notif.link || '{{ route('user.notifications.index') }}'}" 
                                   class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors ${!notif.is_read ? 'bg-blue-50/30' : ''}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-${notif.color}-100">
                                            <span class="material-symbols-outlined text-lg text-${notif.color}-600">${notif.icon}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 mb-1">${notif.title}</p>
                                            <p class="text-xs text-gray-600 line-clamp-2">${notif.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${notif.time_ago}</p>
                                        </div>
                                        ${!notif.is_read ? '<span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>' : ''}
                                    </div>
                                </a>
                            `).join('');
                        }
                        
                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(event) {
                            const notifDropdown = document.getElementById('notificationDropdown');
                            
                            if (notifDropdown && !notifDropdown.contains(event.target) && notificationsOpen) {
                                toggleNotifications();
                            }
                        });
                        
                        // Load badge count on page load
                        document.addEventListener('DOMContentLoaded', function() {
                            fetch('{{ route('user.notifications.recent') }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                credentials: 'same-origin'
                            })
                            .then(res => res.json())
                            .then(data => updateNotificationBadge(data.unread_count))
                            .catch(err => console.error('Error loading badge:', err));
                        });
                        </script>
                    @endauth
                    <div class="h-8 w-[1px] bg-gray-200 mx-1 hidden sm:block"></div>
                    @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                                <span>{{ auth()->user()->name }}</span>
                                <span class="material-symbols-outlined text-[18px]" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 border border-gray-100">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">داشبورد</a>
                                <a href="{{ route('wallet.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">کیف پول</a>
                                <a href="{{ route('tickets.index') }}" class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <span>تیکت‌های پشتیبانی</span>
                                    @php
                                        $buyerUnread = \App\Models\Ticket::where(function($q){ $q->where('creator_id', auth()->id())->orWhere('recipient_id', auth()->id()); })->whereHas('messages', fn($q) => $q->where('user_id', '!=', auth()->id())->where('is_read', false))->count();
                                    @endphp
                                    @if($buyerUnread > 0)
                                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $buyerUnread }}</span>
                                    @endif
                                </a>
                                @if(auth()->user()->role === 'seller')
                                    <a href="{{ route('listings.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">ایجاد آگهی</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">خروج</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            <span>ورود / ثبت نام</span>
                        </a>
                    @endauth
                    <button class="sm:hidden p-2 text-gray-500 rounded-full">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mega Menu (Categories) -->
        @if($thCats || count($thNav) > 0)
        <div class="border-t border-[#e7ebf3] bg-white hidden md:block">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-4 h-12">
                    @if($thCats)<x-category-megamenu />@endif
                    @foreach($thNav as $navLink)
                    <a href="{{ $navLink['url'] ?? '#' }}" class="flex items-center gap-1 text-sm text-gray-600 hover:text-primary transition-colors px-2 h-full whitespace-nowrap">
                        @if(!empty($navLink['icon']))<span class="material-symbols-outlined text-base">{{ $navLink['icon'] }}</span>@endif
                        {{ $navLink['label'] ?? '' }}
                    </a>
                    @endforeach
                    @if($thCats)
                    <a class="text-red-500 hover:bg-red-50 whitespace-nowrap h-full flex items-center gap-1 px-4 rounded-lg transition-colors mr-auto" href="{{ route('listings.index', ['special' => 'discount']) }}">
                        <span class="material-symbols-outlined text-[18px]">local_offer</span>
                        <span>تخفیف‌های ویژه</span>
                    </a>
                    @endif
                </nav>
            </div>
        </div>
        @endif
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    @php
        $tfShow   = \App\Models\SiteSetting::get('theme_footer_show', '1');
        $tfBg     = \App\Models\SiteSetting::get('theme_footer_bg', '#ffffff');
        $tfColor  = \App\Models\SiteSetting::get('theme_footer_text_color', '#6b7280');
        $tfLogo   = \App\Models\SiteSetting::get('theme_footer_logo', '');
        $tfText   = \App\Models\SiteSetting::get('theme_footer_logo_text', 'پرشینآکشن');
        $tfIcon   = \App\Models\SiteSetting::get('theme_footer_logo_icon', 'gavel');
        $tfLogoSz = max(16, (int)\App\Models\SiteSetting::get('theme_footer_logo_size', '32'));
        $tfDesc   = \App\Models\SiteSetting::get('theme_footer_description', 'اولین و بزرگترین پلتفرم برگزاری مزایدات آنلاین در ایران.');
        $tfCopy   = \App\Models\SiteSetting::get('theme_footer_copyright', 'تمامی حقوق این وبسایت محفوظ است © ۱۴۰۳');
        $tfPrivTxt= \App\Models\SiteSetting::get('theme_footer_privacy_text', 'حریم خصوصی');
        $tfPrivUrl= \App\Models\SiteSetting::get('theme_footer_privacy_url', '#');
        $tfTrmTxt = \App\Models\SiteSetting::get('theme_footer_terms_text', 'شرایط استفاده');
        $tfTrmUrl = \App\Models\SiteSetting::get('theme_footer_terms_url', '#');
        $tfBtmRaw = \App\Models\SiteSetting::get('theme_footer_bottom_links', '[]');
        $tfBtmLinks = is_array($tfBtmRaw) ? $tfBtmRaw : (json_decode($tfBtmRaw, true) ?? []);
        if (empty($tfBtmLinks)) {
            $tfBtmLinks = [
                ['label' => $tfPrivTxt, 'url' => $tfPrivUrl],
                ['label' => $tfTrmTxt, 'url' => $tfTrmUrl],
            ];
        }
        $tfTrustHtml = \App\Models\SiteSetting::get('theme_footer_trust_html', '');
        $tfTrustImg  = \App\Models\SiteSetting::get('theme_footer_trust_image', '');
        $tfColsRaw= \App\Models\SiteSetting::get('theme_footer_columns', '[]');
        $tfCols   = is_array($tfColsRaw) ? $tfColsRaw : (json_decode($tfColsRaw, true) ?? []);
        $tfSocRaw = \App\Models\SiteSetting::get('theme_footer_social', '[]');
        $tfSoc    = is_array($tfSocRaw) ? $tfSocRaw : (json_decode($tfSocRaw, true) ?? []);
    @endphp
    @if($tfShow)
    <footer class="mt-auto" style="background:{{ $tfBg }};">

        {{-- Top section with gradient separator --}}
        <div class="h-px w-full" style="background:linear-gradient(to left, transparent, rgba(59,130,246,0.3), transparent);"></div>

        {{-- Main content --}}
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">

                {{-- Brand column - wider --}}
                <div class="md:col-span-4">
                    <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-3 mb-5 group">
                        @if($tfLogo)
                            <img src="{{ url('storage/'.$tfLogo) }}" style="height:{{ $tfLogoSz }}px;width:auto;object-fit:contain;" alt="{{ $tfText }}">
                        @else
                            <div style="width:{{ $tfLogoSz }}px;height:{{ $tfLogoSz }}px;" class="bg-primary/10 rounded-2xl flex items-center justify-center text-primary flex-shrink-0 group-hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined" style="font-size:{{ round($tfLogoSz*0.6) }}px;">{{ $tfIcon }}</span>
                            </div>
                        @endif
                        <span class="text-2xl font-black text-gray-900">{{ $tfText }}</span>
                    </a>
                    <p class="text-sm leading-7 mb-6 max-w-xs" style="color:{{ $tfColor }};">{{ $tfDesc }}</p>

                    {{-- Social links --}}
                    @if(count($tfSoc) > 0)
                    <div class="flex gap-2">
                        @foreach($tfSoc as $soc)
                        <a href="{{ $soc['url'] ?? '#' }}" target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-2xl border flex items-center justify-center transition-all duration-200 hover:border-primary hover:text-primary hover:bg-primary/5"
                           style="border-color:rgba(0,0,0,0.1); color:{{ $tfColor }};">
                            <span class="material-symbols-outlined text-lg">{{ $soc['icon'] ?? 'link' }}</span>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Links columns --}}
                <div class="md:col-span-{{ ($tfTrustHtml || $tfTrustImg) ? '5' : '8' }} grid grid-cols-2 {{ count($tfCols) > 0 ? 'sm:grid-cols-'.min(count($tfCols)+1, 3) : '' }} gap-8">

                    {{-- Default quick links --}}
                    <div>
                        <h5 class="font-bold text-gray-900 mb-5 text-sm">دسترسی سریع</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('listings.index') }}" class="text-sm hover:text-primary transition-colors" style="color:{{ $tfColor }};">خانه</a></li>
                            <li><a href="{{ route('listings.index') }}" class="text-sm hover:text-primary transition-colors" style="color:{{ $tfColor }};">مزایده‌ها</a></li>
                            @auth
                            <li><a href="{{ route('dashboard') }}" class="text-sm hover:text-primary transition-colors" style="color:{{ $tfColor }};">داشبورد</a></li>
                            @else
                            <li><a href="{{ route('login') }}" class="text-sm hover:text-primary transition-colors" style="color:{{ $tfColor }};">ورود / ثبت نام</a></li>
                            @endauth
                        </ul>
                    </div>

                    {{-- Custom columns --}}
                    @foreach($tfCols as $col)
                    <div>
                        <h5 class="font-bold text-gray-900 mb-5 text-sm">{{ $col['title'] ?? '' }}</h5>
                        <ul class="space-y-3">
                            @foreach($col['links'] ?? [] as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-sm hover:text-primary transition-colors" style="color:{{ $tfColor }};">{{ $link['label'] ?? '' }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>

                {{-- Trust Badge --}}
                @if($tfTrustHtml || $tfTrustImg)
                <div class="md:col-span-3">
                    <h5 class="font-bold text-gray-900 mb-5 text-sm">نماد اعتماد</h5>
                    @if($tfTrustHtml)
                        <div>{!! $tfTrustHtml !!}</div>
                    @elseif($tfTrustImg)
                        <img src="{{ url('storage/'.$tfTrustImg) }}" class="max-w-full h-auto max-h-32 object-contain" alt="نماد اعتماد">
                    @endif
                </div>
                @endif

            </div>
        </div>

        {{-- Bottom bar --}}
        <div style="border-top:1px solid rgba(0,0,0,0.06);">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs" style="color:{{ $tfColor }};">{{ $tfCopy }}</p>
                @if(count($tfBtmLinks) > 0)
                <div class="flex items-center gap-1">
                    @foreach($tfBtmLinks as $i => $bl)
                    @if($i > 0)<span class="text-gray-300 text-xs">·</span>@endif
                    <a href="{{ $bl['url'] ?? '#' }}" class="text-xs hover:text-primary transition-colors px-1" style="color:{{ $tfColor }};">{{ $bl['label'] ?? '' }}</a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </footer>
    @endif

    @livewireScripts
    <script defer src="/haraj/public/js/alpine.min.js"></script>
    @stack('scripts')
</body>
</html>
