@extends('layouts.admin')

@section('title', 'مدیریت تیکت‌ها')
@section('page-title', 'تیکت‌ها')
@section('header-title', 'مدیریت تیکت‌های پشتیبانی')
@section('header-subtitle', 'نظارت و پاسخ به تمام تیکت‌ها')

@section('content')
<div class="p-6">
    {{-- Stats --}}
    <div class="flex items-center justify-between mb-6">
        <div class="grid grid-cols-2 gap-4 flex-1">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">inbox</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">@persian($openCount)</p>
                    <p class="text-sm text-gray-500">تیکت باز</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-600">confirmation_number</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">@persian($totalCount)</p>
                    <p class="text-sm text-gray-500">کل تیکت‌ها</p>
                </div>
            </div>
        </div>
        <div class="mr-4">
            <a href="{{ route('admin.tickets.create') }}" class="flex items-center gap-2 bg-primary text-white px-4 py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-xl">add</span>
                تیکت جدید
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="جستجو در موضوع یا شماره تیکت..."
            class="flex-1 min-w-48 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
        <select name="status" class="border border-gray-300 rounded-xl px-4 py-2 text-sm">
            <option value="">همه وضعیت‌ها</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>باز</option>
            <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>پاسخ داده شده</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>بسته</option>
        </select>
        <select name="type" class="border border-gray-300 rounded-xl px-4 py-2 text-sm">
            <option value="">همه انواع</option>
            <option value="buyer_to_seller" {{ request('type') === 'buyer_to_seller' ? 'selected' : '' }}>خریدار به فروشنده</option>
            <option value="buyer_to_admin" {{ request('type') === 'buyer_to_admin' ? 'selected' : '' }}>خریدار به ادمین</option>
            <option value="seller_to_buyer" {{ request('type') === 'seller_to_buyer' ? 'selected' : '' }}>فروشنده به خریدار</option>
            <option value="seller_to_admin" {{ request('type') === 'seller_to_admin' ? 'selected' : '' }}>فروشنده به ادمین</option>
            <option value="admin_to_buyer" {{ request('type') === 'admin_to_buyer' ? 'selected' : '' }}>ادمین به خریدار</option>
            <option value="admin_to_seller" {{ request('type') === 'admin_to_seller' ? 'selected' : '' }}>ادمین به فروشنده</option>
        </select>
        <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors">
            فیلتر
        </button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        @forelse($tickets as $ticket)
            <a href="{{ route('admin.tickets.show', $ticket) }}"
                class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-600' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500') }}">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate">{{ $ticket->subject }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $ticket->ticket_number }} &bull;
                        {{ $ticket->creator->name ?? '-' }} &bull;
                        {{ $ticket->type_label }} &bull;
                        {{ $ticket->listing->title ?? '-' }}
                    </p>
                </div>
                <div class="text-left shrink-0 space-y-1">
                    <span class="block text-xs px-2 py-1 rounded-full text-center
                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $ticket->status_label }}
                    </span>
                    <p class="text-xs text-gray-400 text-center">{{ $ticket->last_reply_at?->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-gray-300 text-5xl">inbox</span>
                <p class="text-gray-500 mt-3">هیچ تیکتی یافت نشد</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $tickets->links() }}</div>
</div>
@endsection
