@extends('layouts.admin')
@section('title', 'مدیریت خبرنامه')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مدیریت خبرنامه</h1>
            <p class="text-sm text-gray-500 mt-1">مشترکین و ارسال ایمیل گروهی</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.newsletter.export', request()->query()) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium">
                <span class="material-symbols-outlined text-base">download</span>
                خروجی Excel
            </a>
            <button onclick="document.getElementById('send-modal').style.display='flex'"
                    class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
                <span class="material-symbols-outlined text-base">send</span>
                ارسال ایمیل گروهی
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-primary">{{ $totalAll }}</div>
            <div class="text-sm text-gray-500 mt-1">کل مشترکین</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-green-600">{{ $totalActive }}</div>
            <div class="text-sm text-gray-500 mt-1">مشترکین فعال</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-amber-500">{{ $totalAll - $totalActive }}</div>
            <div class="text-sm text-gray-500 mt-1">لغو اشتراک</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو در ایمیل یا نام..."
                   class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                <option value="">همه</option>
                <option value="active" @selected(request('status')==='active')>فعال</option>
                <option value="inactive" @selected(request('status')==='inactive')>غیرفعال</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">فیلتر</button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.newsletter.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">پاک کردن</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">ایمیل</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">نام</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">وضعیت</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">تاریخ عضویت</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscribers as $sub)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-400">{{ $sub->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $sub->email }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $sub->name ?: '-' }}</td>
                    <td class="px-4 py-3">
                        @if($sub->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                فعال
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <span class="material-symbols-outlined text-xs">cancel</span>
                                غیرفعال
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $sub->created_at ? $sub->created_at->format('Y/m/d') : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <form method="POST" action="{{ route('admin.newsletter.toggle', $sub) }}">
                                @csrf
                                <button type="submit" title="{{ $sub->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}"
                                        class="p-1.5 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors {{ $sub->is_active ? 'text-amber-500' : 'text-green-600' }}">
                                    <span class="material-symbols-outlined text-sm">{{ $sub->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $sub) }}" onsubmit="return confirm('حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 transition-colors text-red-500">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-5xl block mb-2">mail_off</span>
                        هیچ مشترکی یافت نشد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($subscribers->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $subscribers->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Send Email Modal --}}
<div id="send-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" dir="rtl">
    <div class="absolute inset-0 bg-black/60" onclick="this.parentElement.style.display='none'"></div>
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg text-primary">send</span>
                ارسال ایمیل گروهی
            </h3>
            <button onclick="document.getElementById('send-modal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.newsletter.send') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ارسال به</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="target" value="active" checked class="accent-primary">
                        مشترکین فعال ({{ $totalActive }} نفر)
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="target" value="all" class="accent-primary">
                        همه ({{ $totalAll }} نفر)
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">موضوع ایمیل</label>
                <input type="text" name="subject" required placeholder="موضوع ایمیل را وارد کنید"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">متن ایمیل</label>
                <textarea name="body" required rows="7" placeholder="متن ایمیل... (HTML پشتیبانی می‌شود)"
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary resize-y font-mono"></textarea>
                <p class="text-xs text-gray-400 mt-1">لینک لغو اشتراک به صورت خودکار اضافه می‌شود.</p>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="document.getElementById('send-modal').style.display='none'"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    انصراف
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">send</span>
                    ارسال ایمیل
                </button>
            </div>
        </form>
    </div>

@endsection
