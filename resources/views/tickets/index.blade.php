<x-dashboard-layout pageTitle="تیکت‌های پشتیبانی">

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تیکت‌های پشتیبانی</h1>
            <p class="text-sm text-gray-500 mt-1">مدیریت درخواست‌ها و مکاتبات</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl font-medium hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-xl">add</span>
            تیکت جدید
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        @forelse($tickets as $ticket)
            @php $unread = $ticket->unreadCountFor(auth()->id()); @endphp
            <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-600' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500') }}">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-{{ $unread > 0 ? 'bold' : 'medium' }} text-gray-900 truncate">{{ $ticket->subject }}</p>
                        @if($unread > 0)
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full shrink-0">{{ $unread }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $ticket->ticket_number }} &bull;
                        {{ $ticket->listing->title ?? '-' }} &bull;
                        {{ $ticket->type_label }}
                    </p>
                </div>
                <div class="text-left shrink-0">
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $ticket->status_label }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $ticket->last_reply_at?->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-gray-300 text-5xl">inbox</span>
                <p class="text-gray-500 mt-3">هیچ تیکتی وجود ندارد</p>
                <a href="{{ route('tickets.create') }}" class="mt-4 inline-flex items-center gap-2 text-primary font-medium hover:underline">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    ایجاد اولین تیکت
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $tickets->links() }}</div>
</div>

</x-dashboard-layout>
