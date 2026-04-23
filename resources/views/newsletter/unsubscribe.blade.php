@extends('layouts.app')
@section('title', 'لغو اشتراک خبرنامه')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 max-w-md w-full text-center">
        @if($success)
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-4xl text-green-600">check_circle</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mb-3">اشتراک لغو شد</h1>
            <p class="text-gray-500 mb-6">شما با موفقیت از خبرنامه ما لغو اشتراک کردید. دیگر ایمیلی دریافت نخواهید کرد.</p>
        @else
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-4xl text-red-500">error</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mb-3">لینک نامعتبر</h1>
            <p class="text-gray-500 mb-6">این لینک لغو اشتراک معتبر نیست یا قبلاً استفاده شده است.</p>
        @endif
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-600 transition-colors">
            <span class="material-symbols-outlined">home</span>
            بازگشت به صفحه اصلی
        </a>
    </div>
</div>
@endsection
