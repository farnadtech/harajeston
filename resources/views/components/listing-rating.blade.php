@props(['listing', 'size' => 'sm'])

@if($listing->rating_count > 0)
    <div class="flex items-center gap-1">
        @for($i = 1; $i <= 5; $i++)
            <span class="text-{{ $i <= round($listing->average_rating) ? 'yellow-400' : 'gray-300' }} text-{{ $size === 'lg' ? 'lg' : 'xs' }}">★</span>
        @endfor
        <span class="text-{{ $size === 'lg' ? 'sm' : 'xs' }} text-gray-600 font-medium mr-1">
            {{ number_format($listing->average_rating, 1) }}
            @if($size === 'lg')
                ({{ \App\Services\PersianNumberService::convertToPersian($listing->rating_count) }})
            @endif
        </span>
    </div>
@endif
