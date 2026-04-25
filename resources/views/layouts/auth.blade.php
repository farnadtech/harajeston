<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
        $siteTagline = \App\Models\SiteSetting::get('site_tagline', '');
        $siteLogo = \App\Models\SiteSetting::get('site_logo', '');
        $siteIcon = \App\Models\SiteSetting::get('site_icon', 'gavel');
        $primaryColor = \App\Models\SiteSetting::get('color_primary', '#135bec');
        $primaryHover = \App\Models\SiteSetting::get('color_primary_hover', '#0e4bc7');
    @endphp
    <title>@yield('title', $siteName) - {{ $siteName }}</title>
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>
    @font-face {
        font-family: 'Material Symbols Outlined';
        font-style: normal;
        font-weight: 100 700;
        font-display: block;
        src: url('/haraj/public/fonts/MaterialSymbolsOutlined[FILL,GRAD,opsz,wght].woff2') format('woff2');
    }
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
    body { font-family: 'Vazirmatn', sans-serif; }
    :root {
        --color-primary: {{ $primaryColor }};
        --color-primary-hover: {{ $primaryHover }};
    }
    .text-primary { color: var(--color-primary) !important; }
    .bg-primary { background-color: var(--color-primary) !important; }
    .border-primary { border-color: var(--color-primary) !important; }
    .bg-primary\/10 { background-color: color-mix(in srgb, var(--color-primary) 10%, transparent) !important; }
    .ring-primary\/20 { --tw-ring-color: color-mix(in srgb, var(--color-primary) 20%, transparent) !important; }
    .focus\:border-primary:focus { border-color: var(--color-primary) !important; }
    .hover\:bg-primary:hover { background-color: var(--color-primary-hover) !important; }
    .shadow-blue-500\/30 { box-shadow: 0 4px 14px color-mix(in srgb, var(--color-primary) 30%, transparent) !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-md p-8 sm:p-12">

        {{-- لوگو و نام سایت --}}
        <div class="flex items-center gap-3 mb-8">
            @if($siteLogo)
                <img src="{{ url('storage/' . $siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: color-mix(in srgb, {{ $primaryColor }} 10%, transparent);">
                    <span class="material-symbols-outlined text-2xl" style="color: {{ $primaryColor }};">{{ $siteIcon }}</span>
                </div>
            @endif
            <h1 class="text-xl font-black tracking-tight" style="color: {{ $primaryColor }};">{{ $siteName }}</h1>
        </div>

        @yield('content')

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© {{ \Morilog\Jalali\Jalalian::now()->format('Y') }} {{ $siteName }} - تمامی حقوق محفوظ است</p>
        </div>
    </div>
</body>
</html>
