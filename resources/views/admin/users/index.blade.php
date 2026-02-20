@extends('layouts.admin')

@section('title', 'مدیریت کاربران')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">مدیریت کاربران</h2>
            <p class="text-sm text-gray-500 mt-1">مشاهده و مدیریت تمام کاربران سیستم</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="exportUsers()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">download</span>
                خروجی Excel
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">کل کاربران</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\User::count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">group</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">فروشندگان</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\User::where('role', 'seller')->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 text-2xl">storefront</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">خریداران</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\User::where('role', 'buyer')->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 text-2xl">shopping_bag</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">کاربران امروز</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\User::whereDate('created_at', today())->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 text-2xl">person_add</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="نام، ایمیل یا شماره تماس"
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نقش</label>
                <select name="role" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option value="">همه</option>
                    <option value="buyer" {{ request('role') === 'buyer' ? 'selected' : '' }}>خریدار</option>
                    <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>فروشنده</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>ادمین</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select name="status" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option value="">همه</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 text-sm font-medium">
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">کاربر</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">نقش</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">موجودی کیف پول</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاریخ عضویت</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ mb_substr($user->name, 0, 2) }}
                                </div>
                                <div class="mr-3">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">ادمین</span>
                            @elseif($user->role === 'seller')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">فروشنده</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">خریدار</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->email_verified_at)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">تایید شده</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">در انتظار تایید</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ \App\Services\PersianNumberService::convertToPersian(number_format($user->wallet->balance ?? 0)) }} تومان
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" dir="ltr">
                            {{ \App\Services\JalaliDateService::toJalali($user->created_at, 'Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="text-primary hover:text-primary/80" 
                                   title="مشاهده جزئیات">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                                @if(!$user->email_verified_at)
                                <button onclick="verifyEmail({{ $user->id }})" 
                                        class="text-green-600 hover:text-green-800" 
                                        title="تایید ایمیل">
                                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-6xl mb-3">person_off</span>
                                <p class="text-sm">کاربری یافت نشد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function verifyEmail(userId) {
    if (confirm('آیا از تایید ایمیل این کاربر اطمینان دارید؟')) {
        fetch(`/admin/users/${userId}/verify-email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function exportUsers() {
    alert('قابلیت خروجی Excel به زودی اضافه خواهد شد');
}
</script>
@endpush
@endsection
