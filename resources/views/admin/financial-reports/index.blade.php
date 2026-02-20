@extends('layouts.admin')

@section('title', 'گزارشات مالی')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">گزارشات مالی</h1>
        
        <!-- فیلتر بازه زمانی -->
        <form method="GET" action="{{ route('admin.financial-reports.index') }}" class="flex gap-4">
            <input type="date" 
                   name="start_date" 
                   value="{{ $startDate->format('Y-m-d') }}"
                   class="px-4 py-2 border rounded-lg">
            <input type="date" 
                   name="end_date" 
                   value="{{ $endDate->format('Y-m-d') }}"
                   class="px-4 py-2 border rounded-lg">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                اعمال فیلتر
            </button>
            <a href="{{ route('admin.financial-reports.export', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
               class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                دانلود CSV
            </a>
        </form>
    </div>

    <!-- موجودی کیف پول سایت -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-8 mb-8 text-white">
        <h2 class="text-2xl font-bold mb-4">موجودی کیف پول سایت</h2>
        <div class="grid grid-cols-3 gap-6">
            <div>
                <p class="text-purple-200 text-sm mb-2">موجودی کل</p>
                <p class="text-3xl font-bold">@persian(number_format($siteWallet['balance'])) تومان</p>
            </div>
            <div>
                <p class="text-purple-200 text-sm mb-2">مبلغ فریز شده</p>
                <p class="text-3xl font-bold">@persian(number_format($siteWallet['frozen'])) تومان</p>
            </div>
            <div>
                <p class="text-purple-200 text-sm mb-2">قابل برداشت</p>
                <p class="text-3xl font-bold">@persian(number_format($siteWallet['available'])) تومان</p>
            </div>
        </div>
    </div>

    <!-- خلاصه درآمد -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-medium">کل درآمد</h3>
                <span class="material-symbols-outlined text-green-600">payments</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">@persian(number_format($summary['total_revenue'])) تومان</p>
            <p class="text-sm text-gray-500 mt-2">
                از {{ \Morilog\Jalali\Jalalian::fromCarbon($startDate)->format('Y/m/d') }} 
                تا {{ \Morilog\Jalali\Jalalian::fromCarbon($endDate)->format('Y/m/d') }}
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-medium">کمیسیون‌ها</h3>
                <span class="material-symbols-outlined text-blue-600">account_balance</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">@persian(number_format($summary['commissions'])) تومان</p>
            <p class="text-sm text-gray-500 mt-2">
                @persian(number_format($summary['commission_rate'], 1))% از حجم معاملات
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-medium">سپرده‌های ضبط شده</h3>
                <span class="material-symbols-outlined text-red-600">block</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">@persian(number_format($summary['forfeited_deposits'])) تومان</p>
            <p class="text-sm text-gray-500 mt-2">از برندگان عدم پرداخت</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-600 text-sm font-medium">معاملات موفق</h3>
                <span class="material-symbols-outlined text-purple-600">check_circle</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">@persian(number_format($summary['successful_auctions']))</p>
            <p class="text-sm text-gray-500 mt-2">
                میانگین کمیسیون: @persian(number_format($summary['average_commission_per_sale'])) تومان
            </p>
        </div>
    </div>

    <!-- نمودار درآمد روزانه -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold mb-6">نمودار درآمد روزانه</h2>
        <canvas id="dailyRevenueChart" height="80"></canvas>
    </div>

    <!-- آمار پلتفرم -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-bold mb-6">آمار کلی پلتفرم</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-2">کل کاربران</p>
                <p class="text-3xl font-bold text-blue-600">@persian(number_format($platformStats['total_users']))</p>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-2">فروشندگان</p>
                <p class="text-3xl font-bold text-green-600">@persian(number_format($platformStats['total_sellers']))</p>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-2">خریداران</p>
                <p class="text-3xl font-bold text-purple-600">@persian(number_format($platformStats['total_buyers']))</p>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-sm mb-2">حراج‌های فعال</p>
                <p class="text-3xl font-bold text-orange-600">@persian(number_format($platformStats['active_listings']))</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- فروشندگان برتر -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">فروشندگان برتر</h2>
            <div class="space-y-4">
                @forelse($topSellers as $index => $seller)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-bold text-gray-400">@persian($index + 1)</span>
                            <div>
                                <p class="font-bold text-gray-900">{{ $seller['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $seller['email'] }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-green-600">@persian(number_format($seller['total_revenue'])) تومان</p>
                            <p class="text-sm text-gray-600">@persian($seller['total_sales']) فروش</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">داده‌ای یافت نشد</p>
                @endforelse
            </div>
        </div>

        <!-- خریداران برتر -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">خریداران برتر</h2>
            <div class="space-y-4">
                @forelse($topBuyers as $index => $buyer)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-bold text-gray-400">@persian($index + 1)</span>
                            <div>
                                <p class="font-bold text-gray-900">{{ $buyer['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $buyer['email'] }}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-blue-600">@persian(number_format($buyer['total_spent'])) تومان</p>
                            <p class="text-sm text-gray-600">@persian($buyer['total_purchases']) خرید</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">داده‌ای یافت نشد</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- آمار دسته‌بندی‌ها -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">آمار دسته‌بندی‌ها</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-right py-3 px-4">دسته‌بندی</th>
                        <th class="text-right py-3 px-4">تعداد حراج</th>
                        <th class="text-right py-3 px-4">ارزش کل</th>
                        <th class="text-right py-3 px-4">میانگین قیمت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoryStats as $stat)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium">{{ $stat['category'] ?? 'نامشخص' }}</td>
                            <td class="py-3 px-4">@persian(number_format($stat['total_listings']))</td>
                            <td class="py-3 px-4">@persian(number_format($stat['total_value'])) تومان</td>
                            <td class="py-3 px-4">@persian(number_format($stat['average_price'])) تومان</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500">داده‌ای یافت نشد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // نمودار درآمد روزانه
    const dailyData = @json($dailyRevenue);
    
    const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [
                {
                    label: 'کمیسیون',
                    data: dailyData.map(d => d.commissions),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'سپرده ضبط شده',
                    data: dailyData.map(d => d.forfeited_deposits),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'کل درآمد',
                    data: dailyData.map(d => d.total),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fa-IR') + ' تومان';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fa-IR');
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
