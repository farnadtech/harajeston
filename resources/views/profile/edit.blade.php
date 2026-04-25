@extends('layouts.app')
@section('title', 'ویرایش پروفایل')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">person</span>
        ویرایش پروفایل
    </h1>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Avatar --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h3 class="font-bold text-gray-800 mb-4">تصویر پروفایل</h3>
            <div class="flex items-center gap-6">
                <div class="relative">
                    @if($user->avatar)
                        <img src="{{ url('storage/'.$user->avatar) }}" id="avatar-preview"
                             class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div id="avatar-preview" class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center border-2 border-gray-200">
                            <span class="material-symbols-outlined text-4xl text-primary">person</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer text-sm font-medium text-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-base">upload</span>
                        انتخاب تصویر
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    </label>
                    <p class="text-xs text-gray-400 mt-2">فرمت: JPG, PNG - حداکثر 2MB</p>
                    @error('avatar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Basic Info --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 space-y-5">
            <h3 class="font-bold text-gray-800 mb-4">اطلاعات پایه</h3>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">نام و نام خانوادگی</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary" required>
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">شماره موبایل</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary" dir="ltr">
                @error('phone')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">بیوگرافی (اختیاری)</label>
                <textarea name="bio" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                          placeholder="چند خط درباره خودتان...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Change Password --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 space-y-5">
            <h3 class="font-bold text-gray-800 mb-4">تغییر رمز عبور</h3>
            <p class="text-xs text-gray-500 mb-4">اگر نمی‌خواهید رمز عبور را تغییر دهید، این بخش را خالی بگذارید</p>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">رمز عبور جدید</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">تکرار رمز عبور</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">save</span>
                ذخیره تغییرات
            </button>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                انصراف
            </a>
        </div>
    </form>
</div>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-24 h-24 rounded-full object-cover border-2 border-gray-200';
                img.id = 'avatar-preview';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
