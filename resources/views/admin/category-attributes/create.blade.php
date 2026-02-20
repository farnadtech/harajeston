@extends('layouts.admin')

@section('title', 'افزودن ویژگی جدید')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">افزودن ویژگی جدید</h1>
        <p class="text-sm text-gray-600 mt-1">دسته‌بندی: {{ $category->getFullPath() }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.category-attributes.store', $category) }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نام ویژگی *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="مثال: رم، رنگ، سایز">
                    @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع ویژگی *</label>
                    <select name="type" id="attributeType" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="select" {{ old('type') === 'select' ? 'selected' : '' }}>انتخابی (لیست کشویی)</option>
                        <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>متنی</option>
                        <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>عددی</option>
                    </select>
                    @error('type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="optionsField" style="display: {{ old('type', 'select') === 'select' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">گزینه‌ها (با کاما جدا کنید)</label>
                    <textarea name="options" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                              placeholder="مثال: 4GB, 8GB, 16GB, 32GB">{{ old('options') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">هر گزینه را با کاما (,) از هم جدا کنید</p>
                    @error('options')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ترتیب نمایش</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('order')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="text-sm text-gray-700">الزامی</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_filterable" value="1" {{ old('is_filterable', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <span class="text-sm text-gray-700">قابل فیلتر در جستجو</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                    ذخیره ویژگی
                </button>
                <a href="{{ route('admin.category-attributes.index', $category) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('attributeType').addEventListener('change', function() {
    const optionsField = document.getElementById('optionsField');
    if (this.value === 'select') {
        optionsField.style.display = 'block';
    } else {
        optionsField.style.display = 'none';
    }
});
</script>
@endsection
