@extends('layouts.admin')

@section('title', 'کیف پول')
@section('page-title', 'مدیریت کیف پول')
@section('page-subtitle', 'مشاهده و مدیریت موجودی کیف پول')

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

    <!-- Wallet Balance Card -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-2">موجودی کیف پول</p>
                <h2 class="text-4xl font-bold mb-1">{{ number_format($wallet->balance) }} تومان</h2>
                @if($wallet->frozen > 0)
                    <p class="text-blue-100 text-sm mt-2">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            مسدود شده: {{ number_format($wallet->frozen) }} تومان
                        </span>
                    </p>
                @endif
            </div>
            <div class="text-left">
                <svg class="w-20 h-20 text-blue-300 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
        
        <div class="flex gap-4 mt-6">
            <button onclick="document.getElementById('addFundsModal').classList.remove('hidden')" 
                    class="flex-1 bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                افزایش موجودی
            </button>
            <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')" 
                    class="flex-1 bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                برداشت وجه
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('wallet.show') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                <input type="text" 
                       name="from_date" 
                       id="from_date"
                       value="{{ request('from_date') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="انتخاب تاریخ">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                <input type="text" 
                       name="to_date" 
                       id="to_date"
                       value="{{ request('to_date') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="انتخاب تاریخ">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    فیلتر
                </button>
                <a href="{{ route('wallet.show') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">تراکنش‌های اخیر</h3>
            <a href="{{ route('wallet.export') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                دانلود گزارش
            </a>
        </div>
        
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">توضیحات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">موجودی</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Morilog\Jalali\Jalalian::fromCarbon($transaction->created_at)->format('Y/m/d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaction->type === 'deposit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">واریز</span>
                                    @elseif($transaction->type === 'withdraw' || $transaction->type === 'withdrawal')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">برداشت</span>
                                    @elseif($transaction->type === 'purchase')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">خرید</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $transaction->type }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->amount > 0 ? '+' : '' }}{{ number_format($transaction->amount) }} تومان
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($transaction->after_balance) }} تومان</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links('vendor.pagination.custom') }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-4 text-gray-500">هیچ تراکنشی یافت نشد</p>
            </div>
        @endif
    </div>

    <!-- Modals -->
    <div id="addFundsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">افزایش موجودی</h3>
                <button onclick="document.getElementById('addFundsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('wallet.add-funds') }}" id="addFundsForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ شارژ (تومان)</label>
                    @php
                        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
                        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);
                        $taxPercentage = \App\Models\SiteSetting::get('wallet_charge_tax', 0);
                    @endphp
                    <input type="number" name="amount" id="chargeAmount" required step="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: 100000"
                           oninput="calculateChargeTax()">
                    <p class="text-xs text-gray-500 mt-1">حداقل: {{ number_format($minDeposit) }} تومان | حداکثر: {{ number_format($maxDeposit) }} تومان</p>
                    <p id="amountError" class="text-xs text-red-600 mt-1" style="display:none;"></p>
                </div>

                @if($taxPercentage > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4" id="taxInfo" style="display: none;">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">مبلغ شارژ:</span>
                            <span class="font-semibold text-gray-900" id="baseAmount">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">مالیات ({{ \App\Services\PersianNumberService::convertToPersian($taxPercentage) }}%):</span>
                            <span class="font-semibold text-blue-600" id="taxAmount">0</span>
                        </div>
                        <div class="border-t border-blue-300 pt-2 flex justify-between">
                            <span class="font-bold text-gray-900">مبلغ قابل پرداخت:</span>
                            <span class="font-bold text-lg text-blue-700" id="totalAmount">0</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit" id="submitCharge" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">پرداخت</button>
                    <button type="button" onclick="document.getElementById('addFundsModal').classList.add('hidden')"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">انصراف</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const TAX_PERCENTAGE = {{ $taxPercentage }};
    const MIN_DEPOSIT = {{ $minDeposit }};
    const MAX_DEPOSIT = {{ $maxDeposit }};
    
    function calculateChargeTax() {
        const amountInput = document.getElementById('chargeAmount');
        const amount = parseFloat(amountInput.value) || 0;
        const errorElement = document.getElementById('amountError');
        const submitButton = document.getElementById('submitCharge');
        
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
        
        if (amount > 0 && TAX_PERCENTAGE > 0 && !hasError) {
            const tax = Math.round((amount * TAX_PERCENTAGE) / 100);
            const total = amount + tax;
            
            document.getElementById('baseAmount').textContent = amount.toLocaleString('fa-IR') + ' تومان';
            document.getElementById('taxAmount').textContent = tax.toLocaleString('fa-IR') + ' تومان';
            document.getElementById('totalAmount').textContent = total.toLocaleString('fa-IR') + ' تومان';
            document.getElementById('taxInfo').style.display = 'block';
        } else {
            document.getElementById('taxInfo').style.display = 'none';
        }
    }
    </script>

    <div id="withdrawModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">برداشت وجه</h3>
                <button onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('wallet.withdraw') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ (تومان)</label>
                    @php
                        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);
                    @endphp
                    <input type="number" name="amount" required min="{{ $minWithdraw }}" max="{{ $wallet->balance }}" step="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="مثال: 100000">
                    <p class="text-xs text-gray-500 mt-1">حداقل: {{ number_format($minWithdraw) }} تومان | حداکثر: {{ number_format($wallet->balance) }} تومان</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-yellow-800">مبلغ درخواستی ظرف 24 ساعت به حساب بانکی شما واریز خواهد شد.</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">ثبت درخواست</button>
                    <button type="button" onclick="document.getElementById('withdrawModal').classList.add('hidden')"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">انصراف</button>
                </div>
            </form>
        </div>
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
