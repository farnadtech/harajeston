<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'داشبورد مدیریت') - Persian Auction Marketplace</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
                        "2xl": "1.5rem",
                    },
                },
            },
        }
    </script>
    
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
    
    @livewireStyles
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-l border-gray-200 hidden lg:flex flex-col h-screen fixed right-0 top-0 z-30">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-100">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h1 class="text-xl font-black tracking-tight text-[#0d121b]">پرشین<span class="text-primary">آدمین</span></h1>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.dashboard') ? 'bold' : 'medium' }} transition-colors" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>داشبورد</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.listings.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.listings.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.listings.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">gavel</span>
                <span>مدیریت مزایده‌ها</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.categories.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.categories.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">category</span>
                <span>دسته‌بندی‌ها</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="{{ route('admin.users.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">group</span>
                <span>کاربران</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:text-primary hover:bg-gray-50 rounded-xl font-medium transition-colors group" href="#">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">storefront</span>
                <span>فروشندگان</span>
                @if(isset($pendingSellers) && $pendingSellers->count() > 0)
                    <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full mr-auto">@persian($pendingSellers->count())</span>
                @endif
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.orders.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.orders.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">shopping_bag</span>
                <span>سفارشات</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.financial-reports.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.financial-reports.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.financial-reports.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">payments</span>
                <span>گزارشات مالی</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.shipping-methods.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.shipping-methods.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.shipping-methods.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">local_shipping</span>
                <span>روش‌های ارسال</span>
            </a>
            
            <a class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings.*') ? 'text-primary bg-primary/5' : 'text-gray-600 hover:text-primary hover:bg-gray-50' }} rounded-xl font-{{ request()->routeIs('admin.settings.*') ? 'bold' : 'medium' }} transition-colors group" href="{{ route('admin.settings.index') }}">
                <span class="material-symbols-outlined group-hover:text-primary transition-colors">settings</span>
                <span>تنظیمات سایت</span>
            </a>
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
            <div class="flex items-center gap-4 lg:hidden">
                <button class="p-2 -mr-2 text-gray-500 hover:bg-gray-100 rounded-lg" onclick="toggleSidebar()">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="text-lg font-bold text-gray-900">@yield('page-title', 'داشبورد')</h1>
            </div>
            
            <div class="hidden lg:block">
                <h2 class="text-xl font-bold text-gray-800">@yield('header-title', 'خوش آمدید، ادمین عزیز 👋')</h2>
                <p class="text-sm text-gray-500">@yield('header-subtitle', 'گزارش کلی وضعیت بازار امروز')</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <input class="w-64 h-10 pr-10 pl-4 bg-gray-50 border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-primary transition-all" placeholder="جستجو..." type="text"/>
                    <span class="material-symbols-outlined absolute right-3 top-2.5 text-gray-400 text-[20px]">search</span>
                </div>
                
                <button class="p-2 text-gray-500 hover:text-primary hover:bg-primary/5 rounded-full transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
                
                <div class="flex items-center gap-3 pr-4 border-r border-gray-200 mr-2">
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">مدیر سیستم</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden border-2 border-white shadow-sm flex items-center justify-center">
                        <span class="material-symbols-outlined text-gray-500">person</span>
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

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
    
    <script>
        function toggleSidebar() {
            // Mobile sidebar toggle functionality
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        }
        
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
        
        // Global error handler for fetch
        window.handleFetchError = function(error, defaultMessage = 'خطا در ارتباط با سرور') {
            console.error('Error:', error);
            showNotification(defaultMessage, 'error');
        };
    </script>
    
    @stack('scripts')
</body>
</html>
