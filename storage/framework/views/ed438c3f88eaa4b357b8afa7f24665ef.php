<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo $__env->yieldContent('title', 'داشبورد فروشنده'); ?> - <?php echo e(config('app.name')); ?></title>
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
    <?php echo $__env->yieldPushContent('styles'); ?>
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
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('dashboard') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('dashboard')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('dashboard') ? '' : 'group-hover:text-primary transition-colors'); ?>">dashboard</span>
                <span>داشبورد</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('my-listings') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('my-listings')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('my-listings') ? '' : 'group-hover:text-primary transition-colors'); ?>">inventory_2</span>
                <span>مزایده‌های من</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('listings.create') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('listings.create')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('listings.create') ? '' : 'group-hover:text-primary transition-colors'); ?>">add_circle</span>
                <span>افزودن مزایده جدید</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('wallet.show') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('wallet.show')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('wallet.show') ? '' : 'group-hover:text-primary transition-colors'); ?>">account_balance_wallet</span>
                <span>کیف پول مالی</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('orders.*') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('orders.index')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('orders.*') ? '' : 'group-hover:text-primary transition-colors'); ?>">shopping_bag</span>
                <span>سفارشات</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('stores.edit') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('stores.edit')); ?>">
                <span class="material-symbols-outlined <?php echo e(request()->routeIs('stores.edit') ? '' : 'group-hover:text-primary transition-colors'); ?>">store</span>
                <span>تنظیمات فروشگاه</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-100">
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
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
                <h2 class="text-xl font-bold text-gray-800"><?php echo $__env->yieldContent('page-title', 'داشبورد'); ?></h2>
                <p class="text-sm text-gray-500"><?php echo $__env->yieldContent('page-subtitle', 'خوش آمدید'); ?></p>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Notification Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <?php if(auth()->user()->notifications()->where('is_read', false)->count() > 0): ?>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                         style="display: none;">
                        <div class="p-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-gray-800">اعلان‌ها</h3>
                                <?php if(auth()->user()->notifications()->where('is_read', false)->count() > 0): ?>
                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-medium">
                                        <?php echo app(\App\Services\PersianNumberService::class)->toPersian(auth()->user()->notifications()->where('is_read', false)->count()); ?> جدید
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto">
                            <?php $__empty_1 = true; $__currentLoopData = auth()->user()->notifications()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="block p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-blue-600 text-sm"><?php echo e($notification->icon ?? 'notifications'); ?></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 <?php echo e($notification->is_read ? '' : 'font-bold'); ?>">
                                                <?php echo e($notification->message ?? 'اعلان جدید'); ?>

                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <?php echo e($notification->created_at->diffForHumans()); ?>

                                            </p>
                                        </div>
                                        <?php if(!$notification->is_read): ?>
                                            <div class="w-2 h-2 bg-blue-500 rounded-full shrink-0"></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="p-8 text-center">
                                    <span class="material-symbols-outlined text-gray-300 text-4xl">notifications_off</span>
                                    <p class="text-gray-500 text-sm mt-2">اعلانی وجود ندارد</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(auth()->user()->notifications()->count() > 5): ?>
                            <div class="p-3 border-t border-gray-200">
                                <button @click="open = false" class="block w-full text-center text-sm text-primary hover:text-blue-700 font-medium">
                                    بستن
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 pr-4 border-r border-gray-200 mr-2">
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-bold text-gray-900"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e(auth()->user()->role === 'seller' ? 'فروشنده' : 'کاربر'); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Alpine.js for dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/layouts/seller.blade.php ENDPATH**/ ?>