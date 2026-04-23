@props(['listing'])
@php
    $displayPrice = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price;
    $isActive = $listing->status === 'active' && ($listing->ends_at === null || $listing->ends_at->isFuture());
    $isEnded  = in_array($listing->status, ['ended','completed']);
@endphp
<div class="rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group bg-white border border-gray-100 flex flex-col">

    {{-- Image --}}
    <a href="{{ route('listings.show', $listing) }}" class="relative block overflow-hidden" style="height:200px; flex-shrink:0;">
        @if($listing->images->isNotEmpty())
            <img alt="{{ $listing->title }}"
                 src="{{ url('storage/' . $listing->images->first()->file_path) }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-blue-300">image</span>
            </div>
        @endif
        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

        {{-- Status badge --}}
        @if($isActive)
            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">فعال</span>
        @elseif($isEnded)
            <span class="absolute top-3 right-3 bg-gray-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">پایان یافته</span>
        @endif

        {{-- Category --}}
        @if($listing->category)
            <span class="absolute top-3 left-3 bg-black/40 backdrop-blur-sm text-white text-xs px-2 py-0.5 rounded-full">
                {{ $listing->category->name }}
            </span>
        @endif

        {{-- Title on image --}}
        <div class="absolute bottom-0 right-0 left-0 p-4">
            <h3 class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow">{{ $listing->title }}</h3>
        </div>
    </a>

    {{-- Bottom --}}
    <div class="p-4 flex items-center justify-between mt-auto">
        <div>
            <p class="text-xs text-gray-400 mb-0.5">قیمت فعلی</p>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-black text-primary leading-none">
                    {{ \App\Services\PersianNumberService::convertToPersian(number_format($displayPrice)) }}
                </span>
                <span class="text-xs text-gray-400">تومان</span>
            </div>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span class="flex items-center gap-1 text-xs text-gray-400">
                <span class="material-symbols-outlined" style="font-size:14px;">gavel</span>
                {{ \App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0) }} پیشنهاد
            </span>
            <a href="{{ route('listings.show', $listing) }}"
               class="text-xs font-bold px-4 py-2 rounded-xl transition-all
                      {{ $isActive ? 'bg-primary text-white hover:bg-blue-600 hover:shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $isActive ? 'شرکت در مزایده' : 'مشاهده' }}
            </a>
        </div>
    </div>
</div>
