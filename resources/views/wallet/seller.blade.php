@extends('layouts.seller')

@section('title', 'کیف پول')
@section('page-title', 'کیف پول من')
@section('page-subtitle', 'مدیریت موجودی و تراکنش‌های مالی')

@push('styles')
<link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}?v={{ now()->timestamp }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">کیف پول من</h1>
        <p class="text-gray-600 mt-2">مدیریت موجودی و تراکنش‌های مالی</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined">error</span>
                <span class="font-bold">خطاهای اعتبارسنجی:</span>
            </div>
            <ul class="list-disc list-inside mr-8">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Available Balance -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">موجودی قابل استفاده</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">account_balance_wallet</span>
            </div>
            <p class="text-4xl font-bold">
                @price($wallet->balance)
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>

        <!-- Frozen Balance -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">موجودی مسدود شده</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">lock</span>
            </div>
            <p class="text-4xl font-bold">
                @price($wallet->frozen)
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">مجموع موجودی</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">savings</span>
            </div>
            <p class="text-4xl font-bold">
                @price($wallet->balance + $wallet->frozen)
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Add Funds -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 text-2xl">add_circle</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">افزایش موجودی</h2>
            </div>
            <form method="POST" action="{{ route('wallet.add-funds') }}" class="space-y-4" id="addFundsFormSeller">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ شارژ (تومان)</label>
                    @php
                        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
                        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);
                        $taxPercentage = \App\Models\SiteSetting::get('wallet_charge_tax', 0);
                    @endphp
                    <input type="number" name="amount" id="chargeAmountSeller" placeholder="مثال: 100000" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           oninput="calculateChargeTaxSeller()">
                    <p class="text-xs text-gray-500 mt-1">حداقل: @price($minDeposit) - حداکثر: @price($maxDeposit) تومان</p>
                    <p id="amountErrorSeller" class="text-xs text-red-600 mt-1" style="display:none;"></p>
                </div>

                <!-- Gateway Selection -->
                @if($gateways->count() > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">انتخاب درگاه پرداخت</label>
                    <div class="space-y-2">
                        @foreach($gateways as $gateway)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="gateway" value="{{ $gateway->name }}" required
                                   class="w-5 h-5 text-green-600 focus:ring-green-500">
                            <div class="mr-3 flex-1">
                                <span class="font-medium text-gray-900">{{ $gateway->display_name }}</span>
                            </div>
                            <span class="material-symbols-outlined text-gray-400">payment</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-sm text-yellow-800">در حال حاضر درگاه پرداخت فعالی وجود ندارد. لطفا با پشتیبانی تماس بگیرید.</p>
                </div>
                @endif

                @if($taxPercentage > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4" id="taxInfoSeller" style="display: none;">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">مبلغ شارژ:</span>
                            <span class="font-semibold text-gray-900" id="baseAmountSeller">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">مالیات ({{ \App\Services\PersianNumberService::convertToPersian($taxPercentage) }}%):</span>
                            <span class="font-semibold text-blue-600" id="taxAmountSeller">0</span>
                        </div>
                        <div class="border-t border-blue-300 pt-2 flex justify-between">
                            <span class="font-bold text-gray-900">مبلغ قابل پرداخت:</span>
                            <span class="font-bold text-lg text-blue-700" id="totalAmountSeller">0</span>
                        </div>
                    </div>
                </div>
                @endif

                <button type="submit" id="submitChargeSeller" class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2" 
                        {{ $gateways->count() == 0 ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined">payments</span>
                    <span>پرداخت و شارژ کیف پول</span>
                </button>
            </form>
        </div>

        <script>
        const TAX_PERCENTAGE_SELLER = {{ $taxPercentage }};
        const MIN_DEPOSIT = {{ $minDeposit }};
        const MAX_DEPOSIT = {{ $maxDeposit }};
        const MIN_WITHDRAW = {{ $minWithdraw }};
        const MAX_WITHDRAW = {{ $wallet->balance }};
        
        function calculateChargeTaxSeller() {
            const amountInput = document.getElementById('chargeAmountSeller');
            const amount = parseFloat(amountInput.value) || 0;
            const errorElement = document.getElementById('amountErrorSeller');
            const submitButton = document.getElementById('submitChargeSeller');
            
            // بررسی محدوده مبلغ
            let hasError = false;
            if (amount > 0 && amount < MIN_DEPOSIT) {
                errorElement.textContent = 'حداقل مبلغ شارژ ' + MIN_DEPOSIT.toLocaleString('fa-IR') + ' تومان است.';
                errorElement.style.display = 'block';
                hasError = true;
            } else if (amount > MAX_DEPOSIT) {
                errorElement.textContent = 'حداکثر مبلغ شارژ ' + MAX_DEPOSIT.toLocaleString('fa-IR') + ' تومان است.';
                errorElement.style.display = 'block';
                hasError = true;
            } else {
                errorElement.style.display = 'none';
            }
            
            // غیرفعال کردن دکمه در صورت خطا
            if (submitButton) {
                submitButton.disabled = hasError || amount <= 0;
            }
            
            if (amount > 0 && TAX_PERCENTAGE_SELLER > 0 && !hasError) {
                // محاسبه مالیات و رند کردن به عدد صحیح
                const tax = Math.round((amount * TAX_PERCENTAGE_SELLER) / 100);
                const total = amount + tax;
                
                document.getElementById('baseAmountSeller').textContent = amount.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('taxAmountSeller').textContent = tax.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('totalAmountSeller').textContent = total.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('taxInfoSeller').style.display = 'block';
            } else {
                document.getElementById('taxInfoSeller').style.display = 'none';
            }
        }
        
        function validateWithdrawSeller() {
            const amountInput = document.getElementById('withdrawAmountSeller');
            const amount = parseFloat(amountInput.value) || 0;
            const errorElement = document.getElementById('withdrawErrorSeller');
            const submitButton = document.getElementById('submitWithdrawSeller');
            
            // بررسی محدوده مبلغ
            let hasError = false;
            if (amount > 0 && amount < MIN_WITHDRAW) {
                errorElement.textContent = 'حداقل مبلغ برداشت ' + MIN_WITHDRAW.toLocaleString('fa-IR') + ' تومان است.';
                errorElement.style.display = 'block';
                hasError = true;
            } else if (amount > MAX_WITHDRAW) {
                errorElement.textContent = 'حداکثر مبلغ برداشت ' + MAX_WITHDRAW.toLocaleString('fa-IR') + ' تومان است (موجودی شما).';
                errorElement.style.display = 'block';
                hasError = true;
            } else {
                errorElement.style.display = 'none';
            }
            
            // غیرفعال کردن دکمه در صورت خطا
            if (submitButton) {
                submitButton.disabled = hasError || amount <= 0;
            }
        }
        </script>

        <!-- Withdraw Funds -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-2xl">remove_circle</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">درخواست برداشت</h2>
            </div>

            @php
                $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);
                $pendingWithdrawal = \App\Models\WithdrawalRequest::where('user_id', auth()->id())->where('status', 'pending')->first();
                $lastRequest = \App\Models\WithdrawalRequest::where('user_id', auth()->id())->latest()->first();
                $lastRejected = ($lastRequest && $lastRequest->status === 'rejected') ? $lastRequest : null;
            @endphp

            @if($pendingWithdrawal)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center gap-2 text-yellow-700">
                        <span class="material-symbols-outlined">hourglass_top</span>
                        <p class="text-sm font-medium">درخواست برداشت <strong>@price($pendingWithdrawal->amount) تومان</strong> در انتظار بررسی ادمین است.</p>
                    </div>
                </div>
            @else
                @if($lastRejected)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                        <div class="flex items-start gap-2 text-red-700">
                            <span class="material-symbols-outlined mt-0.5">cancel</span>
                            <div>
                                <p class="text-sm font-medium">آخرین درخواست رد شد:</p>
                                <p class="text-xs mt-1">{{ $lastRejected->reject_reason }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('wallet.withdraw') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ برداشت (تومان) *</label>
                            <input type="number" name="amount" id="withdrawAmountSeller"
                                   placeholder="مثال: 500000" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                                   oninput="validateWithdrawSeller()">
                            <p class="text-xs text-gray-500 mt-1">حداقل: @price($minWithdraw) — حداکثر: @price($wallet->balance) تومان</p>
                            <p id="withdrawErrorSeller" class="text-xs text-red-600 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نام و نام خانوادگی *</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                   placeholder="مطابق کارت ملی"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نام بانک *</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}" required
                                   placeholder="مثال: ملت، صادرات، ملی"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">شماره کارت *</label>
                            <input type="text" name="card_number" value="{{ old('card_number') }}" required
                                   placeholder="16 رقم بدون خط تیره" maxlength="16" dir="ltr"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">شماره شبا *</label>
                            <input type="text" name="sheba_number" value="{{ old('sheba_number') }}" required
                                   placeholder="IR + 22 رقم" maxlength="24" dir="ltr"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">کد ملی *</label>
                            <input type="text" name="national_id" value="{{ old('national_id') }}" required
                                   placeholder="10 رقم" maxlength="10" dir="ltr"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                        </div>
                    </div>
                    <button type="submit" id="submitWithdrawSeller"
                            class="w-full bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">account_balance</span>
                        <span>ثبت درخواست برداشت</span>
                    </button>
                </form>
            @endif

            @php $myWithdrawals = \App\Models\WithdrawalRequest::where('user_id', auth()->id())->latest()->take(5)->get(); @endphp
            @if($myWithdrawals->count() > 0)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 mb-3">آخرین درخواست‌های برداشت:</p>
                    <div class="space-y-2">
                        @foreach($myWithdrawals as $wr)
                        <div class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-3 py-2">
                            <span class="font-medium text-gray-800">@price($wr->amount) تومان</span>
                            <span class="text-gray-400">{{ \Morilog\Jalali\Jalalian::fromCarbon($wr->created_at)->format('Y/m/d') }}</span>
                            <span class="px-2 py-0.5 rounded-full font-medium
                                {{ $wr->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($wr->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">
                                {{ $wr->status_label }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-2xl">receipt_long</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">تاریخچه تراکنش‌ها</h2>
                </div>
                <a href="{{ route('wallet.export') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                    <span class="material-symbols-outlined text-xl">download</span>
                    <span>دانلود CSV</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                    <input type="text" id="from_date" name="from_date" value="{{ request('from_date') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="انتخاب تاریخ" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                    <input type="text" id="to_date" name="to_date" value="{{ request('to_date') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="انتخاب تاریخ" readonly>
                </div>
                <div class="sm:col-span-2 flex items-end gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">filter_alt</span>
                        <span>اعمال فیلتر</span>
                    </button>
                    <a href="{{ route('wallet.show') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        حذف فیلتر
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">تاریخ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">نوع</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">مبلغ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">توضیحات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">موجودی بعد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transactions ?? [] as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @php
                                    $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($transaction->created_at)->format('Y/m/d H:i');
                                @endphp
                                {{ \App\Services\PersianNumberService::convertToPersian($jalaliDate) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeLabels = [
                                        'deposit' => 'واریز',
                                        'withdrawal' => 'برداشت از حساب',
                                        'freeze_deposit' => 'مسدود سازی سپرده',
                                        'release_deposit' => 'آزادسازی سپرده',
                                        'deduct_frozen' => 'کسر از موجودی مسدود',
                                        'transfer_in' => 'انتقال به حساب',
                                        'transfer_out' => 'انتقال از حساب',
                                        'forfeit' => 'ضبط سپرده',
                                        'purchase' => 'خرید',
                                        'refund' => 'بازگشت وجه',
                                        'auction_payment' => 'تبدیل سپرده به پرداخت',
                                        'order_cancellation_penalty' => 'جریمه لغو سفارش',
                                        'order_cancellation_penalty_revenue' => 'درآمد جریمه لغو',
                                        'unfreeze_refund' => 'بازگشت وجه مسدود شده',
                                    ];
                                    $typeColors = [
                                        'deposit' => 'bg-green-100 text-green-800',
                                        'withdrawal' => 'bg-red-100 text-red-800',
                                        'freeze_deposit' => 'bg-orange-100 text-orange-800',
                                        'release_deposit' => 'bg-blue-100 text-blue-800',
                                        'deduct_frozen' => 'bg-red-100 text-red-800',
                                        'transfer_in' => 'bg-green-100 text-green-800',
                                        'transfer_out' => 'bg-red-100 text-red-800',
                                        'forfeit' => 'bg-red-100 text-red-800',
                                        'purchase' => 'bg-purple-100 text-purple-800',
                                        'refund' => 'bg-green-100 text-green-800',
                                        'auction_payment' => 'bg-blue-100 text-blue-800',
                                        'order_cancellation_penalty' => 'bg-red-100 text-red-800',
                                        'order_cancellation_penalty_revenue' => 'bg-green-100 text-green-800',
                                        'unfreeze_refund' => 'bg-green-100 text-green-800',
                                    ];
                                    
                                    $label = $typeLabels[$transaction->type] ?? $transaction->type;
                                    $color = $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold {{ in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in', 'unfreeze_refund', 'order_cancellation_penalty_revenue']) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in', 'unfreeze_refund', 'order_cancellation_penalty_revenue']) ? '+' : '-' }}
                                    @price($transaction->amount)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $transaction->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                @price($transaction->balance_after)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-gray-300 text-6xl mb-3 block">receipt_long</span>
                                <p class="text-gray-500 font-medium">هیچ تراکنشی یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($transactions) && $transactions->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ url('js/persian-datepicker-package.js') }}?v={{ now()->timestamp }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    
    if (fromDateInput && typeof PersianDatePicker !== 'undefined') {
        new PersianDatePicker(fromDateInput);
    }
    
    if (toDateInput && typeof PersianDatePicker !== 'undefined') {
        new PersianDatePicker(toDateInput);
    }
});
</script>
@endpush
