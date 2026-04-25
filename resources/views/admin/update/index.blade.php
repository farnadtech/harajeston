@extends('layouts.admin')

@section('title', 'آپدیت سیستم')
@section('page-title', 'آپدیت سیستم')
@section('header-title', 'آپدیت سیستم')
@section('header-subtitle', 'مدیریت نسخه، آپدیت و بازگردانی')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @foreach(['success','error','info'] as $type)
        @if(session($type))
            @php $colors = ['success'=>'green','error'=>'red','info'=>'blue']; $c = $colors[$type]; @endphp
            <div class="bg-{{ $c }}-50 border border-{{ $c }}-200 text-{{ $c }}-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-{{ $c }}-600">{{ $type === 'success' ? 'check_circle' : ($type === 'error' ? 'error' : 'info') }}</span>
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    {{-- وضعیت نسخه --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">system_update</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">وضعیت نسخه</h2>
                <p class="text-xs text-gray-400">آخرین بررسی: همین الان</p>
            </div>
            <a href="{{ route('admin.update.index') }}" class="mr-auto text-sm text-primary hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">refresh</span> بررسی مجدد
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">نسخه نصب شده</p>
                <p class="text-2xl font-black text-gray-900">{{ $current }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">آخرین نسخه</p>
                @if($error)
                    <p class="text-sm text-red-500 mt-2">{{ $error }}</p>
                @elseif($latest)
                    <p class="text-2xl font-black {{ $hasUpdate ? 'text-green-600' : 'text-gray-900' }}">{{ $latest }}</p>
                @else
                    <p class="text-sm text-gray-400 mt-2">نامشخص</p>
                @endif
            </div>
        </div>

        @if($hasUpdate)
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-green-600 mt-0.5">new_releases</span>
                <div>
                    <p class="font-bold text-green-800">نسخه {{ $latest }} آماده نصب است</p>
                    @if($changelog)
                        <p class="text-sm text-green-700 mt-1">{{ $changelog }}</p>
                    @endif
                </div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 flex items-start gap-2">
                <span class="material-symbols-outlined text-yellow-600 text-[18px] mt-0.5">warning</span>
                <p class="text-xs text-yellow-800">قبل از آپدیت یک بکاپ کامل از فایل‌ها و دیتابیس گرفته می‌شود. در صورت بروز مشکل می‌توانید rollback کنید.</p>
            </div>
            <form method="POST" action="{{ route('admin.update.run') }}"
                  onsubmit="return confirm('آیا از نصب آپدیت مطمئن هستید؟')">
                @csrf
                <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span>
                    نصب آپدیت {{ $latest }}
                </button>
            </form>
        @elseif(!$error)
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <span class="material-symbols-outlined text-green-500 text-3xl">verified</span>
                <div>
                    <p class="font-bold text-gray-800">سیستم به‌روز است</p>
                    <p class="text-sm text-gray-500">شما از آخرین نسخه استفاده می‌کنید</p>
                </div>
            </div>
        @endif
    </div>

    {{-- آپلود دستی --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">آپلود دستی فایل آپدیت</h3>
                <p class="text-xs text-gray-500">فایل zip آپدیت را مستقیم آپلود کنید</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.update.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="flex gap-3">
                <input type="file" name="zip_file" accept=".zip" required
                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 file:ml-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary/10 file:text-primary file:text-sm cursor-pointer">
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors whitespace-nowrap">
                    نصب
                </button>
            </div>
        </form>
    </div>

    {{-- بکاپ‌ها و Rollback --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">history</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">بکاپ‌ها و بازگردانی</h3>
                <p class="text-xs text-gray-500">قبل از هر آپدیت یک بکاپ کامل ذخیره می‌شود</p>
            </div>
        </div>

        @if(count($backups) > 0)
            <div class="divide-y divide-gray-100">
                @foreach($backups as $backup)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">folder_zip</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900">نسخه {{ $backup['version'] }}</p>
                            <p class="text-xs text-gray-400">{{ $backup['created_at'] }} — {{ $backup['size'] }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form method="POST" action="{{ route('admin.update.rollback') }}"
                                  onsubmit="return confirm('بازگردانی به این نسخه؟ دیتابیس و فایل‌ها برگردانده می‌شوند.')">
                                @csrf
                                <input type="hidden" name="backup" value="{{ $backup['name'] }}">
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">restore</span>
                                    بازگردانی
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.update.backup.delete') }}"
                                  onsubmit="return confirm('حذف این بکاپ؟')">
                                @csrf
                                <input type="hidden" name="backup" value="{{ $backup['name'] }}">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-400">
                <span class="material-symbols-outlined text-4xl mb-2">folder_open</span>
                <p class="text-sm">هنوز بکاپی وجود ندارد</p>
                <p class="text-xs mt-1">بکاپ‌ها قبل از هر آپدیت به صورت خودکار ساخته می‌شوند</p>
            </div>
        @endif
    </div>

    {{-- اطلاعات سیستم --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-3">اطلاعات سیستم</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">نسخه فعلی</span>
                <span class="font-mono font-bold">{{ $current }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">PHP</span>
                <span class="font-mono">{{ PHP_VERSION }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">Laravel</span>
                <span class="font-mono">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">سرور آپدیت</span>
                <span class="font-mono text-xs text-gray-400">iranbooklet.ir/harajino</span>
            </div>
        </div>
    </div>

</div>
@endsection
