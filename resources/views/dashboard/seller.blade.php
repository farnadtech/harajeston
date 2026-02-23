<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>داشبورد فروشنده - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
            <a class="flex items-center gap-3 px-4 py-3 text-primary bg-primary/5 rounded-xl font-bold transition-colors" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>داشبورد</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('listings.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">inventory_2</span>
                <span>مزایده‌های من</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('listings.create') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">add_circle</span>
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
                <h2 class="text-xl font-bold text-gray-800">خوش آمدید، {{ auth()->user()->store->store_name ?? auth()->user()->name }} 👋</h2>
                <p class="text-sm text-gray-500">خلاصه وضعیت فروشگاه شما امروز</p>
            </div>
            
            <div class="flex items-center gap-4">
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

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 space-y-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">درآمد کل</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1">
                            @persian(number_format($stats['total_sales'] ?? 0))
                            <span class="text-xs font-normal text-gray-400">تومان</span>
                        </h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-50 text-secondary flex items-center justify-center">
                        <span class="material-symbols-outlined">gavel</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">مزایده‌های فعال</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['active_auctions'] ?? 0)</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">pending</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">در انتظار تایید</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['pending_listings'] ?? 0)</h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                        <span class="material-symbols-outlined">check_circle</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">تکمیل شده</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1">@persian($stats['completed_auctions'] ?? 0)</h3>
                    </div>
                </div>
            </div>

            <!-- Active Listings Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">مزایده‌های فعال من</h3>
                        <p class="text-sm text-gray-500 mt-1">لیست مزایده‌های در حال برگزاری فروشگاه شما</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('listings.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">
                            افزودن مزایده
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                <th class="px-6 py-4">محصول</th>
                                <th class="px-6 py-4">قیمت فعلی</th>
                                <th class="px-6 py-4">زمان باقی‌مانده</th>
                                <th class="px-6 py-4 text-center">وضعیت</th>
                                <th class="px-6 py-4 text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($activeListings ?? [] as $listing)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($listing->images->count() > 0)
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                                    <img alt="{{ $listing->title }}" class="w-full h-full object-cover" src="{{ Storage::url($listing->images->first()->image_path) }}"/>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                                                    <span class="material-symbols-outlined text-gray-400">image</span>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $listing->title }}</p>
                                                <p class="text-xs text-gray-500">شروع: @persian(number_format($listing->starting_price)) تومان</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">
                                            @persian(number_format($listing->current_price))
                                            <span class="text-xs font-normal text-gray-500">تومان</span>
                                        </div>
                                        @if($listing->current_price > $listing->starting_price)
                                            <div class="text-xs text-green-500 mt-0.5">
                                                +@persian(number_format((($listing->current_price - $listing->starting_price) / $listing->starting_price) * 100, 0))٪
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($listing->ends_at > now())
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ $listing->ends_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-sm font-medium text-red-600">پایان یافته</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($listing->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                در جریان
                                            </span>
                                        @elseif($listing->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                در انتظار تایید
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $listing->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('listings.show', $listing) }}" class="p-1.5 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="مشاهده">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="material-symbols-outlined text-5xl text-gray-300">inventory_2</span>
                                            <p class="text-gray-500">هیچ مزایده فعالی وجود ندارد</p>
                                            <a href="{{ route('listings.create') }}" class="mt-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                                                ایجاد اولین مزایده
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            @if(isset($recentOrders) && count($recentOrders) > 0)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">سفارشات اخیر</h3>
                    <p class="text-sm text-gray-500 mt-1">آخرین سفارشات دریافت شده</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                <th class="px-6 py-4">شماره سفارش</th>
                                <th class="px-6 py-4">خریدار</th>
                                <th class="px-6 py-4">مبلغ</th>
                                <th class="px-6 py-4">وضعیت</th>
                                <th class="px-6 py-4">تاریخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-sm">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $order->buyer->name }}</td>
                                    <td class="px-6 py-4 text-sm font-bold">@persian(number_format($order->total)) تومان</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </main>
</body>
</html>
