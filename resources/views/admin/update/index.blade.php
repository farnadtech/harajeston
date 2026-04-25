@extends('layouts.admin')

@section('title', 'آپدیت سیستم')
@section('page-title', 'آپدیت سیستم')
@section('header-title', 'آپدیت سیستم')
@section('header-subtitle', 'بررسی و نصب آخرین نسخه اسکریپت')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-red-600">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-blue-600">info</span>
            {{ session('info') }}
        </div>
    @endif

    {{-- کارت وضعیت نسخه --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">system_update</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">وضعیت نسخه</h2>
                <p class="text-sm text-gray-500">آخرین بررسی: همین الان</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">نسخه نصب شده</p>
                <p class="text-2xl font-black text-gray-900">{{ $current }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">آخرین نسخه</p>
                @if($error)
                    <p class="text-sm text-red-500">{{ $error }}</p>
                @elseif($latest)
                    <p class="text-2xl font-black {{ $hasUpdate ? 'text-green-600' : 'text-gray-900' }}">{{ $latest }}</p>
                @else
                    <p class="text-sm text-gray-400">نامشخص</p>
                @endif
            </div>
        </div>

        @if($hasUpdate)
            {{-- آپدیت موجود --}}
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-green-600 mt-0.5">new_releases</span>
                    <div>
                        <p class="font-bold text-green-800">نسخه {{ $latest }} آماده نصب است</p>
                        @if($changelog)
                            <p class="text-sm text-green-700 mt-1">{{ $changelog }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 flex items-start gap-2">
                <span class="material-symbols-outlined text-yellow-600 text-[18px] mt-0.5">warning</span>
                <p class="text-xs text-yellow-800">قبل از آپدیت، از دیتابیس بکاپ بگیرید. سیستم به صورت خودکار از فایل‌های قدیمی بکاپ می‌گیرد.</p>
            </div>

            <form method="POST" action="{{ route('admin.update.run') }}"
                  onsubmit="return confirm('آیا از نصب آپدیت مطمئن هستید؟ این عملیات چند دقیقه طول می‌کشد.')">
                @csrf
                <button type="submit"
                        class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span>
                    نصب آپدیت {{ $latest }}
                </button>
            </form>

        @elseif(!$error)
            {{-- آپدیتی نیست --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="material-symbols-outlined text-green-500 text-3xl">verified</span>
                <div>
                    <p class="font-bold text-gray-800">سیستم به‌روز است</p>
                    <p class="text-sm text-gray-500">شما از آخرین نسخه استفاده می‌کنید</p>
                </div>
            </div>
        @endif

        {{-- دکمه بررسی مجدد --}}
        <div class="mt-4 text-center">
            <a href="{{ route('admin.update.index') }}"
               class="text-sm text-primary hover:underline inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                بررسی مجدد
            </a>
        </div>
    </div>

    {{-- تاریخچه --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-3">اطلاعات نسخه فعلی</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <div class="flex justify-between">
                <span>نسخه:</span>
                <span class="font-mono font-bold">{{ $current }}</span>
            </div>
            <div class="flex justify-between">
                <span>مسیر نصب:</span>
                <span class="font-mono text-xs text-gray-400">{{ base_path() }}</span>
            </div>
            <div class="flex justify-between">
                <span>PHP:</span>
                <span class="font-mono">{{ PHP_VERSION }}</span>
            </div>
            <div class="flex justify-between">
                <span>Laravel:</span>
                <span class="font-mono">{{ app()->version() }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
