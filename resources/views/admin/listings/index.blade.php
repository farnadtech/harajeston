@extends('layouts.admin')

@section('title', 'مدیریت آگهی‌ها')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">مدیریت آگهی‌ها</h2>
            <p class="text-sm text-gray-500 mt-1">مشاهده و مدیریت تمام آگهی‌های سیستم</p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.listings.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 flex items-center gap-2 text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">add</span>
                ایجاد حراجی جدید
            </a>
            <button onclick="exportListings()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium">
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
                    <p class="text-sm text-gray-500">کل آگهی‌ها</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\Listing::count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">inventory_2</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">آگهی‌های فعال</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\Listing::where('status', 'active')->count()) }}
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
                    <p class="text-sm text-gray-500">در انتظار تایید</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\Listing::whereIn('status', ['draft', 'pending'])->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 text-2xl">pending</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">آگهی‌های امروز</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ \App\Services\PersianNumberService::convertToPersian(\App\Models\Listing::whereDate('created_at', today())->count()) }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 text-2xl">today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">جستجو</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="عنوان یا شناسه"
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع</label>
                <select name="type" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option value="">همه</option>
                    <option value="auction" {{ request('type') === 'auction' ? 'selected' : '' }}>مزایده</option>
                    <option value="direct_sale" {{ request('type') === 'direct_sale' ? 'selected' : '' }}>فروش مستقیم</option>
                    <option value="both" {{ request('type') === 'both' ? 'selected' : '' }}>ترکیبی</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت</label>
                <select name="status" class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option value="">همه</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>پیش‌نویس (نیاز به تایید)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار شروع</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>معلق</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>رد شده</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">فروشنده</label>
                <input type="text" 
                       name="seller" 
                       value="{{ request('seller') }}"
                       placeholder="نام فروشنده"
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 text-sm font-medium">
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.listings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">آگهی</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">نوع</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">فروشنده</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">وضعیت</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">قیمت</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاریخ</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($listings as $listing)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($listing->images->first())
                                <img src="{{ url('storage/' . $listing->images->first()->file_path) }}" 
                                     alt="{{ $listing->title }}"
                                     class="w-12 h-12 rounded-lg object-cover ml-3">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center ml-3">
                                    <span class="material-symbols-outlined text-gray-400">image</span>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $listing->title }}</div>
                                    <div class="text-xs text-gray-500">شناسه: {{ \App\Services\PersianNumberService::convertToPersian($listing->id) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($listing->type === 'auction')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">مزایده</span>
                            @elseif($listing->type === 'direct_sale')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">فروش مستقیم</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">ترکیبی</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.users.show', $listing->seller) }}" class="text-sm text-primary hover:underline">
                                {{ $listing->seller->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($listing->status === 'draft')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">نیاز به تایید</span>
                            @elseif($listing->status === 'active')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">فعال</span>
                            @elseif($listing->status === 'pending')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">در انتظار شروع</span>
                            @elseif($listing->status === 'completed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">تکمیل شده</span>
                            @elseif($listing->status === 'cancelled')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">لغو شده</span>
                            @elseif($listing->status === 'suspended')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">معلق</span>
                            @elseif($listing->status === 'rejected')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">رد شده</span>
                            @elseif($listing->status === 'ended')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">پایان یافته</span>
                            @elseif($listing->status === 'failed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">ناموفق</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">نامشخص</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                            {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->starting_price)) }} تومان
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \App\Services\JalaliDateService::toJalali($listing->created_at, 'Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.listings.show', $listing) }}" 
                                   class="text-primary hover:text-primary/80" 
                                   title="مشاهده جزئیات">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </a>
                                <a href="{{ route('admin.listings.manage', $listing) }}" 
                                   class="text-blue-600 hover:text-blue-800" 
                                   title="مدیریت">
                                    <span class="material-symbols-outlined text-[20px]">settings</span>
                                </a>
                                
                                {{-- Draft status: Show approve and reject buttons --}}
                                @if($listing->status === 'draft')
                                    <button onclick="approveListing('{{ $listing->slug }}')" 
                                            class="text-green-600 hover:text-green-800" 
                                            title="تایید و انتشار">
                                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                                    </button>
                                    <button onclick="rejectListing('{{ $listing->slug }}')" 
                                            class="text-red-600 hover:text-red-800" 
                                            title="رد آگهی">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                @endif
                                
                                {{-- Pending status: Show activate button --}}
                                @if($listing->status === 'pending')
                                    <button onclick="activateListing('{{ $listing->slug }}')" 
                                            class="text-green-600 hover:text-green-800" 
                                            title="فعال‌سازی">
                                        <span class="material-symbols-outlined text-[20px]">play_circle</span>
                                    </button>
                                @endif
                                
                                {{-- Active status: Show suspend button --}}
                                @if($listing->status === 'active')
                                    <button onclick="suspendListing('{{ $listing->slug }}')" 
                                            class="text-orange-600 hover:text-orange-800" 
                                            title="تعلیق">
                                        <span class="material-symbols-outlined text-[20px]">block</span>
                                    </button>
                                @endif
                                
                                {{-- Suspended status: Show activate button --}}
                                @if($listing->status === 'suspended')
                                    <button onclick="activateListing('{{ $listing->slug }}')" 
                                            class="text-green-600 hover:text-green-800" 
                                            title="فعال‌سازی مجدد">
                                        <span class="material-symbols-outlined text-[20px]">play_circle</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <span class="material-symbols-outlined text-6xl mb-3">inventory_2</span>
                                <p class="text-sm">آگهی یافت نشد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($listings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $listings->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
const baseUrl = '{{ url('/') }}';

function approveListing(listingId) {
    Swal.fire({
        title: 'تایید آگهی',
        text: 'آیا از تایید و انتشار این آگهی اطمینان دارید؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'بله، تایید کن',
        cancelButtonText: 'انصراف'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/admin/listings/' + listingId + '/approve', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'موفق!',
                        text: 'آگهی با موفقیت تایید و منتشر شد',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'خطا!',
                        text: data.message || 'خطا در تایید آگهی',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در ارتباط با سرور',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function rejectListing(listingId) {
    Swal.fire({
        title: 'رد آگهی',
        text: 'لطفاً دلیل رد آگهی را وارد کنید:',
        input: 'textarea',
        inputPlaceholder: 'دلیل رد...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'رد کن',
        cancelButtonText: 'انصراف',
        inputValidator: (value) => {
            if (!value) return 'لطفاً دلیل رد را وارد کنید'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/admin/listings/' + listingId + '/reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'رد شد!',
                        text: 'آگهی با موفقیت رد شد',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'خطا!',
                        text: data.message || 'خطا در رد آگهی',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در ارتباط با سرور',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function activateListing(listingId) {
    Swal.fire({
        title: 'فعال‌سازی آگهی',
        text: 'آیا از فعال‌سازی این آگهی اطمینان دارید؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'بله، فعال کن',
        cancelButtonText: 'انصراف'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/admin/listings/' + listingId + '/activate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'موفق!',
                        text: 'آگهی با موفقیت فعال شد',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'خطا!',
                        text: data.message || 'خطا در فعال‌سازی آگهی',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در ارتباط با سرور',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function suspendListing(listingId) {
    Swal.fire({
        title: 'تعلیق آگهی',
        text: 'لطفاً دلیل تعلیق را وارد کنید:',
        input: 'textarea',
        inputPlaceholder: 'دلیل تعلیق...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'تعلیق کن',
        cancelButtonText: 'انصراف',
        inputValidator: (value) => {
            if (!value) return 'لطفاً دلیل تعلیق را وارد کنید'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/admin/listings/' + listingId + '/suspend', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: result.value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'تعلیق شد!',
                        text: 'آگهی با موفقیت تعلیق شد',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: 'خطا!',
                        text: data.message || 'خطا در تعلیق آگهی',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'خطا!',
                    text: 'خطا در ارتباط با سرور',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function exportListings() {
    Swal.fire({
        title: 'به زودی',
        text: 'قابلیت خروجی Excel به زودی اضافه خواهد شد',
        icon: 'info',
        confirmButtonColor: '#3b82f6'
    });
}
</script>
@endpush
@endsection


