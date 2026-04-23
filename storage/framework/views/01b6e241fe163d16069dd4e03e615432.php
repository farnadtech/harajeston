<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Persian Auction Marketplace'); ?></title>
    
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
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex flex-col">
    <?php
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
    ?>
    <!-- Sticky Header -->
    <header class="<?php echo e($thSticky ? 'sticky top-0' : ''); ?> z-50 border-b border-[#e7ebf3] shadow-sm" style="background:<?php echo e($thBg); ?>;">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4" style="height:<?php echo e($thHeight); ?>px;">
                <!-- Right Side: Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="<?php echo e(route('listings.index')); ?>" class="flex items-center gap-3">
                        <?php if($thLogo): ?>
                            <img src="<?php echo e(url('storage/'.$thLogo)); ?>" style="height:<?php echo e($thLogoSz); ?>px;width:auto;object-fit:contain;" alt="<?php echo e($thText); ?>">
                        <?php else: ?>
                            <div style="width:<?php echo e($thLogoSz); ?>px;height:<?php echo e($thLogoSz); ?>px;" class="bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined" style="font-size:<?php echo e(round($thLogoSz*0.6)); ?>px;"><?php echo e($thIcon); ?></span>
                            </div>
                        <?php endif; ?>
                        <h1 class="text-2xl font-black tracking-tight text-[#0d121b]"><?php echo e($thText); ?></h1>
                    </a>
                </div>
                
                <!-- Center: Search Bar (Hidden on mobile, visible on desktop) -->
                <?php if($thSearch): ?>
                <div class="hidden md:flex flex-1 max-w-2xl px-8">
                    <div class="relative w-full group">
                        <form method="GET" action="<?php echo e(route('listings.index')); ?>" class="relative w-full" id="searchForm">
                            <input name="search" value="<?php echo e(request('search')); ?>" 
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
                <?php endif; ?>
                
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
                            const apiUrl = '<?php echo e(url("/api/listings/search")); ?>?q=' + encodeURIComponent(query);
                            
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
                    <?php if(auth()->guard()->check()): ?>
                        <!-- Notifications Dropdown -->
                        <div class="relative" id="notificationDropdown">
                            <button onclick="toggleNotifications()" class="relative p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors">
                                <span class="material-symbols-outlined">notifications</span>
                                <span id="notificationBadge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"></span>
                            </button>
                            
                            <div id="notificationMenu" class="hidden absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="font-bold text-gray-900">اعلان‌ها</h3>
                                    <a href="<?php echo e(route('user.notifications.index')); ?>" class="text-xs text-primary hover:text-blue-700">مشاهده همه</a>
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
                            fetch('<?php echo e(route('user.notifications.recent')); ?>', {
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
                                <a href="${notif.link || '<?php echo e(route('user.notifications.index')); ?>'}" 
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
                            fetch('<?php echo e(route('user.notifications.recent')); ?>', {
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
                    <?php endif; ?>
                    <div class="h-8 w-[1px] bg-gray-200 mx-1 hidden sm:block"></div>
                    <?php if(auth()->guard()->check()): ?>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                                <span><?php echo e(auth()->user()->name); ?></span>
                                <span class="material-symbols-outlined text-[18px]" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 border border-gray-100">
                                <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">داشبورد</a>
                                <a href="<?php echo e(route('wallet.show')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">کیف پول</a>
                                <a href="<?php echo e(route('tickets.index')); ?>" class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <span>تیکت‌های پشتیبانی</span>
                                    <?php
                                        $buyerUnread = \App\Models\Ticket::where(function($q){ $q->where('creator_id', auth()->id())->orWhere('recipient_id', auth()->id()); })->whereHas('messages', fn($q) => $q->where('user_id', '!=', auth()->id())->where('is_read', false))->count();
                                    ?>
                                    <?php if($buyerUnread > 0): ?>
                                        <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full"><?php echo e($buyerUnread); ?></span>
                                    <?php endif; ?>
                                </a>
                                <?php if(auth()->user()->role === 'seller'): ?>
                                    <a href="<?php echo e(route('listings.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">ایجاد آگهی</a>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-right px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">خروج</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-xl transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            <span>ورود / ثبت نام</span>
                        </a>
                    <?php endif; ?>
                    <button class="sm:hidden p-2 text-gray-500 rounded-full">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mega Menu (Categories) -->
        <?php if($thCats || count($thNav) > 0): ?>
        <div class="border-t border-[#e7ebf3] bg-white hidden md:block">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-4 h-12">
                    <?php if($thCats): ?><?php if (isset($component)) { $__componentOriginalae6f97678d43a8ef9f188b0c80aee65c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae6f97678d43a8ef9f188b0c80aee65c = $attributes; } ?>
<?php $component = App\View\Components\CategoryMegamenu::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('category-megamenu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\CategoryMegamenu::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae6f97678d43a8ef9f188b0c80aee65c)): ?>
<?php $attributes = $__attributesOriginalae6f97678d43a8ef9f188b0c80aee65c; ?>
<?php unset($__attributesOriginalae6f97678d43a8ef9f188b0c80aee65c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae6f97678d43a8ef9f188b0c80aee65c)): ?>
<?php $component = $__componentOriginalae6f97678d43a8ef9f188b0c80aee65c; ?>
<?php unset($__componentOriginalae6f97678d43a8ef9f188b0c80aee65c); ?>
<?php endif; ?><?php endif; ?>
                    <?php $__currentLoopData = $thNav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($navLink['url'] ?? '#'); ?>" class="flex items-center gap-1 text-sm text-gray-600 hover:text-primary transition-colors px-2 h-full whitespace-nowrap">
                        <?php if(!empty($navLink['icon'])): ?><span class="material-symbols-outlined text-base"><?php echo e($navLink['icon']); ?></span><?php endif; ?>
                        <?php echo e($navLink['label'] ?? ''); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($thCats): ?>
                    <a class="text-red-500 hover:bg-red-50 whitespace-nowrap h-full flex items-center gap-1 px-4 rounded-lg transition-colors mr-auto" href="<?php echo e(route('listings.index', ['special' => 'discount'])); ?>">
                        <span class="material-symbols-outlined text-[18px]">local_offer</span>
                        <span>تخفیف‌های ویژه</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </header>

    <main class="flex-grow">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <?php
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
    ?>
    <?php if($tfShow): ?>
    <footer class="mt-auto" style="background:<?php echo e($tfBg); ?>;">

        
        <div class="h-px w-full" style="background:linear-gradient(to left, transparent, rgba(59,130,246,0.3), transparent);"></div>

        
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">

                
                <div class="md:col-span-4">
                    <a href="<?php echo e(route('listings.index')); ?>" class="inline-flex items-center gap-3 mb-5 group">
                        <?php if($tfLogo): ?>
                            <img src="<?php echo e(url('storage/'.$tfLogo)); ?>" style="height:<?php echo e($tfLogoSz); ?>px;width:auto;object-fit:contain;" alt="<?php echo e($tfText); ?>">
                        <?php else: ?>
                            <div style="width:<?php echo e($tfLogoSz); ?>px;height:<?php echo e($tfLogoSz); ?>px;" class="bg-primary/10 rounded-2xl flex items-center justify-center text-primary flex-shrink-0 group-hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined" style="font-size:<?php echo e(round($tfLogoSz*0.6)); ?>px;"><?php echo e($tfIcon); ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="text-2xl font-black text-gray-900"><?php echo e($tfText); ?></span>
                    </a>
                    <p class="text-sm leading-7 mb-6 max-w-xs" style="color:<?php echo e($tfColor); ?>;"><?php echo e($tfDesc); ?></p>

                    
                    <?php if(count($tfSoc) > 0): ?>
                    <div class="flex gap-2">
                        <?php $__currentLoopData = $tfSoc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($soc['url'] ?? '#'); ?>" target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-2xl border flex items-center justify-center transition-all duration-200 hover:border-primary hover:text-primary hover:bg-primary/5"
                           style="border-color:rgba(0,0,0,0.1); color:<?php echo e($tfColor); ?>;">
                            <span class="material-symbols-outlined text-lg"><?php echo e($soc['icon'] ?? 'link'); ?></span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="md:col-span-<?php echo e(($tfTrustHtml || $tfTrustImg) ? '5' : '8'); ?> grid grid-cols-2 <?php echo e(count($tfCols) > 0 ? 'sm:grid-cols-'.min(count($tfCols)+1, 3) : ''); ?> gap-8">

                    
                    <div>
                        <h5 class="font-bold text-gray-900 mb-5 text-sm">دسترسی سریع</h5>
                        <ul class="space-y-3">
                            <li><a href="<?php echo e(route('listings.index')); ?>" class="text-sm hover:text-primary transition-colors" style="color:<?php echo e($tfColor); ?>;">خانه</a></li>
                            <li><a href="<?php echo e(route('listings.index')); ?>" class="text-sm hover:text-primary transition-colors" style="color:<?php echo e($tfColor); ?>;">مزایده‌ها</a></li>
                            <?php if(auth()->guard()->check()): ?>
                            <li><a href="<?php echo e(route('dashboard')); ?>" class="text-sm hover:text-primary transition-colors" style="color:<?php echo e($tfColor); ?>;">داشبورد</a></li>
                            <?php else: ?>
                            <li><a href="<?php echo e(route('login')); ?>" class="text-sm hover:text-primary transition-colors" style="color:<?php echo e($tfColor); ?>;">ورود / ثبت نام</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    
                    <?php $__currentLoopData = $tfCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <h5 class="font-bold text-gray-900 mb-5 text-sm"><?php echo e($col['title'] ?? ''); ?></h5>
                        <ul class="space-y-3">
                            <?php $__currentLoopData = $col['links'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><a href="<?php echo e($link['url'] ?? '#'); ?>" class="text-sm hover:text-primary transition-colors" style="color:<?php echo e($tfColor); ?>;"><?php echo e($link['label'] ?? ''); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php if($tfTrustHtml || $tfTrustImg): ?>
                <div class="md:col-span-3">
                    <h5 class="font-bold text-gray-900 mb-5 text-sm">نماد اعتماد</h5>
                    <?php if($tfTrustHtml): ?>
                        <div><?php echo $tfTrustHtml; ?></div>
                    <?php elseif($tfTrustImg): ?>
                        <img src="<?php echo e(url('storage/'.$tfTrustImg)); ?>" class="max-w-full h-auto max-h-32 object-contain" alt="نماد اعتماد">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

        
        <div style="border-top:1px solid rgba(0,0,0,0.06);">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs" style="color:<?php echo e($tfColor); ?>;"><?php echo e($tfCopy); ?></p>
                <?php if(count($tfBtmLinks) > 0): ?>
                <div class="flex items-center gap-1">
                    <?php $__currentLoopData = $tfBtmLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $bl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($i > 0): ?><span class="text-gray-300 text-xs">·</span><?php endif; ?>
                    <a href="<?php echo e($bl['url'] ?? '#'); ?>" class="text-xs hover:text-primary transition-colors px-1" style="color:<?php echo e($tfColor); ?>;"><?php echo e($bl['label'] ?? ''); ?></a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script defer src="/haraj/public/js/alpine.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\layouts\app.blade.php ENDPATH**/ ?>