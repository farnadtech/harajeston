<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تغییر رمز عبور - حراج‌استون</title>
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
    </style>
    <style>
        body {
            font-family: 'Vazirmatn', 'Manrope', sans-serif;
        }
    </style>
</head>
<body class="bg-background-light text-[#0d121b] antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-md p-8 sm:p-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h1 class="text-xl font-black tracking-tight text-[#0d121b]">
                حراج<span class="text-primary">استون</span>
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl">key</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">تغییر رمز عبور</h2>
            <p class="text-sm text-gray-500 text-center">رمز عبور جدید خود را وارد کنید</p>
        </div>
        
        @if($errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <span class="material-symbols-outlined text-red-500 ml-3">error</span>
                    <div class="flex-1">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل</label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="email" 
                           value="{{ old('email', $email) }}"
                           placeholder="example@email.com" 
                           type="email"
                           readonly
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">mail</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور جدید</label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" 
                           name="password"
                           placeholder="حداقل ۸ کاراکتر" 
                           type="password"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">lock</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تکرار رمز عبور جدید</label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" 
                           name="password_confirmation"
                           placeholder="حداقل ۸ کاراکتر" 
                           type="password"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">lock</span>
                </div>
            </div>
            
            <button class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                <span>تغییر رمز عبور</span>
                <span class="material-symbols-outlined text-lg">check_circle</span>
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a class="text-sm font-bold text-primary hover:text-primary-hover flex items-center justify-center gap-2" href="{{ route('login') }}">
                <span class="material-symbols-outlined text-lg rtl:rotate-180">arrow_back</span>
                <span>بازگشت به صفحه ورود</span>
            </a>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© ۱۴۰۳ تمامی حقوق محفوظ است</p>
        </div>
    </div>
</body>
</html>

