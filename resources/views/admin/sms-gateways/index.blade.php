@extends('layouts.admin')
@section('title', 'تنظیمات ملی پیامک')
@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">تنظیمات ملی پیامک</h1>
        <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700"> بازگشت</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">{{ session('success') }}</div>
    @endif

    {{-- فرم تنظیمات --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600">sms</span>
            </div>
            <div>
                <h2 class="text-xl font-bold">ملی پیامک</h2>
                <p class="text-sm text-gray-500">melipayamak.com</p>
            </div>
            @if($settings?->isConfigured())
                <span class="mr-auto inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span> فعال
                </span>
            @else
                <span class="mr-auto inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full">
                    <span class="w-2 h-2 bg-gray-400 rounded-full inline-block"></span> تنظیم نشده
                </span>
            @endif
        </div>

        <form action="{{ route('admin.sms-gateways.save') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نام کاربری <span class="text-red-500">*</span></label>
                    <input type="text" name="username"
                           value="{{ $settings?->username ?? old('username') }}"
                           placeholder="نام کاربری پنل ملی پیامک"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" dir="ltr" required/>
                    @error('username')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور</label>
                    <input type="password" name="password"
                           placeholder="{{ $settings?->password ? '••••••• (برای تغییر وارد کنید)' : 'رمز عبور پنل ملی پیامک' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" dir="ltr"/>
                    <p class="text-xs text-gray-400 mt-1">اگر از ApiKey استفاده می‌کنید، این فیلد را خالی بگذارید.</p>
                </div>
            </div>

            {{-- ApiKey --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <label class="block text-sm font-bold text-blue-800 mb-1">
                    ApiKey (توصیه‌شده)
                    <span class="font-normal text-blue-600 mr-1">— به جای رمز عبور استفاده می‌شود</span>
                </label>
                <input type="text" name="api_key"
                       value="{{ $settings?->api_key ?? '' }}"
                       placeholder="مثال: c97c616c-c227-4e3e-86a6-b7ea6268c0ca"
                       class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white" dir="ltr"/>
                <p class="text-xs text-blue-600 mt-1">ApiKey را از پنل ملی پیامک → تنظیمات وبسرویس دریافت کنید. اگر ApiKey وارد شود، اولویت دارد.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره فرستنده (اختیاری)</label>
                <input type="text" name="from_number"
                       value="{{ $settings?->from_number ?? '' }}"
                       placeholder="مثال: 5000..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white" dir="ltr"/>
                <p class="text-xs text-gray-400 mt-1">برای SendOtp نیازی به شماره فرستنده نیست. برای ارسال متنی آزاد لازم است.</p>
            </div>

            {{-- Body ID پترن --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <label class="block text-sm font-bold text-green-800 mb-1">
                    شناسه پترن (Body ID)
                    <span class="font-normal text-green-600 mr-1">— برای ارسال از خط خدماتی اشتراکی</span>
                </label>
                <input type="text" name="body_id"
                       value="{{ $settings?->body_id ?? '' }}"
                       placeholder="مثال: 123456"
                       class="w-full px-4 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm bg-white" dir="ltr"/>
                <p class="text-xs text-green-700 mt-1">
                    از پنل ملی پیامک → ارسال پیامک → پیامک پترن، یک پترن با متغیر کد بسازید و شناسه آن را اینجا وارد کنید.
                    اگر خالی باشد، از متد SendOtp2 استفاده می‌شود.
                </p>
            </div>
            </div>
            <div class="pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg transition-colors text-sm">
                    ذخیره تنظیمات
                </button>
            </div>
        </form>
    </div>

    {{-- بخش تست --}}
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-gray-600">send</span>
            ارسال پیامک آزمایشی
        </h3>

        @if(session('test_success'))
            <div class="bg-green-50 border border-green-300 rounded-lg p-4 mb-4">
                <p class="text-green-700 font-medium text-sm"> {{ session('test_success') }}</p>
                @if(session('test_response'))
                    <div class="mt-2 bg-gray-100 rounded p-2 text-xs font-mono text-gray-700 break-all" dir="ltr">
                        پاسخ سرور: {{ session('test_response') }}
                    </div>
                @endif
            </div>
        @endif

        @if(session('test_error'))
            <div class="bg-red-50 border border-red-300 rounded-lg p-4 mb-4">
                <p class="text-red-700 font-medium text-sm"> {{ session('test_error') }}</p>
                @if(session('test_response'))
                    <div class="mt-2 bg-gray-100 rounded p-2 text-xs font-mono text-gray-700 break-all" dir="ltr">
                        پاسخ سرور: {{ session('test_response') }}
                    </div>
                @endif
            </div>
        @endif

        @if(!$settings?->isConfigured())
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4 text-sm text-amber-700">
                ابتدا نام کاربری و رمز عبور را ذخیره کنید.
            </div>
        @endif

        <form action="{{ route('admin.sms-gateways.test') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="test_phone" placeholder="09123456789" maxlength="11"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" dir="ltr"
                   {{ !$settings?->isConfigured() ? 'disabled' : '' }}/>
            <button type="submit"
                    class="{{ $settings?->isConfigured() ? 'bg-gray-700 hover:bg-gray-800' : 'bg-gray-300 cursor-not-allowed' }} text-white font-bold px-5 py-2 rounded-lg transition-colors text-sm whitespace-nowrap"
                    {{ !$settings?->isConfigured() ? 'disabled' : '' }}>
                ارسال تست
            </button>
        </form>
        @error('test_phone')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror

        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400">
                پس از ارسال، پاسخ سرور ملی پیامک نمایش داده می‌شود.
                RecId مثبت = موفق | عدد منفی = کد خطا
            </p>
        </div>
    </div>
</div>
@endsection
