@php
    $isExpired = $listing->status === 'active' && $listing->ends_at && \Carbon\Carbon::now()->greaterThanOrEqualTo($listing->ends_at);
    $isEnded   = in_array($listing->status, ['ended', 'completed']) || $isExpired;
@endphp

@if($listing->status === 'suspended')
    <button disabled class="block w-full py-2.5 bg-red-100 text-red-700 text-sm font-bold rounded-lg cursor-not-allowed text-center border border-red-300">
        تعلیق شده
    </button>
@elseif($listing->status === 'pending' && !$listing->approved_at)
    <button disabled class="block w-full py-2.5 bg-orange-100 text-orange-700 text-sm font-bold rounded-lg cursor-not-allowed text-center border border-orange-300">
        منتظر تایید ادمین
    </button>
@elseif($listing->status === 'pending')
    <button disabled class="block w-full py-2.5 bg-gray-300 text-gray-600 text-sm font-bold rounded-lg cursor-not-allowed text-center">
        هنوز شروع نشده
    </button>
@elseif($isEnded)
    <a href="{{ route('listings.show', $listing) }}" class="block w-full py-2.5 bg-gray-400 text-white text-sm font-bold rounded-lg hover:bg-gray-500 transition text-center">
        مشاهده نتیجه
    </a>
@else
    <a href="{{ route('listings.show', $listing) }}" class="block w-full py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition shadow-lg shadow-blue-500/20 text-center">
        ثبت پیشنهاد
    </a>
@endif
