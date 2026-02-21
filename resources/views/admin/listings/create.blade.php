@extends('layouts.admin')

@section('title', 'ایجاد حراجی جدید')

@push('styles')
<link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}?v={{ now()->timestamp }}">
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">ایجاد حراجی جدید</h1>
        <p class="text-sm text-gray-600 mt-1">ایجاد حراجی توسط ادمین</p>
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

    <form action="{{ route('admin.listings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
            <!-- Seller Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">فروشنده *</label>
                <select name="seller_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">انتخاب فروشنده</option>
                    @foreach(\App\Models\User::where('role', 'seller')->orderBy('name')->get() as $seller)
                        <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                            {{ $seller->name }} ({{ $seller->email }})
                        </option>
                    @endforeach
                </select>
                @error('seller_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

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
                               autocomplete="off">
                        @error('starts_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">زمان پایان *</label>
                        <input type="text" 
                               name="ends_at" 
                               id="ends_at" 
                               value="{{ old('ends_at') }}" 
                               required
                               class="persian-datepicker-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="انتخاب تاریخ و زمان"
                               autocomplete="off">
                        @error('ends_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
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
                <p class="text-sm text-gray-500">هیچ روش ارسالی تعریف نشده است. <a href="{{ route('admin.shipping-methods.create') }}" class="text-primary hover:underline">ایجاد روش ارسال</a></p>
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

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">وضعیت *</label>
                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>در انتظار</option>
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
                @error('status')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                ایجاد حراجی
            </button>
            <a href="{{ route('admin.listings.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                انصراف
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ url('js/persian-datepicker-package.js') }}?v={{ now()->timestamp }}"></script>
<script>
// Initialize datepickers with minDate for starts_at
document.addEventListener('DOMContentLoaded', function() {
    // Starts at - can't be in the past
    const startsAtInput = document.getElementById('starts_at');
    if (startsAtInput && !startsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(startsAtInput, {
            minDate: 'today'
        });
    }
    
    // Ends at - no restriction (will be validated to be after starts_at on server)
    const endsAtInput = document.getElementById('ends_at');
    if (endsAtInput && !endsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(endsAtInput);
    }
});

// Toggle price input visibility when checkbox is checked
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

// Handle form submission
document.querySelector('form').addEventListener('submit', function(e) {
    // Check if at least one shipping method is selected
    const checkedMethods = document.querySelectorAll('.shipping-method-checkbox:checked');
    if (checkedMethods.length === 0) {
        e.preventDefault();
        alert('لطفاً حداقل یک روش ارسال را انتخاب کنید.');
        document.getElementById('shippingMethodsContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    // Get all number inputs and remove commas
    const numberInputs = this.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        if (input.value) {
            input.value = input.value.replace(/,/g, '');
        }
    });
});

// Scroll to error message if exists
@if ($errors->any())
    window.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.querySelector('.bg-red-50');
        if (errorBox) {
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
@endif
</script>
@endpush
