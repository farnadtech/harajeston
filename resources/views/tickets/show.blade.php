<x-dashboard-layout pageTitle="تیکت {{ $ticket->ticket_number }}">

<div class="p-6 max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tickets.index') }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $ticket->ticket_number }} &bull;
                {{ $ticket->type_label }} &bull;
                حراجی:
                @if($ticket->listing)
                    <a href="{{ route('listings.show', $ticket->listing) }}" class="text-primary hover:underline">{{ $ticket->listing->title }}</a>
                @else
                    -
                @endif
            </p>
        </div>
        <span class="text-sm px-3 py-1 rounded-full
            {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
            {{ $ticket->status_label }}
        </span>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Messages --}}
    <div class="space-y-4 mb-6">
        @foreach($ticket->messages as $msg)
            @php $isMe = $msg->user_id === auth()->id(); @endphp
            <div class="flex gap-3 {{ $isMe ? 'flex-row-reverse' : '' }}">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-sm font-bold
                    {{ $isMe ? 'bg-primary/20 text-primary' : ($msg->user->role === 'admin' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }}">
                    {{ mb_substr($msg->user->name ?? '?', 0, 1) }}
                </div>
                <div class="max-w-[75%]">
                    <div class="flex items-center gap-2 mb-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                        <span class="text-xs font-medium text-gray-700">
                            {{ $msg->user->name ?? 'کاربر' }}
                            @if($msg->user->role === 'admin')
                                <span class="bg-red-100 text-red-600 text-[10px] px-1.5 py-0.5 rounded-full mr-1">ادمین</span>
                            @endif
                        </span>
                        <span class="text-[11px] text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                        {{ $isMe ? 'bg-primary text-white rounded-tl-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-tr-sm' }}">
                        {!! nl2br(e($msg->message)) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reply Form --}}
    @if(!$ticket->isClosed())
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-2">پاسخ شما</label>
                <textarea name="message" rows="4"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary resize-none mb-3"
                    placeholder="پیام خود را بنویسید..." required maxlength="5000"></textarea>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-primary/90 transition-colors text-sm">
                        ارسال پاسخ
                    </button>
                    @if($ticket->creator_id === auth()->id())
                        <form action="{{ route('tickets.close', $ticket) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors"
                                onclick="return confirm('آیا از بستن تیکت مطمئن هستید؟')">
                                بستن تیکت
                            </button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-center text-sm text-gray-500">
            <span class="material-symbols-outlined text-gray-400 text-3xl block mb-2">lock</span>
            این تیکت بسته شده است.
        </div>
    @endif
</div>

</x-dashboard-layout>
