@extends('layouts.app')

@section('title', 'ایجاد آگهی جدید')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">ایجاد آگهی جدید</h1>

    <div class="bg-white rounded-lg shadow-md p-6" x-data="{ 
        type: 'auction',
        showAuctionFields: true,
        showDirectSaleFields: false,
        basePrice: 0,
        deposit: 0,
        updateDeposit() {
            this.deposit = Math.round(this.basePrice * 0.1);
        }
    }">
        <form method="POST" action="{{ url('/listings') }}" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Type Selection -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-3">نوع آگهی</label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="auction" x-model="type" 
                               @change="showAuctionFields = true; showDirectSaleFields = false"
                               class="hidden peer">
                        <div class="border-2 border-gray-300 peer-checked:border-purple-600 peer-checked:bg-purple-50 rounded-lg p-4 text-center transition">
                            <span class="text-2xl block mb-2">🔨</span>
                            <span class="font-bold">مزایده</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="direct_sale" x-model="type"
                               @change="showAuctionFields = false; showDirectSaleFields = true"
                               class="hidden peer">
                        <div class="border-2 border-gray-300 peer-checked:border-green-600 peer-checked:bg-green-50 rounded-lg p-4 text-center transition">
                            <span class="text-2xl block mb-2">🛒</span>
                            <span class="font-bold">فروش مستقیم</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="hybrid" x-model="type"
                               @change="showAuctionFields = true; showDirectSaleFields = true"
                               class="hidden peer">
                        <div class="border-2 border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-lg p-4 text-center transition">
                            <span class="text-2xl block mb-2">⚡</span>
                            <span class="font-bold">ترکیبی</span>
                        </div>
                    </label>
                </div>
                @error('type')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Basic Information -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">عنوان آگهی</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="عنوان جذاب برای آگهی خود وارد کنید" required>
                @error('title')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">توضیحات</label>
                <textarea name="description" rows="5"
                          class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="توضیحات کامل درباره محصول..." required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Category Selection -->
            <div class="mb-6">
                <x-category-selector :selected="old('category_id')" />
            </div>

            <!-- Listing Attributes -->
            <x-listing-attributes />

            <!-- Auction Fields -->
            <div x-show="showAuctionFields" x-transition>
                <h3 class="text-xl font-bold mb-4 text-purple-600">اطلاعات مزایده</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">قیمت پایه (ریال)</label>
                        <input type="number" name="base_price" x-model="basePrice" @input="updateDeposit"
                               value="{{ old('base_price') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('base_price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">سپرده (۱۰٪ خودکار)</label>
                        <input type="text" x-model="deposit" readonly
                               class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">تاریخ شروع</label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('start_time')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">تاریخ پایان</label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('end_time')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Direct Sale Fields -->
            <div x-show="showDirectSaleFields" x-transition>
                <h3 class="text-xl font-bold mb-4 text-green-600">اطلاعات فروش مستقیم</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">قیمت (ریال)</label>
                        <input type="number" name="price" value="{{ old('price') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('price')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">موجودی انبار</label>
                        <input type="number" name="stock" value="{{ old('stock') }}"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('stock')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">تصاویر محصول</label>
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">حداکثر ۵ تصویر - فرمت‌های مجاز: JPG, PNG</p>
                @error('images')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold">
                    ایجاد آگهی
                </button>
                <a href="{{ url('/dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
