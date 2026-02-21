@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">مدیریت فروشندگان</h1>
            <p class="text-gray-600 mt-1">مدیریت درخواست‌ها و فروشندگان فعال</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- آمار -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-amber-600 font-medium">در انتظار تایید</p>
                    <p class="text-2xl font-black text-amber-900 mt-1">@persian($stats['pending'])</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-amber-500">schedule</span>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">فعال</p>
                    <p class="text-2xl font-black text-green-900 mt-1">@persian($stats['active'])</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-green-500">check_circle</span>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium">تعلیق شده</p>
                    <p class="text-2xl font-black text-red-900 mt-1">@persian($stats['suspended'])</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-red-500">block</span>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">رد شده</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">@persian($stats['rejected'])</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-gray-500">cancel</span>
            </div>
        </div>
    </div>

    <!-- فیلترها -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="جستجو (نام، ایمیل، شناسه)..."
                       class="w-full border-gray-300 rounded-lg">
            </div>
            
            <select name="status" class="border-gray-300 rounded-lg">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>همه</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>در انتظار</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>فعال</option>
                <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>تعلیق</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>رد شده</option>
            </select>

            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark">
                فیلتر
            </button>
            
            @if(request('search') || request('status') !== 'all')
                <a href="{{ route('admin.sellers.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                    پاک کردن
                </a>
            @endif
        </form>
    </div>

    <!-- لیست -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">شناسه</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">نام</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">فروشگاه</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">تاریخ درخواست</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">وضعیت</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sellers as $seller)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">@persian($seller->id)</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $seller->name }}</div>
                        <div class="text-xs text-gray-500">{{ $seller->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($seller->store)
                            <a href="{{ route('stores.show', $seller->store->slug) }}" 
                               class="text-primary hover:underline" target="_blank">
                                {{ $seller->store->name }}
                            </a>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ \Morilog\Jalali\Jalalian::fromDateTime($seller->seller_requested_at)->format('Y/m/d') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($seller->seller_status === 'pending')
                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                در انتظار
                            </span>
                        @elseif($seller->seller_status === 'active')
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                فعال
                            </span>
                        @elseif($seller->seller_status === 'suspended')
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">block</span>
                                تعلیق
                            </span>
                        @elseif($seller->seller_status === 'rejected')
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                رد شده
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.sellers.show', $seller) }}" 
                           class="text-primary hover:text-primary-dark">
                            <span class="material-symbols-outlined">visibility</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <span class="material-symbols-outlined text-6xl text-gray-300 mb-2">person_off</span>
                        <p>فروشنده‌ای یافت نشد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sellers->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
