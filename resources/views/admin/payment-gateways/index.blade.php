@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">credit_card</span>
                        </div>
                        مدیریت درگاه‌های پرداخت
                    </h1>
                    <p class="mt-2 text-gray-600">تنظیم و مدیریت درگاه‌های پرداخت آنلاین</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-r-4 border-green-500 rounded-xl p-4 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">کل درگاه‌ها</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $gateways->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-3xl">payments</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">درگاه‌های فعال</p>
                        <p class="text-3xl font-bold text-green-600">{{ $gateways->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-3xl">check_circle</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">نیاز به تنظیم</p>
                        <p class="text-3xl font-bold text-orange-600">
                            {{ $gateways->filter(function($g) { return empty(array_filter($g->credentials ?? [])); })->count() }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-orange-600 text-3xl">settings</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Gateways Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($gateways as $gateway)
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <!-- Gateway Header -->
                <div class="bg-gradient-to-r {{ $gateway->is_active ? 'from-green-500 to-green-600' : 'from-gray-400 to-gray-500' }} p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg">
                                @switch($gateway->name)
                                    @case('zarinpal')
                                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="#FDB913"/>
                                            <path d="M2 17L12 22L22 17V12L12 17L2 12V17Z" fill="#FDB913"/>
                                        </svg>
                                        @break
                                    @case('zibal')
                                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#00A693"/>
                                            <path d="M8 8L16 16M16 8L8 16" stroke="white" stroke-width="2"/>
                                        </svg>
                                        @break
                                    @case('vandar')
                                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                                            <rect x="4" y="4" width="16" height="16" rx="3" fill="#6366F1"/>
                                            <path d="M8 12L12 16L16 8" stroke="white" stroke-width="2"/>
                                        </svg>
                                        @break
                                    @case('payping')
                                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" fill="#FF6B6B"/>
                                            <path d="M12 8V16M8 12H16" stroke="white" stroke-width="2"/>
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $gateway->display_name }}</h3>
                                <p class="text-white/80 text-sm">{{ $gateway->name }}</p>
                            </div>
                        </div>
                        <div>
                            @if($gateway->is_active)
                                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-xl font-medium">
                                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                                    فعال
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-xl font-medium">
                                    <span class="w-2 h-2 bg-white/60 rounded-full"></span>
                                    غیرفعال
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gateway Body -->
                <div class="p-6">
                    <!-- Status Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-600 mb-1">وضعیت تنظیمات</p>
                            @php
                                $hasCredentials = !empty(array_filter($gateway->credentials ?? []));
                            @endphp
                            @if($hasCredentials)
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-green-600 text-xl">verified</span>
                                    <span class="text-sm font-medium text-green-700">تنظیم شده</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-orange-600 text-xl">warning</span>
                                    <span class="text-sm font-medium text-orange-700">نیاز به تنظیم</span>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-600 mb-1">ترتیب نمایش</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-xl">sort</span>
                                <span class="text-sm font-medium text-gray-900">{{ $gateway->sort_order }}</span>
                            </div>
                        </div>
                    </div>

                    @if($gateway->name === 'zarinpal' && $gateway->sandbox_mode)
                    <!-- Sandbox Mode Badge -->
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined text-orange-600 text-xl">science</span>
                        <div>
                            <p class="text-sm font-semibold text-orange-900">حالت Sandbox فعال است</p>
                            <p class="text-xs text-orange-700">این درگاه در حالت تست قرار دارد</p>
                        </div>
                    </div>
                    @endif

                    <!-- Required Credentials -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <p class="text-sm font-medium text-blue-900 mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">info</span>
                            اطلاعات مورد نیاز:
                        </p>
                        <div class="text-sm text-blue-800">
                            @switch($gateway->name)
                                @case('zarinpal')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 px-2 py-1 rounded">Merchant ID</span>
                                    @break
                                @case('zibal')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 px-2 py-1 rounded">Merchant ID</span>
                                    @break
                                @case('vandar')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 px-2 py-1 rounded">API Key</span>
                                    @break
                                @case('payping')
                                    <span class="inline-flex items-center gap-1 bg-blue-100 px-2 py-1 rounded">API Key</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" 
                           class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <span class="material-symbols-outlined text-xl">edit</span>
                            <span>ویرایش تنظیمات</span>
                        </a>
                        
                        <form action="{{ route('admin.payment-gateways.toggle', $gateway) }}" 
                              method="POST" 
                              class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full {{ $gateway->is_active ? 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700' : 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700' }} text-white px-6 py-3 rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                                    onclick="return confirm('آیا مطمئن هستید؟')">
                                @if($gateway->is_active)
                                    <span class="material-symbols-outlined text-xl">toggle_off</span>
                                    <span>غیرفعال کردن</span>
                                @else
                                    <span class="material-symbols-outlined text-xl">toggle_on</span>
                                    <span>فعال کردن</span>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Help Section -->
        <div class="bg-white rounded-2xl shadow-md p-8 border border-gray-100">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">help</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">راهنمای استفاده</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-600">check_circle</span>
                                نکات مهم
                            </h4>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 mt-1">•</span>
                                    <span>برای فعال‌سازی هر درگاه، ابتدا اطلاعات احراز هویت را وارد کنید</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 mt-1">•</span>
                                    <span>کاربران فقط می‌توانند از درگاه‌های فعال استفاده کنند</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-600 mt-1">•</span>
                                    <span>مالیات 9% به صورت خودکار به مبلغ شارژ اضافه می‌شود</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600">info</span>
                                درگاه‌های پشتیبانی شده
                            </h4>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                    <strong>زرین‌پال:</strong> محبوب‌ترین درگاه پرداخت ایران
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                                    <strong>زیبال:</strong> درگاه سریع و مطمئن
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                    <strong>وندار:</strong> درگاه مدرن با API قدرتمند
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <strong>پی‌پینگ:</strong> درگاه آسان و کاربرپسند
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-sm text-blue-900 flex items-start gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-xl flex-shrink-0">lightbulb</span>
                            <span>
                                <strong>نکته:</strong> برای دریافت اطلاعات احراز هویت، به پنل مدیریت هر درگاه مراجعه کنید و API Key یا Merchant ID خود را کپی کنید.
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
