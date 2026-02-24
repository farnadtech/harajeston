@extends('layouts.admin')

@section('title', 'درخواست‌های تغییر نام فروشگاه')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">درخواست‌های تغییر نام فروشگاه</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">فروشنده</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام فعلی</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام درخواستی</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاریخ درخواست</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عملیات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $store)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $store->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $store->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $store->store_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-blue-600">{{ $store->pending_store_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Morilog\Jalali\Jalalian::fromCarbon($store->store_name_change_requested_at)->format('Y/m/d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('admin.stores.approve-name', $store) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 ml-3">تایید</button>
                                </form>
                                <form action="{{ route('admin.stores.reject-name', $store) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">رد</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">درخواستی برای تغییر نام فروشگاه وجود ندارد.</p>
        </div>
    @endif
</div>
@endsection
