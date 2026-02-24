<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'داشبورد'); ?> - <?php echo e(config('app.name')); ?></title>
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
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 0.5rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-width: 320px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 50;
        }
        .dropdown.active .dropdown-menu {
            display: block;
        }
    </style>
    <?php echo e($styles ?? ''); ?>

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
            <?php if(auth()->user()->canSell()): ?>
                
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
                <?php if(auth()->user()->store): ?>
                <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('stores.edit') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('stores.edit')); ?>">
                    <span class="material-symbols-outlined <?php echo e(request()->routeIs('stores.edit') ? '' : 'group-hover:text-primary transition-colors'); ?>">store</span>
                    <span>تنظیمات فروشگاه</span>
                </a>
                <?php endif; ?>
                
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="<?php echo e(route('home')); ?>">
                        <span class="material-symbols-outlined group-hover:text-primary transition-colors">home</span>
                        <span>بازگشت به صفحه اصلی</span>
                    </a>
                </div>
            <?php else: ?>
                
                <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('dashboard') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors" href="<?php echo e(route('dashboard')); ?>">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>داشبورد</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('my-bids') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('my-bids')); ?>">
                    <span class="material-symbols-outlined group-hover:text-primary transition-colors">gavel</span>
                    <span>پیشنهادات من</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('orders.index') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('orders.index')); ?>">
                    <span class="material-symbols-outlined group-hover:text-primary transition-colors">shopping_bag</span>
                    <span>سفارشات من</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 <?php echo e(request()->routeIs('wallet.show') ? 'text-primary bg-primary/5 font-bold' : 'text-gray-600 hover:text-primary hover:bg-gray-50 font-medium'); ?> rounded-xl transition-colors group" href="<?php echo e(route('wallet.show')); ?>">
                    <span class="material-symbols-outlined group-hover:text-primary transition-colors">account_balance_wallet</span>
                    <span>کیف پول</span>
                </a>
                
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="<?php echo e(route('home')); ?>">
                        <span class="material-symbols-outlined group-hover:text-primary transition-colors">home</span>
                        <span>بازگشت به صفحه اصلی</span>
                    </a>
                </div>
                
                <?php if(auth()->user()->seller_status === 'none'): ?>
                <div class="pt-4 border-t border-gray-200">
                    <a class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all shadow-md" href="<?php echo e(route('seller-request.create')); ?>">
                        <span class="material-symbols-outlined">store</span>
                        <span>فروشنده شوید</span>
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
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
                <h2 class="text-xl font-bold text-gray-800"><?php echo e($pageTitle ?? 'داشبورد'); ?></h2>
                <p class="text-sm text-gray-500">خوش آمدید، <?php echo e(auth()->user()->name); ?> عزیز</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="dropdown" id="notificationsDropdown">
                    <button onclick="toggleDropdown('notificationsDropdown')" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <?php
                            $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                            $unreadCount = $unreadNotifications->count();
                        ?>
                        <?php if($unreadCount > 0): ?>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        <?php endif; ?>
                    </button>
                    
                    <div class="dropdown-menu">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-900">اعلان‌ها</h3>
                            <?php if($unreadCount > 0): ?>
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full"><?php echo e(\App\Services\PersianNumberService::convertToPersian($unreadCount)); ?> جدید</span>
                            <?php endif; ?>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <?php
                                $allNotifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->limit(10)->get();
                            ?>
                            <?php $__empty_1 = true; $__currentLoopData = $allNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a href="<?php echo e($notification->link ?? '#'); ?>" class="block p-4 hover:bg-gray-50 transition-colors border-b border-gray-50 <?php echo e(!$notification->is_read ? 'bg-blue-50' : ''); ?>">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-<?php echo e($notification->color); ?>-100 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-<?php echo e($notification->color); ?>-600 text-xl"><?php echo e($notification->icon ?? 'notifications'); ?></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900"><?php echo e($notification->title); ?></p>
                                            <p class="text-xs text-gray-500 mt-1"><?php echo e($notification->message); ?></p>
                                            <p class="text-xs text-gray-400 mt-1"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="p-8 text-center">
                                    <span class="material-symbols-outlined text-gray-300 text-4xl">notifications_off</span>
                                    <p class="text-gray-500 text-sm mt-2">اعلانی وجود ندارد</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if($allNotifications->count() > 0): ?>
                        <div class="p-3 border-t border-gray-100">
                            <a href="<?php echo e(route('user.notifications.index')); ?>" class="block text-center text-sm text-primary hover:text-blue-700 font-medium">
                                مشاهده همه اعلان‌ها
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 pr-4 border-r border-gray-200 mr-2">
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-bold text-gray-900"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e(auth()->user()->canSell() ? 'فروشنده' : 'خریدار'); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <?php echo e($slot); ?>

        </div>
    </main>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('active');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d.id !== id) d.classList.remove('active');
            });
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
            }
        });
    </script>
    <?php echo e($scripts ?? ''); ?>

</body>
</html>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/components/dashboard-layout.blade.php ENDPATH**/ ?>