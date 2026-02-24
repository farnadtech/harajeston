<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Persian Auction Marketplace'); ?></title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&amp;family=Vazirmatn:wght@100..900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
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
                        "display": ["Vazirmatn", "Manrope", "sans-serif"],
                        "body": ["Vazirmatn", "Manrope", "sans-serif"],
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    
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
    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-[#e7ebf3] shadow-sm">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                <!-- Right Side: Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="<?php echo e(route('listings.index')); ?>" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">gavel</span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-[#0d121b]">پرشین<span class="text-primary">آکشن</span></h1>
                    </a>
                </div>
                
                <!-- Center: Search Bar (Hidden on mobile, visible on desktop) -->
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
        <div class="border-t border-[#e7ebf3] bg-white hidden md:block">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-4 h-12">
                    <?php if (isset($component)) { $__componentOriginalae6f97678d43a8ef9f188b0c80aee65c = $component; } ?>
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
<?php endif; ?>
                    
                    <a class="text-red-500 hover:bg-red-50 whitespace-nowrap h-full flex items-center gap-1 px-4 rounded-lg transition-colors mr-auto" href="<?php echo e(route('listings.index', ['special' => 'discount'])); ?>">
                        <span class="material-symbols-outlined text-[18px]">local_offer</span>
                        <span>تخفیف‌های ویژه</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#e7ebf3] pt-12 pb-8 mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">gavel</span>
                        </div>
                        <h2 class="text-xl font-black text-[#0d121b]">پرشین<span class="text-primary">آکشن</span></h2>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed mb-4">اولین و بزرگترین پلتفرم برگزاری مزایدات آنلاین در ایران. با ما تجربه‌ای امن و هیجان‌انگیز از خرید و فروش کالاهای خاص داشته باشید.</p>
                    <div class="flex gap-3">
                        <a class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors" href="#">
                            <i class="material-symbols-outlined text-sm">alternate_email</i>
                        </a>
                        <a class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white transition-colors" href="#">
                            <i class="material-symbols-outlined text-sm">public</i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">دسترسی سریع</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a class="hover:text-primary transition-colors" href="<?php echo e(route('listings.index')); ?>">خانه</a></li>
                        <li><a class="hover:text-primary transition-colors" href="<?php echo e(route('listings.index', ['type' => 'auction'])); ?>">مزایده‌های جاری</a></li>
                        <li><a class="hover:text-primary transition-colors" href="<?php echo e(route('listings.index', ['type' => 'direct_sale'])); ?>">فروش مستقیم</a></li>
                        <?php if(auth()->guard()->check()): ?>
                            <li><a class="hover:text-primary transition-colors" href="<?php echo e(route('dashboard')); ?>">داشبورد</a></li>
                        <?php else: ?>
                            <li><a class="hover:text-primary transition-colors" href="<?php echo e(route('login')); ?>">ورود / ثبت نام</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">راهنمای مشتریان</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a class="hover:text-primary transition-colors" href="#">قوانین و مقررات</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">رویه‌های ارسال سفارش</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">شیوه‌های پرداخت</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">پاسخ به پرسش‌های متداول</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">نماد اعتماد</h3>
                    <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                        <p class="text-xs text-gray-400 mb-2">محل قرارگیری نماد اعتماد الکترونیک</p>
                        <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400">verified</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-6 text-center text-sm text-gray-500 flex flex-col md:flex-row justify-between items-center gap-4">
                <p>تمامی حقوق این وبسایت محفوظ است © ۱۴۰۳</p>
                <div class="flex gap-6">
                    <a class="hover:text-gray-900" href="#">حریم خصوصی</a>
                    <a class="hover:text-gray-900" href="#">شرایط استفاده</a>
                </div>
            </div>
        </div>
    </footer>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/layouts/app.blade.php ENDPATH**/ ?>