<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', 'حراج‌استون');
        $siteTagline = \App\Models\SiteSetting::get('site_tagline', '');
        $siteFavicon = \App\Models\SiteSetting::get('site_favicon', '');
        $faviconUrl = $siteFavicon ? rtrim(config('app.url'), '/') . '/storage/' . $siteFavicon : '';
        $primaryColor = \App\Models\SiteSetting::get('color_primary', '#135bec');
        $primaryHover = \App\Models\SiteSetting::get('color_primary_hover', '#0e4bc7');
        $siteLogo = \App\Models\SiteSetting::get('site_logo', '');
        $siteIcon = \App\Models\SiteSetting::get('site_icon', 'gavel');
    @endphp
    <title>فراموشی رمز عبور | {{ $siteName }}{{ $siteTagline ? ' - ' . $siteTagline : '' }}</title>
    @if($faviconUrl)<link rel="icon" type="image/png" href="{{ $faviconUrl }}"><link rel="shortcut icon" href="{{ $faviconUrl }}">@endif
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
    :root { --color-primary: {{ $primaryColor }}; --color-primary-hover: {{ $primaryHover }}; }
    .text-primary { color: var(--color-primary) !important; }
    .bg-primary { background-color: var(--color-primary) !important; }
    .bg-primary\/10 { background-color: color-mix(in srgb, var(--color-primary) 10%, transparent) !important; }
    .hover\:bg-primary-hover:hover { background-color: var(--color-primary-hover) !important; }
    .focus\:border-primary:focus { border-color: var(--color-primary) !important; }
    .shadow-blue-500\/30 { box-shadow: 0 4px 14px color-mix(in srgb, var(--color-primary) 30%, transparent) !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 sm:p-12">

        {{-- لوگو --}}
        <div class="flex items-center gap-3 mb-8">
            @if($siteLogo)
                <img src="{{ rtrim(config('app.url'), '/') . '/storage/' . $siteLogo }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-primary\/10">
                    <span class="material-symbols-outlined text-2xl text-primary">{{ $siteIcon }}</span>
                </div>
            @endif
            <h1 class="text-xl font-black tracking-tight text-primary">{{ $siteName }}</h1>
        </div>

        <div class="mb-8 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-primary\/10">
                <span class="material-symbols-outlined text-4xl text-primary">lock_reset</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">فراموشی رمز عبور</h2>
            <p class="text-sm text-gray-500">شماره موبایل یا ایمیل خود را وارد کنید تا کد تایید ارسال شود</p>
        </div>

        @if(session('status'))
            <div class="bg-green-50 border-r-4 border-green-500 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-700 font-medium">{{ session('status') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.otp.send') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره موبایل یا ایمیل</label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:border-primary transition-colors pl-10"
                           name="identifier"
                           value="{{ old('identifier') }}"
                           placeholder="09123456789 یا example@email.com"
                           type="text"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">person</span>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-primary text-white font-bold py-3.5 rounded-xl shadow-blue-500\/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                <span>ارسال کد تایید</span>
                <span class="material-symbols-outlined text-lg">send</span>
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-primary flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                بازگشت به صفحه ورود
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© {{ date('Y') }} {{ $siteName }} - تمامی حقوق محفوظ است</p>
        </div>
    </div>
</body>
</html>
