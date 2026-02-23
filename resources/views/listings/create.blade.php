@extends('layouts.app')

@section('title', 'ایجاد آگهی جدید')

@push('styles')
<link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}?v={{ now()->timestamp }}">
<style>
    /* حذف فلش پیش‌فرض select */
    select {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
    }
    
    /* اضافه کردن فلش سفارشی در سمت چپ */
    select {
        background: white url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") no-repeat !important;
        background-size: 1.5em 1.5em !important;
        background-position: 0.5rem center !important;
        padding-left: 2.5rem !important;
        padding-right: 0.75rem !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">ایجاد آگهی جدید</h1>
        <p class="text-sm text-gray-600 mt-1">ایجاد آگهی حراجی</p>
    </div>

    {{-- نمایش خطاهای validation --}}
    @if ($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="material-symbols-outlined text-red-500 text-2xl">error</span>
                </div>
                <div class="mr-3 flex-1">
                    <h3 class="text-sm font-bold text-red-800 mb-2">لطفاً خطاهای زیر را برطرف کنید:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ url('/listings') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
            <!-- Basic Info -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان حراجی *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('title')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات *</label>
                <textarea name="description" rows="5" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <x-category-selector :selected="old('category_id')" />
            </div>

            <!-- Attributes -->
            <x-listing-attributes />

            <!-- Condition -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت کالا *</label>
                <select name="condition" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>نو</option>
                    <option value="like_new" {{ old('condition') === 'like_new' ? 'selected' : '' }}>در حد نو</option>
                    <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>دست دوم</option>
                </select>
                @error('condition')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Auction Settings -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">تنظیمات مزایده</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">قیمت شروع (تومان) *</label>
                        <input type="number" name="starting_price" value="{{ old('starting_price') }}" required min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('starting_price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">قیمت خرید فوری (تومان)</label>
                        <input type="number" name="buy_now_price" value="{{ old('buy_now_price') }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('buy_now_price')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ سپرده (تومان)</label>
                        <input type="number" name="deposit_amount" value="{{ old('deposit_amount', 0) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('deposit_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حداقل افزایش پیشنهاد (تومان)</label>
                        <input type="number" name="bid_increment" value="{{ old('bid_increment', 10000) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('bid_increment')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">زمان شروع *</label>
                        <input type="text" 
                               name="starts_at" 
                               id="starts_at" 
                               value="{{ old('starts_at') }}" 
                               required
                               class="persian-datepicker-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="انتخاب تاریخ و زمان"
                               autocomplete="off"
                               onchange="calculateEndDate()">
                        @error('starts_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @php
                        $forceDuration = \App\Models\SiteSetting::get('force_auction_duration', false);
                        $durationDays = \App\Models\SiteSetting::get('auction_duration_days', 7);
                    @endphp

                    <div id="ends_at_container" class="{{ $forceDuration ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">زمان پایان *</label>
                        <input type="text" 
                               name="ends_at" 
                               id="ends_at" 
                               value="{{ old('ends_at') }}" 
                               {{ $forceDuration ? '' : 'required' }}
                               class="persian-datepicker-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="انتخاب تاریخ و زمان"
                               autocomplete="off">
                        @error('ends_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($forceDuration)
                    <div class="col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">محاسبه خودکار زمان پایان</p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        زمان پایان حراجی به صورت خودکار {{ \App\Services\PersianNumberService::convertToPersian($durationDays) }} روز بعد از زمان شروع محاسبه می‌شود.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="ends_at" id="ends_at_hidden" value="{{ old('ends_at') }}">
                    </div>
                    @endif
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_extend" value="1" {{ old('auto_extend') ? 'checked' : '' }}
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="text-sm text-gray-700">تمدید خودکار در صورت پیشنهاد در دقایق پایانی</span>
                    </label>
                </div>
            </div>

            <!-- Shipping Methods -->
            <div class="border-t border-gray-200 pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">روش‌های ارسال * <span class="text-xs text-gray-500">(حداقل یک روش را انتخاب کنید)</span></label>
                @php
                    $shippingMethods = \App\Models\ShippingMethod::where('is_active', true)->get();
                @endphp
                
                @if($shippingMethods->count() > 0)
                <div class="space-y-3" id="shippingMethodsContainer">
                    @foreach($shippingMethods as $method)
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors" data-method-id="{{ $method->id }}">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="shipping_methods[]" 
                                   value="{{ $method->id }}"
                                   class="w-4 h-4 text-primary rounded focus:ring-primary mt-1 shipping-method-checkbox"
                                   onchange="togglePriceInput(this, {{ $method->id }})">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $method->name }}</span>
                                        @if($method->estimated_days)
                                            <span class="text-xs text-gray-500 mr-2">({{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز)</span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        قیمت پایه: {{ \App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost)) }} تومان
                                    </span>
                                </div>
                                
                                <div class="price-adjustment-container hidden" id="price-container-{{ $method->id }}">
                                    <label class="block text-xs text-gray-600 mb-1">قیمت سفارشی برای این محصول (تومان)</label>
                                    <input type="number" 
                                           name="shipping_costs[{{ $method->id }}]" 
                                           id="price-input-{{ $method->id }}"
                                           value="{{ $method->base_cost }}"
                                           min="0"
                                           step="1000"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="قیمت ارسال برای این محصول">
                                    <p class="text-xs text-gray-500 mt-1">می‌توانید قیمت ارسال را برای این محصول تغییر دهید</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">هیچ روش ارسالی تعریف نشده است.</p>
                @endif
                @error('shipping_methods')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">برچسب‌ها (با کاما جدا کنید)</label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                       placeholder="مثال: لپتاپ, گیمینگ, ارزان"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">حداکثر 5 برچسب</p>
                @error('tags')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Images Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">تصاویر محصول</h3>
            <p class="text-sm text-gray-600 mb-4">حداکثر 8 تصویر می‌توانید آپلود کنید. اولین تصویر به عنوان تصویر اصلی نمایش داده می‌شود.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">انتخاب تصاویر</label>
                    <input type="file" 
                           name="images[]" 
                           id="images" 
                           multiple 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-primary file:text-white
                                  hover:file:bg-blue-600
                                  cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">فرمت‌های مجاز: JPG, PNG, GIF - حداکثر حجم هر تصویر: 2MB</p>
                    @error('images')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4" style="display: none;"></div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                ایجاد آگهی
            </button>
            <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                انصراف
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ url('js/persian-datepicker-package.js') }}?v={{ now()->timestamp }}"></script>
<script>
const FORCE_DURATION = {{ $forceDuration ? 'true' : 'false' }};
const DURATION_DAYS = {{ $durationDays }};

document.addEventListener('DOMContentLoaded', function() {
    const startsAtInput = document.getElementById('starts_at');
    if (startsAtInput && !startsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(startsAtInput, { minDate: 'today' });
    }
    
    const endsAtInput = document.getElementById('ends_at');
    if (endsAtInput && !endsAtInput.dataset.pickerInitialized && !FORCE_DURATION) {
        new PersianDatePicker(endsAtInput);
    }
});

function calculateEndDate() {
    if (!FORCE_DURATION) return;
    const startsAtInput = document.getElementById('starts_at');
    const endsAtHidden = document.getElementById('ends_at_hidden');
    if (!startsAtInput || !endsAtHidden) return;
    const startsAtValue = startsAtInput.value;
    if (!startsAtValue) return;
    const match = startsAtValue.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})\s+(\d{1,2}):(\d{1,2})$/);
    if (!match) return;
    const jy = parseInt(match[1]);
    const jm = parseInt(match[2]);
    const jd = parseInt(match[3]);
    const hour = parseInt(match[4]);
    const minute = parseInt(match[5]);
    let newJd = jd + DURATION_DAYS;
    let newJm = jm;
    let newJy = jy;
    const daysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
    while (newJd > daysInMonth[newJm - 1]) {
        newJd -= daysInMonth[newJm - 1];
        newJm++;
        if (newJm > 12) {
            newJm = 1;
            newJy++;
        }
    }
    const endsAtValue = `${newJy}/${String(newJm).padStart(2, '0')}/${String(newJd).padStart(2, '0')} ${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
    endsAtHidden.value = endsAtValue;
}

function togglePriceInput(checkbox, methodId) {
    const container = document.getElementById('price-container-' + methodId);
    const input = document.getElementById('price-input-' + methodId);
    if (checkbox.checked) {
        container.classList.remove('hidden');
        input.disabled = false;
    } else {
        container.classList.add('hidden');
        input.disabled = true;
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    const checkedMethods = document.querySelectorAll('.shipping-method-checkbox:checked');
    if (checkedMethods.length === 0) {
        e.preventDefault();
        alert('لطفاً حداقل یک روش ارسال را انتخاب کنید.');
        document.getElementById('shippingMethodsContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    const numberInputs = this.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        if (input.value) {
            input.value = input.value.replace(/,/g, '');
        }
    });
});

@if ($errors->any())
    window.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.querySelector('.bg-red-50');
        if (errorBox) {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
@endif

document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('imagePreview');
    previewContainer.innerHTML = '';
    if (files.length === 0) {
        previewContainer.style.display = 'none';
        return;
    }
    if (files.length > 8) {
        alert('حداکثر 8 تصویر می‌توانید انتخاب کنید.');
        e.target.value = '';
        return;
    }
    previewContainer.style.display = 'grid';
    files.forEach((file, index) => {
        if (file.size > 2 * 1024 * 1024) {
            alert(`حجم تصویر "${file.name}" بیش از 2MB است.`);
            return;
        }
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${event.target.result}" 
                     class="w-full h-32 object-cover rounded-lg border-2 border-gray-200"
                     alt="Preview ${index + 1}">
                <div class="absolute top-2 right-2 bg-primary text-white text-xs px-2 py-1 rounded">
                    ${index === 0 ? 'تصویر اصلی' : index + 1}
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                    <span class="text-white opacity-0 group-hover:opacity-100 text-sm">
                        ${(file.size / 1024).toFixed(0)} KB
                    </span>
                </div>
            `;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
