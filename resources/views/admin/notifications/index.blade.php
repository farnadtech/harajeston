@extends('layouts.admin')

@section('title', 'اعلان‌ها')
@section('header-title', 'مدیریت اعلان‌ها')
@section('header-subtitle', 'مشاهده و مدیریت تمام اعلان‌های سیستم')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">همه اعلان‌ها</h2>
            @if($notifications->where('is_read', false)->count() > 0)
                <button onclick="markAllAsRead()" class="text-sm text-primary hover:text-blue-700 font-medium">
                    علامت‌گذاری همه به عنوان خوانده شده
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <a href="{{ $notification->link ? route('admin.notifications.read', $notification->id) : '#' }}" 
                   class="block px-6 py-4 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-{{ $notification->color }}-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-{{ $notification->color }}-600 text-2xl">{{ $notification->icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <p class="text-base font-medium text-gray-900">{{ $notification->title }}</p>
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-2">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400">{{ $notification->time_ago }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-6xl mb-3 block">notifications_off</span>
                    <p class="text-gray-500">اعلانی وجود ندارد</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAllAsRead() {
    fetch('{{ route('admin.notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('همه اعلان‌ها به عنوان خوانده شده علامت‌گذاری شدند', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('خطا در علامت‌گذاری اعلان‌ها', 'error');
    });
}
</script>
@endpush
@endsection
