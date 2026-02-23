@props(['listing'])

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group relative">
    {{-- Status Badge --}}
    @if($listing->status === 'suspended')
        <div class="absolute top-3 right-3 z-10">
            <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">block</span>
                تعلیق شده
            </span>
        </div>
    @elseif($listing->status === 'pending' && $listing->starts_at && $listing->starts_at->isFuture())
        @php
            $now = \Carbon\Carbon::now();
            $diff = $now->diff($listing->starts_at);
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;
            
            if ($days > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($days) . ' روز تا شروع';
            } elseif ($hours > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت تا شروع';
            } elseif ($minutes > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه تا شروع';
            } else {
                $timeUntilStart = 'در حال شروع...';
            }
        @endphp
        <div class="absolute top-3 right-3 z-10">
            <span class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-lg">
                {{ $timeUntilStart }}
            </span>
        </div>
    @elseif($listing->status === 'active' && $listing->ends_at)
        @php
            $hoursLeft = $listing->ends_at->diffInHours(now());
        @endphp
        <div class="absolute top-3 left-3 z-10">
            <span class="px-2 py-1 {{ $hoursLeft < 3 ? 'bg-red-500 animate-pulse' : 'bg-orange-500' }} text-white text-xs font-bold rounded-md shadow-sm">
                @php
                    $now = \Carbon\Carbon::now();
                    if ($now->greaterThanOrEqualTo($listing->ends_at)) {
                        echo 'پایان یافته';
                    } else {
                        $diff = $now->diff($listing->ends_at);
                        $days = $diff->d;
                        $hours = $diff->h;
                        $minutes = $diff->i;
                        
                        if ($days > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($days) . ' روز مانده';
                        } elseif ($hours > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت مانده';
                        } elseif ($minutes > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه مانده';
                        } else {
                            echo 'کمتر از یک دقیقه';
                        }
                    }
                @endphp
            </span>
        </div>
    @elseif($listing->status === 'completed')
    <div class="absolute top-3 right-3 z-10">
        <span class="px-3 py-1.5 bg-gray-500 text-white text-xs font-bold rounded-full shadow-lg">
            تمام شده
        </span>
    </div>
    @endif

    @if($listing->buy_now_price)
        <div class="absolute top-3 {{ ($listing->status === 'pending' && $listing->starts_at && $listing->starts_at->isFuture()) ? 'left-3' : 'right-3' }} z-10">
            <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-md shadow-sm flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">bolt</span>
                خرید فوری
            </span>
        </div>
    @endif

    {{-- Image --}}
    <a href="{{ route('listings.show', $listing) }}" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
        @if($listing->images->isNotEmpty())
            <img alt="{{ $listing->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" src="{{ url('storage/' . $listing->images->first()->file_path) }}"/>
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <span class="material-symbols-outlined text-6xl">image</span>
            </div>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-4">
        {{-- Category Badge --}}
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
                {{ $listing->category ? $listing->category->name : 'بدون دسته' }}
            </span>
        </div>

        {{-- Title --}}
        <a href="{{ route('listings.show', $listing) }}">
            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors line-clamp-1">
                {{ $listing->title }}
            </h3>
        </a>

        {{-- Price --}}
        <div class="flex items-baseline gap-2 mb-3">
            <span class="text-2xl font-black text-primary">
                {{ \App\Services\PersianNumberService::convertToPersian(number_format($listing->current_price ?? $listing->starting_price)) }}
            </span>
            <span class="text-sm text-gray-500">تومان</span>
        </div>

        {{-- Stats --}}
        <div class="flex items-center justify-between text-xs text-gray-500 mb-3 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">gavel</span>
                <span>{{ \App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0) }} پیشنهاد</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">visibility</span>
                <span>{{ \App\Services\PersianNumberService::convertToPersian($listing->views) }} بازدید</span>
            </div>
        </div>

        {{-- Action Button --}}
        @if($listing->status === 'suspended')
            <button disabled class="block w-full py-2.5 bg-red-100 text-red-700 text-sm font-bold rounded-lg cursor-not-allowed text-center border border-red-300">
                این آگهی تعلیق شده است
            </button>
        @elseif($listing->status === 'pending')
            <button disabled class="block w-full py-2.5 bg-gray-300 text-gray-600 text-sm font-bold rounded-lg cursor-not-allowed text-center">
                هنوز شروع نشده
            </button>
        @elseif($listing->status === 'completed')
            <a href="{{ route('listings.show', $listing) }}" class="block w-full py-2.5 bg-gray-400 text-white text-sm font-bold rounded-lg hover:bg-gray-500 transition-colors shadow-lg text-center">
                مشاهده نتیجه
            </a>
        @else
            <a href="{{ route('listings.show', $listing) }}" class="block w-full py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20 text-center">
                ثبت پیشنهاد
            </a>
        @endif
    </div>
</div>
