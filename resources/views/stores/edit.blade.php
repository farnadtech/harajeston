@extends('layouts.seller')

@section('title', 'ویرایش فروشگاه')

@section('page-title', 'ویرایش فروشگاه')
@section('page-subtitle', 'مدیریت اطلاعات فروشگاه خود')

@section('content')
<div class="max-w-4xl mx-auto"
        <h1 class="text-3xl font-bold text-gray-900 mb-8">ویرایش فروشگاه</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Store Profile Form --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">اطلاعات فروشگاه</h2>
            
            <form action="{{ route('stores.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">نام فروشگاه</label>
                    <input type="text" 
                           id="store_name" 
                           name="store_name" 
                           value="{{ old('store_name', $store->store_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('store_name') border-red-500 @enderror"
                           required>
                    @error('store_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">توضیحات فروشگاه</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $store->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        ذخیره تغییرات
                    </button>
                </div>
            </form>
        </div>

        {{-- Banner Upload --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">بنر فروشگاه</h2>
            
            @if($store->banner_url)
                <div class="mb-4">
                    <img src="{{ $store->banner_url }}" alt="بنر فروشگاه" class="w-full h-48 object-cover rounded-lg">
                </div>
            @endif

            <form action="{{ route('stores.upload-banner') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-2">آپلود بنر جدید</label>
                    <input type="file" 
                           id="banner" 
                           name="banner" 
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('banner') border-red-500 @enderror"
                           required>
                    <p class="text-sm text-gray-500 mt-1">حداکثر حجم: 2 مگابایت - ابعاد پیشنهادی: 1200x400 پیکسل</p>
                    @error('banner')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        آپلود بنر
                    </button>
                </div>
            </form>
        </div>

        {{-- Logo Upload --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">لوگوی فروشگاه</h2>
            
            @if($store->logo_url)
                <div class="mb-4">
                    <img src="{{ $store->logo_url }}" alt="لوگوی فروشگاه" class="w-32 h-32 object-cover rounded-full">
                </div>
            @endif

            <form action="{{ route('stores.upload-logo') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">آپلود لوگو جدید</label>
                    <input type="file" 
                           id="logo" 
                           name="logo" 
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('logo') border-red-500 @enderror"
                           required>
                    <p class="text-sm text-gray-500 mt-1">حداکثر حجم: 1 مگابایت - ابعاد پیشنهادی: 400x400 پیکسل</p>
                    @error('logo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        آپلود لوگو
                    </button>
                </div>
            </form>
        </div>

        {{-- Back to Store --}}
        <div class="mt-6">
            <a href="{{ route('stores.show', $store->slug) }}" class="text-blue-600 hover:text-blue-800">
                ← بازگشت به فروشگاه
            </a>
        </div>
    </div>
@endsection
