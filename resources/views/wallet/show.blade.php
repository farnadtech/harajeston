@extends('layouts.app')

@section('title', 'کیف پول')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">کیف پول من</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Available Balance -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">موجودی قابل استفاده</h3>
            <p class="text-4xl font-bold">
                @price($wallet->balance)
                <span class="text-lg mr-2">ریال</span>
            </p>
        </div>

        <!-- Frozen Balance -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">موجودی مسدود شده</h3>
            <p class="text-4xl font-bold">
                @price($wallet->frozen)
                <span class="text-lg mr-2">ریال</span>
            </p>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg mb-2 opacity-90">مجموع موجودی</h3>
            <p class="text-4xl font-bold">
                @price($wallet->balance + $wallet->frozen)
                <span class="text-lg mr-2">ریال</span>
            </p>
        </div>
    </div>

    <!-- Add Funds -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">افزایش موجودی</h2>
        <form method="POST" action="{{ route('wallet.add-funds') }}" class="flex gap-4">
            @csrf
            <input type="number" name="amount" placeholder="مبلغ به ریال" required
                   class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                افزایش موجودی
            </button>
        </form>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">تاریخچه تراکنش‌ها</h2>
            <a href="{{ route('wallet.export') }}" class="text-blue-600 hover:underline">
                دانلود CSV
            </a>
        </div>

        <!-- Filters -->
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                   class="px-4 py-2 border rounded-lg">
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                   class="px-4 py-2 border rounded-lg">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                فیلتر
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right">تاریخ</th>
                        <th class="px-4 py-3 text-right">نوع</th>
                        <th class="px-4 py-3 text-right">مبلغ</th>
                        <th class="px-4 py-3 text-right">موجودی قبل</th>
                        <th class="px-4 py-3 text-right">موجودی بعد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions ?? [] as $transaction)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">@jalali($transaction->created_at)</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-sm
                                    {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ __('transactions.' . $transaction->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold">@currency($transaction->amount)</td>
                            <td class="px-4 py-3">@currency($transaction->balance_before)</td>
                            <td class="px-4 py-3">@currency($transaction->balance_after)</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                هیچ تراکنشی یافت نشد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($transactions) && $transactions->hasPages())
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
