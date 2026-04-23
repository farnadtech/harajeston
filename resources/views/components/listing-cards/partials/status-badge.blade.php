@if($listing->status === 'suspended')
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">block</span> تعلیق
        </span>
    </div>
@elseif($listing->status === 'pending' && !$listing->approved_at)
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full shadow">منتظر تایید</span>
    </div>
@elseif($listing->status === 'pending' && $listing->approved_at && $listing->starts_at && $listing->starts_at->isFuture())
    @php
        $now = \Carbon\Carbon::now();
        $d = (int)$now->diffInDays($listing->starts_at);
        $h = (int)$now->diffInHours($listing->starts_at);
        $m = (int)$now->diffInMinutes($listing->starts_at);
        if ($d > 0) $t = \App\Services\PersianNumberService::convertToPersian($d).' روز تا شروع';
        elseif ($h > 0) $t = \App\Services\PersianNumberService::convertToPersian($h).' ساعت تا شروع';
        elseif ($m > 0) $t = \App\Services\PersianNumberService::convertToPersian($m).' دقیقه تا شروع';
        else $t = 'در حال شروع...';
    @endphp
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow">{{ $t }}</span>
    </div>
@elseif(in_array($listing->status, ['ended','completed']))
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-gray-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">check_circle</span> تمام شده
        </span>
    </div>
@elseif($listing->status === 'active' && $listing->ends_at)
    @php
        $now = \Carbon\Carbon::now();
        $hoursLeft = (int)$now->diffInHours($listing->ends_at);
        $daysLeft = (int)$now->diffInDays($listing->ends_at);
        $minsLeft = (int)$now->diffInMinutes($listing->ends_at);
    @endphp
    @if($now->greaterThanOrEqualTo($listing->ends_at))
    {{-- active ولی زمانش تموم شده - همون استایل ended --}}
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-gray-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">check_circle</span> تمام شده
        </span>
    </div>
    @else
    @php
        if ($daysLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($daysLeft).' روز مانده';
        elseif ($hoursLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($hoursLeft).' ساعت مانده';
        elseif ($minsLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($minsLeft).' دقیقه مانده';
        else $timeStr = 'کمتر از یک دقیقه';
    @endphp
    <div class="absolute top-3 left-3 z-10">
        <span class="px-2 py-1 {{ $hoursLeft < 3 ? 'bg-red-500 animate-pulse' : 'bg-orange-500' }} text-white text-xs font-bold rounded-md shadow">
            {{ $timeStr }}
        </span>
    </div>
    @endif
@endif
