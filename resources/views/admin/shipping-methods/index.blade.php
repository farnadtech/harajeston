@extends('layouts.app')

@section('title', 'مدیریت روش‌های ارسال')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">مدیریت روش‌های ارسال</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.shipping-methods.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                افزودن روش جدید
            </a>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                بازگشت به داشبورد
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">شناسه</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">توضیحات</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">هزینه پایه</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">وضعیت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عملیات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($shippingMethods ?? [] as $method)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">@persian($method->id)</td>
                        <td class="px-6 py-4">{{ $method->name }}</td>
                        <td class="px-6 py-4">{{ $method->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">@currency($method->base_cost)</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($method->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">فعال</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-sm">غیرفعال</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.shipping-methods.edit', $method) }}" class="text-blue-600 hover:text-blue-900 ml-3">ویرایش</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">روش ارسالی یافت نشد</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
