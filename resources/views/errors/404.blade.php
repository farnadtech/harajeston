<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @php
        $siteName    = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
        $siteTagline = \App\Models\SiteSetting::get('site_tagline', '');
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon', '');
        $faviconUrl  = $siteFavicon ? rtrim(config('app.url'), '/') . '/storage/' . $siteFavicon : '';
        $primaryColor = \App\Models\SiteSetting::get('color_primary', '#135bec');
    @endphp
    <title>صفحه پیدا نشد | {{ $siteName }}</title>
    @if($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
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
    :root { --color-primary: {{ $primaryColor }}; }
    .text-primary { color: var(--color-primary) !important; }
    .bg-primary { background-color: var(--color-primary) !important; }
    .border-primary { border-color: var(--color-primary) !important; }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(2); opacity: 0; }
    }
    .float-anim { animation: float 3s ease-in-out infinite; }
    .pulse-ring {
        position: absolute;
        border-radius: 50%;
        border: 2px solid var(--color-primary);
        animation: pulse-ring 2s ease-out infinite;
    }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-6 text-gray-900">

    {{-- Background decoration --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-20 w-64 h-64 rounded-full opacity-5" style="background: var(--color-primary);"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 rounded-full opacity-5" style="background: var(--color-primary);"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-3" style="background: var(--color-primary);"></div>
    </div>

    <div class="relative z-10 text-center max-w-lg w-full">

        {{-- Logo --}}
        <a href="{{ route('listings.index') }}" class="inline-flex items-center gap-2 mb-12 text-primary font-black text-xl">
            @php $siteLogo = \App\Models\SiteSetting::get('site_logo', ''); $siteIcon = \App\Models\SiteSetting::get('site_icon', 'gavel'); @endphp
            @if($siteLogo)
                <img src="{{ rtrim(config('app.url'), '/') . '/storage/' . $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-auto object-contain">
            @else
                <span class="material-symbols-outlined text-2xl text-primary">{{ $siteIcon }}</span>
            @endif
            <span class="text-primary">{{ $siteName }}</span>
        </a>

        {{-- 404 Number --}}
        <div class="relative inline-block mb-8">
            <div class="pulse-ring w-40 h-40" style="top: 50%; left: 50%; margin: -80px 0 0 -80px;"></div>
            <div class="float-anim relative">
                <div class="w-40 h-40 rounded-full flex items-center justify-center mx-auto" style="background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 15%, transparent), color-mix(in srgb, var(--color-primary) 5%, transparent)); border: 2px solid color-mix(in srgb, var(--color-primary) 20%, transparent);">
                    <span class="material-symbols-outlined text-7xl text-primary" style="font-size: 72px;">search_off</span>
                </div>
            </div>
        </div>

        {{-- Error code --}}
        <div class="mb-4">
            <span class="text-8xl font-black text-primary leading-none">۴۰۴</span>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-bold text-gray-900 mb-3">صفحه پیدا نشد!</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">
            صفحه‌ای که دنبالش می‌گردید وجود ندارد، حذف شده یا آدرسش تغییر کرده.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('listings.index') }}"
               class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-primary text-white font-bold rounded-xl transition-all hover:opacity-90 active:scale-95">
                <span class="material-symbols-outlined text-lg">home</span>
                بازگشت به صفحه اصلی
            </a>
            <button onclick="history.back()"
                    class="inline-flex items-center justify-center gap-2 px-8 py-3 border-2 border-primary text-primary font-bold rounded-xl transition-all hover:bg-primary hover:text-white active:scale-95">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                صفحه قبلی
            </button>
        </div>

        {{-- Quick links --}}
        <div class="mt-10 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-400 mb-4">شاید این لینک‌ها کمک کنن:</p>
            <div class="flex flex-wrap gap-2 justify-center">
                <a href="{{ route('listings.index') }}" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-600 hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle">gavel</span> حراجی‌ها
                </a>
                @auth
                <a href="{{ route('dashboard') }}" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-600 hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle">dashboard</span> داشبورد
                </a>
                @else
                <a href="{{ route('login') }}" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-600 hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle">login</span> ورود
                </a>
                @endauth
                <a href="{{ route('categories.index') }}" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-600 hover:border-primary hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-sm align-middle">category</span> دسته‌بندی‌ها
                </a>
            </div>
        </div>

    </div>

</body>
</html>
