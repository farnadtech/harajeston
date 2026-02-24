@extends('layouts.seller')

@section('title', 'ویرایش فروشگاه')
@section('page-title', 'ویرایش فروشگاه')
@section('page-subtitle', 'مدیریت اطلاعات و تصاویر فروشگاه')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Store Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">اطلاعات فروشگاه</h2>
        
        @if($store->pending_store_name)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 ml-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold">درخواست تغییر نام در انتظار تایید</p>
                        <p class="text-sm mt-1">نام جدید: <strong>{{ $store->pending_store_name }}</strong></p>
                        <p class="text-xs text-yellow-700 mt-1">تاریخ درخواست: {{ \Morilog\Jalali\Jalalian::fromCarbon($store->store_name_change_requested_at)->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <form action="{{ route('stores.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">نام فروشگاه</label>
                <input type="text" name="store_name" value="{{ old('store_name', $store->store_name) }}"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
                @if($store->pending_store_name)
                    <p class="text-xs text-gray-500 mt-1">نام فعلی: {{ $store->store_name }} | در انتظار تایید: {{ $store->pending_store_name }}</p>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $store->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">آدرس</label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address', $store->address) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تلفن</label>
                    <input type="text" name="phone" value="{{ old('phone', $store->phone) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ایمیل</label>
                    <input type="email" name="email" value="{{ old('email', $store->email) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                ذخیره تغییرات
            </button>
        </form>
    </div>

    <!-- Logo Upload -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">لوگو فروشگاه</h2>
        
        @if($store->logo_image)
            <div class="mb-4">
                <img src="{{ url('storage/' . $store->logo_image) }}" alt="Logo" class="w-32 h-32 object-cover rounded-lg">
            </div>
        @endif

        <form action="{{ route('stores.upload-logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <input type="file" name="logo" accept="image/*" required
                       class="w-full px-4 py-2 border rounded-lg">
                <p class="text-sm text-gray-500 mt-1">حداکثر 1MB - فرمت: JPG, PNG, WEBP - ابعاد توصیه شده: 300x300</p>
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                آپلود لوگو
            </button>
        </form>
    </div>

    <!-- Banner Upload -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">بنر فروشگاه</h2>
        
        @if($store->banner_image)
            <div class="mb-4">
                <img src="{{ url('storage/' . $store->banner_image) }}" alt="Banner" class="w-full h-48 object-cover rounded-lg">
            </div>
        @endif

        <form action="{{ route('stores.upload-banner') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <input type="file" name="banner" accept="image/*" required
                       class="w-full px-4 py-2 border rounded-lg">
                <p class="text-sm text-gray-500 mt-1">حداکثر 2MB - فرمت: JPG, PNG, WEBP - ابعاد توصیه شده: 1920x400</p>
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                آپلود بنر
            </button>
        </form>
    </div>
</div>
@endsection
