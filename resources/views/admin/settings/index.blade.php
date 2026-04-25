@extends('layouts.admin')

@section('title', 'تنظیمات سایت')
@section('page-title', 'تنظیمات سایت')
@section('header-title', 'تنظیمات سایت')
@section('header-subtitle', 'مدیریت تمام تنظیمات سیستم')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex overflow-x-auto border-b border-gray-100" id="settingsTabs">
            @php
                $tabs = [
                    ['id' => 'auction',  'icon' => 'gavel',        'label' => 'مزایده'],
                    ['id' => 'users',    'icon' => 'group',         'label' => 'کاربران'],
                    ['id' => 'listings', 'icon' => 'sell',          'label' => 'آگهی‌ها'],
                    ['id' => 'finance',  'icon' => 'account_balance_wallet', 'label' => 'مالی'],
                ];
            @endphp
            @foreach($tabs as $tab)
                <button onclick="switchTab('{{ $tab['id'] }}')"
                        id="tab-btn-{{ $tab['id'] }}"
                        class="tab-btn flex items-center gap-2 px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors
                               {{ $loop->first ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                    <span class="material-symbols-outlined text-[18px]">{{ $tab['icon'] }}</span>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        {{-- ===== TAB: مزایده ===== --}}
        <div id="tab-auction" class="tab-panel p-6 space-y-6">

            {{-- سپرده --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">savings</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">سپرده شرکت در مزایده</h3>
                        <p class="text-xs text-gray-500">نحوه محاسبه مبلغ سپرده برای شرکت در حراجی‌ها</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.deposit.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع محاسبه</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="deposit_type" value="fixed" {{ $depositSettings['type'] === 'fixed' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">مبلغ ثابت</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="deposit_type" value="percentage" {{ $depositSettings['type'] === 'percentage' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">درصد از قیمت پایه</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ ثابت (تومان)</label>
                            <input type="number" name="deposit_fixed_amount" value="{{ $depositSettings['fixed_amount'] }}" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">درصد سپرده (%)</label>
                            <input type="number" name="deposit_percentage" value="{{ $depositSettings['percentage'] }}" min="0" max="100" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- مدت زمان حراجی --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">timer</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">مدت زمان حراجی</h3>
                        <p class="text-xs text-gray-500">تعیین مدت زمان ثابت برای همه حراجی‌ها</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.auction-duration.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200">
                            <input type="checkbox" name="force_auction_duration" value="1" id="force_auction_duration"
                                   {{ $auctionDurationSettings['force_duration'] ?? false ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-primary rounded" onchange="toggleDurationFields()">
                            <div>
                                <label for="force_auction_duration" class="text-sm font-medium text-gray-800 cursor-pointer">اجبار مدت زمان ثابت</label>
                                <p class="text-xs text-gray-500 mt-1">فروشندگان نمی‌توانند زمان پایان را خودشان انتخاب کنند</p>
                            </div>
                        </div>
                        <div id="duration-fields" class="{{ ($auctionDurationSettings['force_duration'] ?? false) ? '' : 'opacity-50 pointer-events-none' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">مدت زمان (روز)</label>
                            <input type="number" name="auction_duration_days" value="{{ $auctionDurationSettings['duration_days'] ?? 7 }}" min="1" max="365"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- زمان‌بندی پرداخت --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">schedule</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">مهلت تکمیل پرداخت</h3>
                        <p class="text-xs text-gray-500">برنده مزایده باید ظرف این مدت پرداخت کند</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.auction-release.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 mb-2">مهلت پرداخت (ساعت)</label>
                        <input type="number" name="auction_finalize_deadline_hours" value="{{ $auctionReleaseSettings['finalize_deadline_hours'] ?? 24 }}" min="1" max="168"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">در صورت عدم پرداخت، سپرده ضبط می‌شود</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- کمیسیون --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">percent</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">کمیسیون سایت</h3>
                        <p class="text-xs text-gray-500">نحوه محاسبه و دریافت کمیسیون از معاملات</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.commission.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع محاسبه کمیسیون</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_type" value="fixed" {{ $commissionSettings['type'] === 'fixed' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">مبلغ ثابت</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_type" value="percentage" {{ $commissionSettings['type'] === 'percentage' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">درصد از قیمت نهایی</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_type" value="category" {{ $commissionSettings['type'] === 'category' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">بر اساس دسته‌بندی</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ ثابت (تومان)</label>
                                <input type="number" name="commission_fixed_amount" value="{{ $commissionSettings['fixed_amount'] }}" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">درصد کمیسیون (%)</label>
                                <input type="number" name="commission_percentage" value="{{ $commissionSettings['percentage'] }}" min="0" max="100" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">پرداخت‌کننده کمیسیون</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_payer" value="buyer" {{ $commissionSettings['payer'] === 'buyer' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">خریدار</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_payer" value="seller" {{ $commissionSettings['payer'] === 'seller' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">فروشنده</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="commission_payer" value="both" {{ $commissionSettings['payer'] === 'both' ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">هر دو (تقسیم)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">درصد سهم خریدار (%) — فقط حالت "هر دو"</label>
                            <input type="number" name="commission_split_percentage" value="{{ $commissionSettings['split_percentage'] }}" min="0" max="100" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">مثال: 60 یعنی 60% از خریدار و 40% از فروشنده</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        @if($commissionSettings['type'] === 'category')
                            <a href="{{ route('admin.category-commissions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                مدیریت کمیسیون دسته‌بندی‌ها
                            </a>
                        @else
                            <span></span>
                        @endif
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- بازندگان مزایده --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">money_off</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">کارمزد بازندگان مزایده</h3>
                        <p class="text-xs text-gray-500">کسر درصدی از سپرده کاربرانی که برنده نشدند</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.loser-fee.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200">
                            <input type="checkbox" name="loser_fee_enabled" value="1" id="loser_fee_enabled"
                                   {{ ($loserFeeSettings['enabled'] ?? false) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-primary rounded" onchange="toggleLoserFeeFields()">
                            <div>
                                <label for="loser_fee_enabled" class="text-sm font-medium text-gray-800 cursor-pointer">فعال‌سازی کارمزد بازندگان</label>
                                <p class="text-xs text-gray-500 mt-1">درصدی از سپرده بازندگان کسر می‌شود</p>
                            </div>
                        </div>
                        <div id="loser-fee-fields" class="{{ ($loserFeeSettings['enabled'] ?? false) ? '' : 'opacity-50 pointer-events-none' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">درصد کارمزد از سپرده (%)</label>
                            <input type="number" name="loser_fee_percentage" value="{{ $loserFeeSettings['percentage'] ?? 5 }}" min="0" max="100" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">مابقی سپرده به کاربر برگردانده می‌شود</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- سپرده ضبط شده --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">سپرده ضبط شده</h3>
                        <p class="text-xs text-gray-500">تقسیم سپرده ضبط‌شده بین سایت و فروشنده</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.forfeit.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 mb-2">درصد سهم سایت (%)</label>
                        <input type="number" name="forfeit_to_site_percentage" value="{{ $forfeitSettings['to_site_percentage'] ?? 100 }}" min="0" max="100" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">مثال: 60 یعنی 60% به سایت و 40% به فروشنده</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

        </div>{{-- end tab-auction --}}

        {{-- ===== TAB: کاربران ===== --}}
        <div id="tab-users" class="tab-panel hidden p-6 space-y-6">

            {{-- احراز هویت --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">احراز هویت کاربران</h3>
                        <p class="text-xs text-gray-500">الزام تایید شماره تلفن یا ایمیل قبل از استفاده از سیستم</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.verification.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200 max-w-lg">
                        <input type="checkbox" name="require_user_verification" value="1"
                               {{ \App\Models\SiteSetting::get('require_user_verification', true) ? 'checked' : '' }}
                               class="mt-1 w-4 h-4 text-primary rounded">
                        <div>
                            <span class="text-sm font-medium text-gray-800">الزام احراز هویت برای کاربران</span>
                            <p class="text-xs text-gray-500 mt-1">کاربران باید قبل از شرکت در حراجی یا ایجاد آگهی، شماره تلفن یا ایمیل خود را تایید کنند</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- OTP --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">sms</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">سیستم OTP</h3>
                        <p class="text-xs text-gray-500">تایید شماره موبایل از طریق کد پیامکی در ثبت‌نام و ورود</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.otp.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 max-w-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-800">وضعیت سیستم OTP</p>
                            <p class="text-xs mt-1">
                                @if($otpEnabled)
                                    <span class="text-green-600 font-medium">● فعال</span> — کاربران باید شماره موبایل را تایید کنند
                                @else
                                    <span class="text-red-500 font-medium">● غیرفعال</span> — ثبت‌نام بدون تایید پیامکی
                                @endif
                            </p>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="otp_enabled" value="1" {{ $otpEnabled ? 'checked' : '' }} class="w-5 h-5 text-primary rounded">
                            <span class="text-sm text-gray-600">{{ $otpEnabled ? 'فعال' : 'غیرفعال' }}</span>
                        </label>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- فروشندگان --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">storefront</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">تایید فروشندگان</h3>
                        <p class="text-xs text-gray-500">نیاز به تایید دستی درخواست‌های فروشندگی توسط ادمین</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.seller.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200 max-w-lg">
                        <input type="checkbox" name="require_seller_approval" value="1"
                               {{ $sellerSettings['require_approval'] ? 'checked' : '' }}
                               class="mt-1 w-4 h-4 text-primary rounded">
                        <div>
                            <span class="text-sm font-medium text-gray-800">نیاز به تایید دستی فروشندگان</span>
                            <p class="text-xs text-gray-500 mt-1">اگر غیرفعال باشد، کاربران بلافاصله پس از درخواست به عنوان فروشنده فعال می‌شوند</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

        </div>{{-- end tab-users --}}

        {{-- ===== TAB: آگهی‌ها ===== --}}
        <div id="tab-listings" class="tab-panel hidden p-6 space-y-6">

            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">sell</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">تنظیمات آگهی‌ها</h3>
                        <p class="text-xs text-gray-500">کنترل نحوه انتشار و نمایش آگهی‌ها</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.listing.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200">
                            <input type="checkbox" name="require_listing_approval" value="1"
                                   {{ ($listingSettings['require_approval'] ?? true) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-primary rounded">
                            <div>
                                <span class="text-sm font-medium text-gray-800">نیاز به تایید دستی آگهی‌ها</span>
                                <p class="text-xs text-gray-500 mt-1">تمام آگهی‌های جدید و ویرایش‌شده باید توسط ادمین تایید شوند</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-white rounded-lg border border-gray-200">
                            <input type="checkbox" name="default_show_before_start" value="1"
                                   {{ ($listingSettings['default_show_before_start'] ?? false) ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-primary rounded">
                            <div>
                                <span class="text-sm font-medium text-gray-800">نمایش پیش‌فرض حراجی‌ها قبل از شروع</span>
                                <p class="text-xs text-gray-500 mt-1">حراجی‌های جدید با برچسب "هنوز شروع نشده" در لیست نمایش داده می‌شوند</p>
                            </div>
                        </div>
                        <div class="p-4 bg-white rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-800 mb-2">گام افزایش پیشنهاد پیش‌فرض (تومان)</label>
                            <input type="number" name="default_bid_increment"
                                   value="{{ old('default_bid_increment', $listingSettings['default_bid_increment'] ?? 10000) }}"
                                   min="1000" step="1000" required
                                   class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">حداقل افزایش پیشنهاد برای تمام آگهی‌های جدید</p>
                            @error('default_bid_increment')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start gap-2">
                        <span class="material-symbols-outlined text-yellow-600 text-[16px] mt-0.5">info</span>
                        <p class="text-xs text-yellow-800">اگر گزینه "نیاز به تایید دستی" را غیرفعال کنید، آگهی‌های جدید بدون بررسی منتشر می‌شوند.</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

        </div>{{-- end tab-listings --}}

        {{-- ===== TAB: مالی ===== --}}
        <div id="tab-finance" class="tab-panel hidden p-6 space-y-6">

            {{-- کیف پول --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">کیف پول</h3>
                        <p class="text-xs text-gray-500">محدودیت‌های شارژ و برداشت از کیف پول</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.wallet.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حداقل شارژ (تومان)</label>
                            <input type="number" name="wallet_min_deposit" value="{{ $walletSettings['min_deposit'] ?? 10000 }}" min="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حداکثر شارژ (تومان)</label>
                            <input type="number" name="wallet_max_deposit" value="{{ $walletSettings['max_deposit'] ?? 100000000 }}" min="10000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حداقل برداشت (تومان)</label>
                            <input type="number" name="wallet_min_withdraw" value="{{ $walletSettings['min_withdraw'] ?? 50000 }}" min="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مالیات شارژ (%)</label>
                            <input type="number" name="wallet_charge_tax" value="{{ $walletSettings['charge_tax'] ?? 0 }}" min="0" max="100" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">مثال: 9% → شارژ 100,000 = پرداخت 109,000 تومان</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- جریمه لغو سفارش --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">جریمه لغو سفارش</h3>
                        <p class="text-xs text-gray-500">کسر جریمه از کیف پول در صورت لغو سفارش در مرحله پردازش</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.cancellation-penalty.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع محاسبه جریمه</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="order_cancellation_penalty_type" value="percentage"
                                           {{ (\App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage') === 'percentage') ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">درصد از مبلغ سفارش</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="order_cancellation_penalty_type" value="fixed"
                                           {{ (\App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage') === 'fixed') ? 'checked' : '' }} class="text-primary">
                                    <span class="text-sm">مبلغ ثابت</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مقدار جریمه (درصد یا تومان)</label>
                            <input type="number" name="order_cancellation_penalty_value"
                                   value="{{ \App\Models\SiteSetting::get('order_cancellation_penalty_value', 10) }}"
                                   min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">درصدی: 0 تا 100 — ثابت: مبلغ به تومان</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

            {{-- مهلت تست کالا --}}
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">مهلت تست و بررسی کالا</h3>
                        <p class="text-xs text-gray-500">مدت زمانی که خریدار برای دریافت و تست کالا دارد</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.test-period.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium text-gray-700 mb-2">مهلت تست (روز)</label>
                        <input type="number" name="order_test_period_days"
                               value="{{ \App\Models\SiteSetting::get('order_test_period_days', 7) }}"
                               min="1" max="30"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">از زمان ثبت کد رهگیری توسط فروشنده. پیشنهادی: 7 روز</p>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="px-8 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">ذخیره</button>
                    </div>
                </form>
            </div>

        </div>{{-- end tab-finance --}}

    </div>{{-- end card --}}
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-primary', 'text-primary');
        b.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-' + tabId).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + tabId);
    btn.classList.remove('border-transparent', 'text-gray-500');
    btn.classList.add('border-primary', 'text-primary');
}

function toggleDurationFields() {
    const cb = document.getElementById('force_auction_duration');
    const f  = document.getElementById('duration-fields');
    f.classList.toggle('opacity-50', !cb.checked);
    f.classList.toggle('pointer-events-none', !cb.checked);
}

function toggleLoserFeeFields() {
    const cb = document.getElementById('loser_fee_enabled');
    const f  = document.getElementById('loser-fee-fields');
    f.classList.toggle('opacity-50', !cb.checked);
    f.classList.toggle('pointer-events-none', !cb.checked);
}
</script>
@endsection
