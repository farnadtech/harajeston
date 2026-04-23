@props(['listing'])
@php
    $displayPrice = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price;
    $isActive = $listing->status === 'active' && ($listing->ends_at === null || $listing->ends_at->isFuture());
@endphp
<div class="bg-white rounded-xl overflow-hidden hover:shadow-md transition-all duration-200 group border border-gray-100 hover:border-primary/20 flex flex-col">

    {{-- Image --}}
    <a href="{{ route('listings.show', $listing) }}" class="relative block overflow-hidden" style="height:180px; flex-shrink:0;">
        @if($listing->images->isNotEmpty())
            <img alt="{{ $listing->title }}"
                 src="{{ url('storage/' . $listing->images->first()->file_path) }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-gray-200">image</span>
            </div>
        @endif
        @if($isActive)
            <span class="absolute top-2 right-2 w-2 h-2 bg-green-500 rounded-full ring-2 ring-white"></span>
        @endif
    </a>

    {{-- Content --}}
    <div class="p-3 flex flex-col flex-1">
        <a href="{{ route('listings.show', $listing) }}">
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 group-hover:text-primary transition-colors leading-snug mb-2">
                {{ $listing->title }}
            </h3>
        </a>

        @if($listing->category)
            <span class="text-xs text-gray-400 mb-2">{{ $listing->category->name }}</span>
        @endif

        <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-between">
            <div>
                <span class="text-base font-black text-gray-900">
                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($displayPrice)) }}
                </span>
                <span class="text-xs text-gray-400 mr-0.5">تومان</span>
            </div>
            <a href="{{ route('listings.show', $listing) }}"
               class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors
                      {{ $isActive ? 'text-primary border border-primary hover:bg-primary hover:text-white' : 'text-gray-400 border border-gray-200' }}">
                {{ $isActive ? 'پیشنهاد' : 'مشاهده' }}
            </a>
        </div>

        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
            <span class="flex items-center gap-0.5">
                <span class="material-symbols-outlined" style="font-size:13px;">gavel</span>
                {{ \App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0) }}
            </span>
            @if($listing->ends_at && $isActive)
                <span class="flex items-center gap-0.5 mr-auto">
                    <span class="material-symbols-outlined" style="font-size:13px;">schedule</span>
                    @php
                        $h = (int)now()->diffInHours($listing->ends_at);
                        $d = (int)now()->diffInDays($listing->ends_at);
                    @endphp
                    {{ $d > 0 ? \App\Services\PersianNumberService::convertToPersian($d).' روز' : \App\Services\PersianNumberService::convertToPersian($h).' ساعت' }}
                </span>
            @endif
        </div>
    </div>
</div>
