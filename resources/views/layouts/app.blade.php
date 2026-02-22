<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Persian Auction Marketplace')</title>
    
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
        
        /* Custom select dropdown arrow */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 0.5rem center;
            background-size: 1.5em 1.5em;
            padding-left: 2.5rem !important;
        }
        
        /* For RTL, adjust arrow position */
        [dir="rtl"] select {
            background-position: left 0.5rem center;
            padding-left: 2.5rem !important;
            padding-right: 1rem !important;
        }
    </style>
    
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex flex-col">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-[#e7ebf3] shadow-sm">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                <!-- Right Side: Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('listings.index') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">gavel</span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-[#0d121b]">پرشین<span class="text-primary">آکشن</span></h1>
                    </a>
                </div>
                
                <!-- Center: Search Bar (Hidden on mobile, visible on desktop) -->
                <div class="hidden md:flex flex-1 max-w-2xl px-8">
                    <form method="GET" action="{{ route('listings.index') }}" class="relative w-full group">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input name="search" value="{{ request('search') }}" class="block w-full h-12 pr-10 pl-4 bg-[#f1f3f7] border-transparent rounded-xl focus:bg-white focus:border-primary focus:ring-primary sm:text-sm transition-all duration-200" placeholder="جستجو در بین هزاران کالا..." type="text"/>
                        <button type="submit" class="absolute inset-y-1 left-1 bg-primary text-white px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">جستجو</button>
                    </form>
                </div>
                
                <!-- Left Side: User Actions -->
                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <a href="{{ route('cart.index') }}" class="p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors relative">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        @auth
                            @if(auth()->user()->cart && auth()->user()->cart->items->count() > 0)
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        @endauth
                    </a>
                    @auth
                        <button class="p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
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
        <div class="border-t border-[#e7ebf3] bg-white hidden md:block">
            <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-4 h-12">
                    <x-category-megamenu />
                    
                    <a class="text-red-500 hover:bg-red-50 whitespace-nowrap h-full flex items-center gap-1 px-4 rounded-lg transition-colors mr-auto" href="{{ route('listings.index', ['special' => 'discount']) }}">
                        <span class="material-symbols-outlined text-[18px]">local_offer</span>
                        <span>تخفیف‌های ویژه</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
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
                        <li><a class="hover:text-primary transition-colors" href="{{ route('listings.index') }}">خانه</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('listings.index', ['type' => 'auction']) }}">مزایده‌های جاری</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('listings.index', ['type' => 'direct_sale']) }}">فروش مستقیم</a></li>
                        @auth
                            <li><a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">داشبورد</a></li>
                        @else
                            <li><a class="hover:text-primary transition-colors" href="{{ route('login') }}">ورود / ثبت نام</a></li>
                        @endauth
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

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
