@extends('layouts.admin')

@section('title', 'مدیریت روش‌های ارسال')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">مدیریت روش‌های ارسال</h2>
            <p class="text-sm text-gray-500 mt-1">تعریف و مدیریت روش‌های ارسال کالا</p>
        </div>
        
        <a href="{{ route('admin.shipping-methods.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 flex items-center gap-2 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">add</span>
            افزودن روش جدید
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">کل روش‌ها</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian($shippingMethods->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">local_shipping</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">روش‌های فعال</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian($shippingMethods->where('is_active', true)->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">میانگین هزینه</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(number_format($shippingMethods->avg('base_cost'))) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 text-2xl">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Methods Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">شناسه</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">نام روش ارسال</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">هزینه پایه</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">مدت زمان تحویل</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($shippingMethods as $method)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ \App\Services\PersianNumberService::convertToPersian($method->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $method->name }}</div>
                                    @if($method->description)
                                        <div class="text-xs text-gray-500">{{ Str::limit($method->description, 40) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ \App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost)) }} تومان
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($method->estimated_days)
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">schedule</span>
                                    {{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز کاری
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($method->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">فعال</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">غیرفعال</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.shipping-methods.edit', $method) }}" 
                                   class="text-primary hover:text-primary/80" 
                                   title="ویرایش">
                                    <span class="material-symbols-outlined text-[20px]">edit</span>
                                </a>
                                <button onclick="toggleStatus({{ $method->id }}, {{ $method->is_active ? 'false' : 'true' }})" 
                                        class="{{ $method->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}" 
                                        title="{{ $method->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                    <span class="material-symbols-outlined text-[20px]">
                                        {{ $method->is_active ? 'toggle_on' : 'toggle_off' }}
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-6xl mb-3">local_shipping</span>
                                <p class="text-sm">روش ارسالی یافت نشد</p>
                                <a href="{{ route('admin.shipping-methods.create') }}" class="mt-4 text-primary hover:underline text-sm font-medium">
                                    افزودن اولین روش ارسال
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(methodId, newStatus) {
    const action = newStatus === 'true' ? 'فعال' : 'غیرفعال';
    
    // ایجاد پاپ‌آپ سفارشی
    const popup = document.createElement('div');
    popup.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4';
    popup.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 animate-scale-in">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">help</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900">تایید عملیات</h3>
            </div>
            <p class="text-gray-600 mb-6">آیا از ${action} کردن این روش ارسال اطمینان دارید؟</p>
            <div class="flex gap-3">
                <button onclick="confirmToggle(${methodId}, '${newStatus}')" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-medium">
                    تایید
                </button>
                <button onclick="this.closest('.fixed').remove()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    انصراف
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(popup);
}

function confirmToggle(methodId, newStatus) {
    // حذف پاپ‌آپ
    document.querySelector('.fixed.inset-0').remove();
    
    fetch(`/haraj/public/admin/shipping-methods/${methodId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ is_active: newStatus === 'true' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('خطا در تغییر وضعیت');
    });
}
</script>
@endpush
@endsection
