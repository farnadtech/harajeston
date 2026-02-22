@extends('layouts.app')

@section('title', 'کیف پول')

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
            <form method="POST" action="{{ route('wallet.add-funds') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ (تومان)</label>
                    @php
                        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
                        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);
                    @endphp
                    <input type="number" name="amount" placeholder="مثال: 100000" required 
                           min="{{ $minDeposit }}" max="{{ $maxDeposit }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">حداقل: @price($minDeposit) - حداکثر: @price($maxDeposit) تومان</p>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">payments</span>
                    <span>افزایش موجودی</span>
                </button>
            </form>
        </div>

        <!-- Withdraw Funds -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-2xl">remove_circle</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">برداشت از حساب</h2>
            </div>
            <form method="POST" action="{{ route('wallet.withdraw') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ (تومان)</label>
                    @php
                        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);
                    @endphp
                    <input type="number" name="amount" placeholder="مثال: 50000" required 
                           min="{{ $minWithdraw }}" max="{{ $wallet->balance }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">حداقل: @price($minWithdraw) - حداکثر: @price($wallet->balance) تومان</p>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">account_balance</span>
                    <span>درخواست برداشت</span>
                </button>
            </form>
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
                                    ];
                                    
                                    $label = $typeLabels[$transaction->type] ?? $transaction->type;
                                    $color = $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold {{ in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in']) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in']) ? '+' : '-' }}
                                    @price($transaction->amount)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                @price($transaction->balance_after)
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
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
