@php
    $siteName = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
    $siteLogo = \App\Models\SiteSetting::get('site_logo', '');
    $siteIcon = \App\Models\SiteSetting::get('site_icon', 'gavel');
    $primaryColor = \App\Models\SiteSetting::get('color_primary', '#135bec');
    $primaryHover = \App\Models\SiteSetting::get('color_primary_hover', '#0e4bc7');
@endphp
<style>
:root { --color-primary: {{ $primaryColor }}; --color-primary-hover: {{ $primaryHover }}; }
.text-primary { color: var(--color-primary) !important; }
.bg-primary { background-color: var(--color-primary) !important; }
.border-primary { border-color: var(--color-primary) !important; }
.bg-primary\/10 { background-color: color-mix(in srgb, var(--color-primary) 10%, transparent) !important; }
.focus\:border-primary:focus { border-color: var(--color-primary) !important; }
.hover\:bg-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
.shadow-blue-500\/30 { box-shadow: 0 4px 14px color-mix(in srgb, var(--color-primary) 30%, transparent) !important; }
</style>
<div class="flex items-center gap-3 mb-8">
    @if($siteLogo)
        <img src="{{ rtrim(config('app.url'), '/') . '/storage/' . $siteLogo }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
    @else
        <div class="w-10 h-10 bg-primary\/10 rounded-xl flex items-center justify-center" style="background-color: color-mix(in srgb, {{ $primaryColor }} 10%, transparent);">
            <span class="material-symbols-outlined text-2xl" style="color: {{ $primaryColor }};">{{ $siteIcon }}</span>
        </div>
    @endif
    <h1 class="text-xl font-black tracking-tight" style="color: {{ $primaryColor }};">{{ $siteName }}</h1>
</div>
