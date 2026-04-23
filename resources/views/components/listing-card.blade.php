@props(['listing'])
@php
    $cardStyle = \App\Models\HomepageSetting::get('card_style', 'classic');
@endphp

@switch($cardStyle)
    @case('modern')
        <x-listing-cards.modern :listing="$listing" />
        @break
    @case('minimal')
        <x-listing-cards.minimal :listing="$listing" />
        @break
    @case('horizontal')
        <x-listing-cards.horizontal :listing="$listing" />
        @break
    @default
        <x-listing-cards.classic :listing="$listing" />
@endswitch
