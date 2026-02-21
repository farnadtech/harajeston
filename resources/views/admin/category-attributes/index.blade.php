@extends('layouts.admin')

@section('title', 'مدیریت ویژگی‌های دسته‌بندی')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ویژگی‌های دسته‌بندی: {{ $category->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">مسیر: {{ $category->getFullPath() }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                بازگشت
            </a>
            <a href="{{ route('admin.category-attributes.create', $category) }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                افزودن ویژگی جدید
            </a>
        </div>
    </div>

    {{-- پیام ارث‌بری ویژگی‌ها --}}
    @if($category->children && $category->children->count() > 0)
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
        <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
        <div>
            <p class="font-medium">ارث‌بری ویژگی‌ها</p>
            <p class="text-sm mt-1">ویژگی‌های این دسته به تمام زیردسته‌هایی که ویژگی خاص ندارند، به صورت خودکار اعمال می‌شود.</p>
        </div>
    </div>
    @endif

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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($attributes->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl text-gray-400">tune</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">هیچ ویژگی‌ای تعریف نشده</h3>
            <p class="text-gray-600 mb-4">برای این دسته‌بندی هنوز ویژگی‌ای اضافه نشده است.</p>
            <a href="{{ route('admin.category-attributes.create', $category) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                <span class="material-symbols-outlined text-sm">add</span>
                افزودن اولین ویژگی
            </a>
        </div>
        @else
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام ویژگی</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">گزینه‌ها</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الزامی</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">قابل فیلتر</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ترتیب</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($attributes as $attribute)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $attribute->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($attribute->type === 'select')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">انتخابی</span>
                        @elseif($attribute->type === 'text')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">متنی</span>
                        @else
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">عددی</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($attribute->options)
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($attribute->options, 0, 3) as $option)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ $option }}</span>
                                @endforeach
                                @if(count($attribute->options) > 3)
                                <span class="text-xs text-gray-500">+{{ count($attribute->options) - 3 }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($attribute->is_required)
                            <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                        @else
                            <span class="material-symbols-outlined text-gray-300 text-sm">cancel</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($attribute->is_filterable)
                            <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                        @else
                            <span class="material-symbols-outlined text-gray-300 text-sm">cancel</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $attribute->order }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.category-attributes.edit', [$category, $attribute]) }}" class="text-blue-600 hover:text-blue-800">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('admin.category-attributes.destroy', [$category, $attribute]) }}" method="POST" class="inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
