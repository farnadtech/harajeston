@extends('layouts.admin')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-900">افزودن دسته‌بندی جدید</h2>
        <p class="text-sm text-gray-500 mt-1">ایجاد دسته‌بندی یا زیردسته جدید</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- نام دسته‌بندی -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نام دسته‌بندی *</label>
                    <input type="text" 
                           name="name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('name') border-red-500 @enderror" 
                           value="{{ old('name') }}" 
                           required
                           placeholder="مثال: الکترونیکی">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- نامک (Slug) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نامک (Slug)</label>
                    <input type="text" 
                           name="slug" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('slug') border-red-500 @enderror" 
                           value="{{ old('slug') }}" 
                           placeholder="خودکار - مثال: electronics">
                    <p class="text-xs text-gray-500 mt-1">در صورت خالی بودن، خودکار ایجاد می‌شود</p>
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- دسته والد -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">دسته والد</label>
                    <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('parent_id') border-red-500 @enderror">
                        <option value="">دسته اصلی</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">برای ایجاد زیردسته، دسته والد را انتخاب کنید</p>
                    @error('parent_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- آیکون -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">آیکون (Material Icons)</label>
                    <input type="text" 
                           name="icon" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('icon') border-red-500 @enderror" 
                           value="{{ old('icon') }}" 
                           placeholder="مثال: category">
                    <p class="text-xs text-gray-500 mt-1">
                        <a href="https://fonts.google.com/icons" target="_blank" class="text-primary hover:underline">جستجوی آیکون</a>
                    </p>
                    @error('icon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- توضیحات -->
            <div class="mt-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror"
                          placeholder="توضیحات اختیاری درباره این دسته‌بندی">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- فعال/غیرفعال -->
            <div class="mt-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" 
                           name="is_active" 
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">فعال</span>
                </label>
            </div>

            <!-- دکمه‌ها -->
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    ذخیره دسته‌بندی
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
