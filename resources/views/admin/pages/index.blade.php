@extends('layouts.admin')
@section('title', 'مدیریت صفحات')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">article</span>
            مدیریت صفحات
        </h1>
        <a href="{{ route('admin.pages.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            صفحه جدید
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @if($pages->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-3 block">article</span>
            <p>هنوز صفحه‌ای ایجاد نشده است.</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">عنوان</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">آدرس (Slug)</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">وضعیت</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">تاریخ</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pages as $page)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $page->title }}</td>
                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                        <a href="{{ url('/page/' . $page->slug) }}" target="_blank"
                           class="hover:text-primary transition-colors flex items-center gap-1">
                            /page/{{ $page->slug }}
                            <span class="material-symbols-outlined text-xs">open_in_new</span>
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        @if($page->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                منتشر شده
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                پیش‌نویس
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $page->created_at->format('Y/m/d') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.pages.edit', $page) }}"
                               class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                  onsubmit="return confirm('آیا از حذف این صفحه مطمئن هستید؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:border-red-400 hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
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
