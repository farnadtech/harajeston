<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ورود - حراج‌استون</title>
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
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-md p-8 sm:p-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h1 class="text-xl font-black tracking-tight">
                حراج<span class="text-primary">استون</span>
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl">login</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">خوش برگشتید</h2>
            <p class="text-sm text-gray-500 text-center">لطفا برای ورود اطلاعات خود را وارد کنید</p>
        </div>
        
        @if(session('status'))
            <div class="bg-green-50 border-r-4 border-green-500 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-700 font-medium">{{ session('status') }}</p>
            </div>
        @endif

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
        
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل یا شماره تلفن</label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="login" 
                           value="{{ old('login') }}"
                           placeholder="example@email.com یا 09123456789" 
                           type="text"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">person</span>
                </div>
            </div>
            
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">رمز عبور</label>
                    <a class="text-xs font-bold text-primary hover:text-primary-hover" href="{{ route('password.request') }}">
                        فراموش کردید؟
                    </a>
                </div>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="password" 
                           placeholder="••••••••" 
                           type="password"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">lock</span>
                </div>
            </div>
            
            <button class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                <span>ورود به حساب کاربری</span>
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <span class="text-sm text-gray-600">حساب کاربری ندارید؟</span>
            <a class="text-sm font-bold text-primary hover:text-primary-hover mr-1" href="{{ route('register') }}">
                ثبت‌نام کنید
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
            @if(\App\Models\SiteSetting::get('otp_enabled', true))
            <a href="{{ route('otp.login') }}" class="text-sm text-gray-500 hover:text-primary font-medium flex items-center justify-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-base">sms</span>
                ورود با کد یکبار مصرف (OTP)
            </a>
            @endif
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© ۱۴۰۳ تمامی حقوق محفوظ است</p>
        </div>
    </div>
</body>
</html>
