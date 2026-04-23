@props(['listing'])
{{-- کارت کلاسیک - همان طرح فعلی --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group relative">
    @include('components.listing-cards.partials.status-badge', ['listing' => $listing])

    <a href="{{ route('listings.show', $listing) }}" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
        @if($listing->images->isNotEmpty())
            <img alt="{{ $listing->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                 src="{{ url('storage/' . $listing->images->first()->file_path) }}"/>
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <span class="material-symbols-outlined text-6xl">image</span>
            </div>
        @endif
    </a>

    <div class="p-4">
        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
            {{ $listing->category ? $listing->category->name : 'بدون دسته' }}
        </span>
        <a href="{{ route('listings.show', $listing) }}">
            <h3 class="text-lg font-bold text-gray-900 mt-2 mb-1 group-hover:text-primary transition-colors line-clamp-1">
                {{ $listing->title }}
            </h3>
        </a>
        @php $displayPrice = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price; @endphp
        <div class="flex items-baseline gap-2 mb-3">
            <span class="text-2xl font-black text-primary">{{ \App\Services\PersianNumberService::convertToPersian(number_format($displayPrice)) }}</span>
            <span class="text-sm text-gray-500">تومان</span>
        </div>
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
        @include('components.listing-cards.partials.action-button', ['listing' => $listing])
    </div>
</div>
