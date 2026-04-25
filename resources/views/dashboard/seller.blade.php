@extends('layouts.seller')

@section('title', 'داشبورد فروشنده')
@section('page-title', 'خوش آمدید، ' . (optional(auth()->user()->store)->store_name ?? auth()->user()->name) . ' 👋')
@section('page-subtitle', 'خلاصه وضعیت فروشگاه شما امروز')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">payments</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">درآمد تکمیل‌شده</p>
                <h3 class="text-lg font-black text-gray-900">
                    @persian(number_format($stats['total_sales'] ?? 0))
                    <span class="text-xs font-normal text-gray-400">ت</span>
                </h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">gavel</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">مزایده فعال</p>
                <h3 class="text-lg font-black text-gray-900">@persian($stats['active_auctions'] ?? 0)</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">pending</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">نیاز به تایید</p>
                <h3 class="text-lg font-black text-gray-900">@persian($stats['pending_listings'] ?? 0)</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">schedule</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">منتظر شروع</p>
                <h3 class="text-lg font-black text-gray-900">@persian($stats['scheduled_listings'] ?? 0)</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">check_circle</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">تکمیل شده</p>
                <h3 class="text-lg font-black text-gray-900">@persian($stats['completed_auctions'] ?? 0)</h3>
            </div>
        </div>
    </div>

    {{-- Financial Breakdown --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">گزارش مالی سفارشات</h3>
                <p class="text-xs text-gray-500 mt-0.5">تفکیک درآمد بر اساس وضعیت سفارش</p>
            </div>
            <div class="text-left">
                <p class="text-xs text-gray-400">مجموع کل سفارشات</p>
                <p class="text-lg font-black text-gray-900">
                    @persian(number_format($grandTotal))
                    <span class="text-xs font-normal text-gray-400">تومان</span>
                </p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($financials as $status => $info)
                @if($info['count'] > 0)
                <div class="bg-{{ $info['color'] }}-50 border border-{{ $info['color'] }}-100 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-{{ $info['color'] }}-700">{{ $info['label'] }}</span>
                        <span class="text-xs bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-700 px-1.5 py-0.5 rounded-full font-bold">
                            @persian($info['count'])
                        </span>
                    </div>
                    <p class="text-base font-black text-{{ $info['color'] }}-900">
                        @persian(number_format($info['total']))
                    </p>
                    <p class="text-xs text-{{ $info['color'] }}-600 mt-0.5">تومان</p>
                    @if($grandTotal > 0)
                    <div class="mt-2 h-1 bg-{{ $info['color'] }}-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $info['color'] }}-400 rounded-full"
                             style="width: {{ min(100, round(($info['total'] / $grandTotal) * 100)) }}%"></div>
                    </div>
                    <p class="text-xs text-{{ $info['color'] }}-500 mt-1">
                        @persian(round(($info['total'] / $grandTotal) * 100))٪ از کل
                    </p>
                    @endif
                </div>
                @endif
                @endforeach
                @if($grandCount === 0)
                <div class="col-span-4 text-center py-8 text-gray-400">
                    <span class="material-symbols-outlined text-4xl block mb-2">receipt_long</span>
                    هنوز سفارشی ثبت نشده
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sales Chart with period selector --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">نمودار فروش</h3>
                <p class="text-xs text-gray-500 mt-0.5">شامل: تکمیل‌شده، در پردازش، ارسال‌شده، تحویل‌شده، پرداخت‌شده</p>
            </div>
            <div class="flex gap-1 flex-wrap justify-end">
                @foreach([7=>'۷ روز', 30=>'۱ ماه', 90=>'۳ ماه', 180=>'۶ ماه', 365=>'۱ سال'] as $d => $label)
                <button onclick="loadChart({{ $d }}, this)"
                        data-days="{{ $d }}"
                        class="chart-btn px-2.5 py-1 rounded-lg text-xs font-medium transition-colors {{ $d === 7 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
        <div class="p-5">
            <div id="chart-container" style="height:192px;display:flex;align-items:flex-end;gap:2px;overflow:hidden;"></div>
            <div id="chart-labels" style="display:flex;gap:2px;margin-top:4px;overflow:hidden;"></div>
        </div>
    </div>

    {{-- Active/Pending/Scheduled Listings --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">آگهی‌های من</h3>
                <p class="text-xs text-gray-500 mt-0.5">فعال، در انتظار تایید و زمان‌بندی‌شده</p>
            </div>
            <a href="{{ route('listings.create') }}"
               class="px-3 py-1.5 text-xs font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                + افزودن آگهی
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 font-semibold">
                        <th class="px-5 py-3">محصول</th>
                        <th class="px-5 py-3">قیمت فعلی</th>
                        <th class="px-5 py-3">زمان</th>
                        <th class="px-5 py-3 text-center">وضعیت</th>
                        <th class="px-5 py-3 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activeListings as $listing)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($listing->images->count() > 0)
                                    <img src="{{ $listing->images->first()->url }}" alt="{{ $listing->title }}"
                                         class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-gray-400 text-base">image</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ Str::limit($listing->title, 30) }}</p>
                                    <p class="text-xs text-gray-400">شروع: @persian(number_format($listing->starting_price)) ت</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-bold text-gray-900">
                            @persian(number_format($listing->current_price ?? $listing->starting_price))
                            <span class="text-xs font-normal text-gray-400">ت</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            @if($listing->status === 'scheduled' && $listing->starts_at)
                                شروع: {{ $listing->starts_at->diffForHumans() }}
                            @elseif($listing->ends_at)
                                {{ $listing->ends_at > now() ? 'پایان: '.$listing->ends_at->diffForHumans() : 'پایان یافته' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($listing->status === 'active')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">فعال</span>
                            @elseif($listing->status === 'pending')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">نیاز به تایید</span>
                            @elseif($listing->status === 'scheduled')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">منتظر شروع</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $listing->status }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('listings.show', $listing) }}"
                               class="p-1.5 text-gray-400 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors inline-flex">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">inventory_2</span>
                            هیچ آگهی‌ای وجود ندارد
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Orders --}}
    @if($recentOrders->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">سفارشات اخیر</h3>
            <p class="text-xs text-gray-500 mt-0.5">آخرین سفارشات دریافت‌شده</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 font-semibold">
                        <th class="px-5 py-3">شماره سفارش</th>
                        <th class="px-5 py-3">خریدار</th>
                        <th class="px-5 py-3">مبلغ</th>
                        <th class="px-5 py-3">وضعیت</th>
                        <th class="px-5 py-3">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3 font-bold">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $order->buyer->name ?? '-' }}</td>
                        <td class="px-5 py-3 font-bold">
                            @persian(number_format($order->total))
                            <span class="text-xs font-normal text-gray-400">ت</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ order_status_color($order->status) }}">
                                {{ order_status_label($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
var chartData = @json($chartData);

function renderChart(data) {
    var container = document.getElementById('chart-container');
    var labelsEl  = document.getElementById('chart-labels');
    if (!container) return;

    var values = data.values;
    var labels = data.labels;
    var maxVal = data.max || 1;
    var containerH = 192; // px - matches h-48
    var hasData = values.some(function(v){ return v > 0; });

    if (!hasData) {
        container.innerHTML = '<div style="width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#9ca3af;font-size:14px;gap:8px;height:100%"><span style="font-size:36px;font-family:\'Material Symbols Outlined\'">bar_chart</span><span>هنوز فروشی در این بازه ثبت نشده</span></div>';
        labelsEl.innerHTML = '';
        return;
    }

    // Limit visible bars
    var maxBars = 30;
    if (values.length > maxBars) {
        var step = Math.ceil(values.length / maxBars);
        var sv = [], sl = [];
        for (var i = 0; i < values.length; i += step) {
            sv.push(values[i]);
            sl.push(labels[i]);
        }
        values = sv; labels = sl;
        maxVal = Math.max.apply(null, values.concat([1]));
    }

    var barsHtml = '';
    var labelsHtml = '';
    // How many labels to show (avoid crowding)
    var maxLabels = 10;
    var labelStep = values.length > maxLabels ? Math.ceil(values.length / maxLabels) : 1;

    for (var j = 0; j < values.length; j++) {
        var val = values[j];
        var barH = Math.max(4, Math.round((val / maxVal) * containerH));
        var color = val > 0 ? '#3b82f6' : '#dbeafe';
        var fullLabel = labels[j]; // e.g. 1404/2/3
        // Short label: only month/day (remove year)
        var parts = fullLabel.split('/');
        var shortLabel = parts.length >= 3 ? parts[1] + '/' + parts[2] : fullLabel;
        var tooltip = fullLabel + (val > 0 ? '\n' + Number(val).toLocaleString('fa-IR') + ' تومان' : '');

        barsHtml += '<div style="flex:1;display:flex;align-items:flex-end;min-width:0;">' +
            '<div title="' + tooltip + '" style="width:100%;height:' + barH + 'px;background:' + color + ';border-radius:3px 3px 0 0;cursor:default;opacity:0.7;transition:opacity 0.2s;" ' +
            'onmouseover="this.style.opacity=\'1\'" onmouseout="this.style.opacity=\'0.7\'"></div>' +
            '</div>';

        // Show label only every N steps to avoid crowding
        var showLabel = (j % labelStep === 0) || (j === values.length - 1);
        labelsHtml += '<div style="flex:1;text-align:center;font-size:10px;color:#9ca3af;overflow:hidden;white-space:nowrap;min-width:0;" title="' + fullLabel + '">'
            + (showLabel ? shortLabel : '') + '</div>';
    }

    container.innerHTML = barsHtml;
    labelsEl.innerHTML = labelsHtml;
}

function loadChart(days, btn) {
    // Update active button
    document.querySelectorAll('.chart-btn').forEach(function(b) {
        b.style.background = '';
        b.style.color = '';
        b.className = b.className.replace('bg-primary text-white', 'bg-gray-100 text-gray-600');
    });
    if (btn) {
        btn.className = btn.className.replace('bg-gray-100 text-gray-600', 'bg-primary text-white');
    }

    document.getElementById('chart-container').innerHTML =
        '<div style="width:100%;display:flex;align-items:center;justify-content:center;color:#9ca3af;height:100%"><span style="font-size:24px">⟳</span></div>';

    fetch('{{ route("dashboard.chart-data") }}?days=' + days, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){ renderChart(data); })
    .catch(function(){ document.getElementById('chart-container').innerHTML = '<div style="color:red;padding:20px">خطا در بارگذاری</div>'; });
}

// Initial render
document.addEventListener('DOMContentLoaded', function() {
    renderChart(chartData);
});
</script>
@endsection
