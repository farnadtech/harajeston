@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-6xl text-primary mb-4">storefront</span>
            <h1 class="text-3xl font-black text-gray-900 mb-2">فروشنده شوید</h1>
            <p class="text-gray-600">با ایجاد فروشگاه خود، محصولات خود را به فروش برسانید</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($showSuspensionWarning) && $showSuspensionWarning)
            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-red-600 text-2xl">warning</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-900 mb-1">حساب شما تعلیق شده است</h3>
                        <p class="text-red-700 text-sm">
                            <span class="font-bold">دلیل تعلیق:</span> {{ auth()->user()->seller_rejection_reason ?? 'نقض قوانین پلتفرم' }}
                        </p>
                        <p class="text-red-700 text-sm mt-2">
                            با ارسال مجدد درخواست، مدیریت مجدداً اطلاعات شما را بررسی خواهد کرد.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($existingData))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-yellow-600">edit</span>
                    <div class="text-sm text-yellow-800">
                        <p class="font-bold mb-1">اطلاعات قبلی شما بارگذاری شد</p>
                        <p>اطلاعات درخواست قبلی شما بارگذاری شده است. می‌توانید آنها را ویرایش کرده و مجدداً ارسال کنید.</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('seller-request.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600">info</span>
                    <div class="text-sm text-blue-800">
                        <p class="font-bold mb-1">نکات مهم:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>تمام اطلاعات را با دقت وارد کنید</li>
                            <li>اطلاعات شما توسط مدیریت بررسی خواهد شد</li>
                            <li>پس از تایید، می‌توانید محصولات خود را اضافه کنید</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">نام فروشگاه <span class="text-red-500">*</span></label>
                <input type="text" name="store_name" value="{{ old('store_name', $existingData['store_name'] ?? '') }}" 
                       class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                       required>
                @error('store_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات فروشگاه <span class="text-red-500">*</span></label>
                <textarea name="store_description" rows="4" 
                          class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                          required>{{ old('store_description', $existingData['store_description'] ?? '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">توضیحات کوتاهی درباره فروشگاه و محصولات خود بنویسید</p>
                @error('store_description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">شماره تماس <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $existingData['phone'] ?? '') }}" 
                           class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="09123456789"
                           required>
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">کد ملی <span class="text-red-500">*</span></label>
                    <input type="text" name="national_id" value="{{ old('national_id', $existingData['national_id'] ?? '') }}" 
                           class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           maxlength="10"
                           required>
                    @error('national_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">آدرس (اختیاری)</label>
                <textarea name="address" rows="2" 
                          class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">{{ old('address', $existingData['address'] ?? '') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">اطلاعات بانکی</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نام بانک <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $existingData['bank_name'] ?? '') }}" 
                               class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                               placeholder="مثال: ملی، ملت، پاسارگاد"
                               required>
                        @error('bank_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">شماره حساب / شبا <span class="text-red-500">*</span></label>
                        <input type="text" name="bank_account" value="{{ old('bank_account', $existingData['bank_account'] ?? '') }}" 
                               class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                               placeholder="IR..."
                               required>
                        <p class="text-xs text-gray-500 mt-1">درآمد شما به این حساب واریز خواهد شد</p>
                        @error('bank_account')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-6">
                <button type="submit" 
                        class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    ارسال درخواست
                </button>
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-lg text-center transition-colors">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
