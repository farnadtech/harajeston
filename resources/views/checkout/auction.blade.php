@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">تکمیل خرید حراجی</h1>
        <p class="text-gray-600 mt-2">لطفاً اطلاعات ارسال را وارد کنید</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('checkout.auction.process', $listing) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Listing Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">محصول</h2>
                    <div class="flex gap-4">
                        @if($listing->images->first())
                            <img src="{{ url('storage/' . $listing->images->first()->file_path) }}" 
                                 alt="{{ $listing->title }}" 
                                 class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900">{{ $listing->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">مبلغ برنده شده: {{ \App\Services\PersianNumberService::convertToPersian(number_format($totalAmount)) }} تومان</p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">آدرس ارسال</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">آدرس کامل</label>
                            <textarea name="shipping_address" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                      placeholder="آدرس کامل خود را وارد کنید">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">شهر</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="نام شهر">
                                @error('shipping_city')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">کد پستی</label>
                                <input type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                       placeholder="کد پستی ۱۰ رقمی">
                                @error('shipping_postal_code')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">شماره تماس</label>
                            <input type="text" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="شماره تماس برای هماهنگی ارسال">
                            @error('shipping_phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">روش ارسال</h2>
                    
                    @if($shippingMethods->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                            هیچ روش ارسالی برای این محصول تعریف نشده است. لطفاً با فروشنده تماس بگیرید.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($shippingMethods as $method)
                                @php
                                    $cost = $method->cost;
                                    if ($method->pivot->custom_cost_adjustment) {
                                        $cost += $method->pivot->custom_cost_adjustment;
                                    }
                                @endphp
                                <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary transition-colors">
                                    <input type="radio" name="shipping_method_id" value="{{ $method->id }}" 
                                           data-cost="{{ $cost }}"
                                           class="w-5 h-5 text-primary" 
                                           {{ old('shipping_method_id') == $method->id ? 'checked' : '' }}
                                           onchange="updateTotal()">
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900">{{ $method->name }}</div>
                                        @if($method->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $method->description }}</div>
                                        @endif
                                        <div class="text-sm text-gray-500 mt-1">
                                            زمان تحویل: {{ \App\Services\PersianNumberService::convertToPersian($method->estimated_days) }} روز کاری
                                        </div>
                                    </div>
                                    <div class="font-bold text-primary">
                                        {{ \App\Services\PersianNumberService::convertToPersian(number_format($cost)) }} تومان
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('shipping_method_id')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg transition-colors flex items-center justify-center gap-2"
                        {{ $shippingMethods->isEmpty() ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined">check_circle</span>
                    تایید و ثبت سفارش
                </button>
            </form>
        </div>

        <!-- Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-900 mb-4">خلاصه سفارش</h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">مبلغ برنده شده:</span>
                        <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(number_format($totalAmount)) }} تومان</span>
                    </div>
                    
                    <div class="flex justify-between text-green-600">
                        <span>سپرده پرداخت شده:</span>
                        <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(number_format($depositAmount)) }} تومان</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">مبلغ باقیمانده:</span>
                        <span class="font-bold">{{ \App\Services\PersianNumberService::convertToPersian(number_format($remainingAmount)) }} تومان</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">هزینه ارسال:</span>
                        <span class="font-bold" id="shipping-cost">انتخاب کنید</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-lg">
                        <span class="font-bold text-gray-900">جمع کل:</span>
                        <span class="font-black text-primary" id="final-total">{{ \App\Services\PersianNumberService::convertToPersian(number_format($totalAmount)) }} تومان</span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-xl">info</span>
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-1">نکات مهم:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>مبلغ از کیف پول شما کسر می‌شود</li>
                                <li>پس از تحویل، پول به فروشنده واریز می‌شود</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Custom form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        // Remove previous error messages
        document.querySelectorAll('.custom-error').forEach(el => el.remove());
        
        let hasError = false;
        
        // Validate shipping address
        const address = document.querySelector('textarea[name="shipping_address"]');
        if (!address.value.trim()) {
            showError(address, 'لطفاً آدرس کامل را وارد کنید');
            hasError = true;
        }
        
        // Validate city
        const city = document.querySelector('input[name="shipping_city"]');
        if (!city.value.trim()) {
            showError(city, 'لطفاً نام شهر را وارد کنید');
            hasError = true;
        }
        
        // Validate postal code
        const postalCode = document.querySelector('input[name="shipping_postal_code"]');
        if (!postalCode.value.trim()) {
            showError(postalCode, 'لطفاً کد پستی را وارد کنید');
            hasError = true;
        } else if (!/^\d{10}$/.test(postalCode.value.trim())) {
            showError(postalCode, 'کد پستی باید ۱۰ رقم باشد');
            hasError = true;
        }
        
        // Validate phone
        const phone = document.querySelector('input[name="shipping_phone"]');
        if (!phone.value.trim()) {
            showError(phone, 'لطفاً شماره تماس را وارد کنید');
            hasError = true;
        } else if (!/^09\d{9}$/.test(phone.value.trim())) {
            showError(phone, 'شماره تماس باید با ۰۹ شروع شود و ۱۱ رقم باشد');
            hasError = true;
        }
        
        // Validate shipping method
        const shippingMethod = document.querySelector('input[name="shipping_method_id"]:checked');
        if (!shippingMethod) {
            const shippingSection = document.querySelector('.bg-white.rounded-lg.shadow-sm.p-6:has(input[name="shipping_method_id"])');
            if (shippingSection) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'custom-error bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mt-4';
                errorDiv.textContent = 'لطفاً یک روش ارسال انتخاب کنید';
                shippingSection.appendChild(errorDiv);
                shippingSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
    });
    
    function showError(element, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'custom-error text-red-600 text-sm mt-1';
        errorDiv.textContent = message;
        element.parentNode.appendChild(errorDiv);
        element.classList.add('border-red-500');
        
        // Scroll to first error
        if (!document.querySelector('.custom-error:first-of-type').previousElementSibling) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Remove error on input
        element.addEventListener('input', function() {
            element.classList.remove('border-red-500');
            const error = element.parentNode.querySelector('.custom-error');
            if (error) error.remove();
        }, { once: true });
    }
});

function updateTotal() {
    const selectedShipping = document.querySelector('input[name="shipping_method_id"]:checked');
    if (selectedShipping) {
        const shippingCost = parseInt(selectedShipping.dataset.cost);
        const totalAmount = {{ $totalAmount }};
        const finalTotal = totalAmount + shippingCost;
        
        document.getElementById('shipping-cost').textContent = shippingCost.toLocaleString('fa-IR') + ' تومان';
        document.getElementById('final-total').textContent = finalTotal.toLocaleString('fa-IR') + ' تومان';
    }
}

// Initialize on page load if a method is already selected
document.addEventListener('DOMContentLoaded', function() {
    const selectedShipping = document.querySelector('input[name="shipping_method_id"]:checked');
    if (selectedShipping) {
        updateTotal();
    }
});
</script>
@endsection
