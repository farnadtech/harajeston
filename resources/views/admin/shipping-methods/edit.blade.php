@extends('layouts.admin')

@section('title', 'ویرایش روش ارسال')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.shipping-methods.index') }}" class="text-gray-600 hover:text-gray-900">
            <span class="material-symbols-outlined text-2xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-black text-gray-900">ویرایش روش ارسال</h2>
            <p class="text-sm text-gray-500 mt-1">ویرایش اطلاعات روش ارسال {{ $shippingMethod->name }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.shipping-methods.update', $shippingMethod) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        نام روش ارسال <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $shippingMethod->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="مثال: پست پیشتاز"
                           required>
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        توضیحات
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                              placeholder="توضیحات اختیاری درباره این روش ارسال">{{ old('description', $shippingMethod->description) }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Base Cost -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        هزینه پایه (تومان) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="base_cost" 
                           value="{{ old('base_cost', $shippingMethod->base_cost) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="50000"
                           min="0"
                           step="1000"
                           required>
                    @error('base_cost')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estimated Days -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        مدت زمان تحویل (روز کاری)
                    </label>
                    <input type="number" 
                           name="estimated_days" 
                           value="{{ old('estimated_days', $shippingMethod->estimated_days) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="3"
                           min="1"
                           max="30">
                    @error('estimated_days')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Is Active -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $shippingMethod->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-primary rounded focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">فعال بودن روش ارسال</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-medium">
                    بروزرسانی روش ارسال
                </button>
                <a href="{{ route('admin.shipping-methods.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
