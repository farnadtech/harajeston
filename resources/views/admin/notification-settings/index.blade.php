@extends('layouts.admin')
@section('title', 'تنظیمات اعلان‌ها')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">notifications_active</span>
            تنظیمات اعلان‌ها
        </h1>
    </div>

    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-50 min-w-[320px] max-w-md px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 transition-all duration-300">
        <span id="toast-icon" class="material-symbols-outlined text-2xl flex-shrink-0"></span>
        <div class="flex-1">
            <p id="toast-msg" class="text-sm font-medium"></p>
        </div>
        <button onclick="hideToast()" class="text-current opacity-60 hover:opacity-100 flex-shrink-0">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Admin Contact Settings --}}
    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <h2 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">contact_mail</span>
            اطلاعات تماس ادمین برای دریافت تست
        </h2>
        <form method="POST" action="{{ route('admin.notification-settings.update-admin-phone') }}" class="flex flex-wrap items-end gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs text-gray-500 mb-1">شماره موبایل (پیامک تست)</label>
                <input type="text" name="admin_test_phone"
                       value="{{ \App\Models\SiteSetting::get('admin_test_phone', auth()->user()->phone ?? '') }}"
                       class="w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="09123456789" dir="ltr">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">ایمیل (ایمیل تست)</label>
                <input type="email" name="admin_test_email"
                       value="{{ \App\Models\SiteSetting::get('admin_test_email', auth()->user()->email ?? '') }}"
                       class="w-64 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                       placeholder="admin@example.com" dir="ltr">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">save</span>
                ذخیره
            </button>
            @php
                $adminPhone = \App\Models\SiteSetting::get('admin_test_phone', '');
                $adminEmail = \App\Models\SiteSetting::get('admin_test_email', '');
            @endphp
            <div class="flex flex-col gap-1 text-xs">
                @if($adminPhone)
                    <span class="text-green-600 flex items-center gap-1"><span class="material-symbols-outlined text-sm">check_circle</span>موبایل: {{ $adminPhone }}</span>
                @else
                    <span class="text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-sm">warning</span>موبایل تنظیم نشده</span>
                @endif
                @if($adminEmail)
                    <span class="text-green-600 flex items-center gap-1"><span class="material-symbols-outlined text-sm">check_circle</span>ایمیل: {{ $adminEmail }}</span>
                @else
                    <span class="text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-sm">warning</span>ایمیل تنظیم نشده</span>
                @endif
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.notification-settings.update') }}" id="settings-form">
        @csrf @method('PUT')

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200 grid grid-cols-12 gap-2 text-xs font-semibold text-gray-600">
                <div class="col-span-3">رویداد</div>
                <div class="col-span-1 text-center">گیرنده</div>
                <div class="col-span-1 text-center">داخل سایت</div>
                <div class="col-span-1 text-center">پیامک</div>
                <div class="col-span-1 text-center">ایمیل</div>
                <div class="col-span-2 text-center">شناسه پترن پیامک</div>
                <div class="col-span-1 text-center">تست پیامک</div>
                <div class="col-span-1 text-center">تست ایمیل</div>
                <div class="col-span-1 text-center">ایمیل</div>
            </div>

            @foreach($settings as $setting)
            <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                {{-- Main Row --}}
                <div class="p-4 grid grid-cols-12 gap-2 items-center">
                    <div class="col-span-3">
                        <p class="text-sm font-medium text-gray-800">{{ $setting->event_label }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $setting->event_key }}</p>
                    </div>
                    <div class="col-span-1 text-center">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $setting->recipient === 'buyer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $setting->recipient === 'buyer' ? 'خریدار' : 'فروشنده' }}
                        </span>
                    </div>
                    <div class="col-span-1 text-center">
                        <input type="checkbox" name="settings[{{ $setting->id }}][via_database]" value="1"
                               {{ $setting->via_database ? 'checked' : '' }}
                               class="w-4 h-4 text-primary rounded focus:ring-primary">
                    </div>
                    <div class="col-span-1 text-center">
                        <input type="checkbox" name="settings[{{ $setting->id }}][via_sms]" value="1"
                               {{ $setting->via_sms ? 'checked' : '' }}
                               class="w-4 h-4 text-primary rounded focus:ring-primary">
                    </div>
                    <div class="col-span-1 text-center">
                        <input type="checkbox" name="settings[{{ $setting->id }}][via_email]" value="1"
                               {{ $setting->via_email ? 'checked' : '' }}
                               class="w-4 h-4 text-primary rounded focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <input type="text" name="settings[{{ $setting->id }}][sms_pattern_id]"
                               value="{{ $setting->sms_pattern_id }}"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-xs font-mono focus:ring-1 focus:ring-primary focus:border-primary"
                               placeholder="123456" dir="ltr">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button"
                                onclick="testSms({{ $setting->id }}, '{{ route('admin.notification-settings.test', $setting) }}')"
                                class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-orange-50 border border-orange-200 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors w-full justify-center"
                                title="ارسال پیامک تست">
                            <span class="material-symbols-outlined text-sm">sms</span>
                            تست
                        </button>
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button"
                                onclick="testEmail({{ $setting->id }}, '{{ route('admin.notification-settings.test-email', $setting) }}')"
                                class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors w-full justify-center"
                                title="ارسال ایمیل تست">
                            <span class="material-symbols-outlined text-sm">mail</span>
                            تست
                        </button>
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button"
                                onclick="toggleEmailEditor({{ $setting->id }})"
                                class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-gray-50 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors w-full justify-center"
                                title="ویرایش قالب ایمیل">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            قالب
                        </button>
                    </div>
                </div>

                {{-- SMS Pattern Row --}}
                @if($setting->sms_pattern_text)
                <div class="px-4 pb-3 mr-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 space-y-2">
                        {{-- Header --}}
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-500 text-sm">sms</span>
                            <p class="text-xs font-bold text-amber-700">متن پیشنهادی پترن — فرمت باید به همین شکل باشد (متن قابل ویرایش است):</p>
                        </div>
                        {{-- Pattern text --}}
                        <div class="flex items-start gap-2">
                            <code class="flex-1 text-xs text-gray-800 leading-relaxed bg-white border border-amber-200 rounded px-3 py-2 block font-mono select-all" dir="rtl">{{ $setting->sms_pattern_text }}</code>
                            <button type="button"
                                    onclick="copyText(this, '{{ addslashes($setting->sms_pattern_text) }}')"
                                    class="flex-shrink-0 p-1.5 text-amber-600 hover:bg-amber-100 rounded transition-colors"
                                    title="کپی متن">
                                <span class="material-symbols-outlined text-base">content_copy</span>
                            </button>
                        </div>
                        {{-- Variables table --}}
                        @if(!empty($setting->sms_vars))
                        <div class="bg-white border border-amber-100 rounded overflow-hidden">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-amber-100">
                                        <th class="px-3 py-1.5 text-right text-amber-700 font-semibold w-16">متغیر</th>
                                        <th class="px-3 py-1.5 text-right text-amber-700 font-semibold">مقدار ارسالی از سیستم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setting->sms_vars as $placeholder => $description)
                                    <tr class="border-t border-amber-50">
                                        <td class="px-3 py-1.5">
                                            <code class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-mono font-bold">{{ $placeholder }}</code>
                                        </td>
                                        <td class="px-3 py-1.5 text-gray-600">{{ $description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Email Template Editor --}}
                <div id="email-editor-{{ $setting->id }}" class="hidden px-4 pb-3 mr-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-500 text-sm">mail</span>
                                <p class="text-xs font-bold text-blue-700">قالب ایمیل برای این رویداد:</p>
                            </div>
                            <span class="text-xs text-blue-500">متغیرها: {{'{'}}order_number{{'}'}} {{'{'}}buyer_name{{'}'}} {{'{'}}seller_name{{'}'}} {{'{'}}amount{{'}'}} {{'{'}}listing_title{{'}'}} {{'{'}}tracking_number{{'}'}}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-blue-700 mb-1">موضوع ایمیل:</label>
                            <input type="text"
                                   name="settings[{{ $setting->id }}][email_subject]"
                                   value="{{ $setting->email_subject }}"
                                   class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400 focus:border-blue-400 bg-white"
                                   placeholder="مثال: سفارش شما ثبت شد">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-blue-700 mb-1">متن ایمیل:</label>
                            <textarea name="settings[{{ $setting->id }}][email_body]"
                                      rows="5"
                                      class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400 focus:border-blue-400 bg-white resize-y font-mono"
                                      placeholder="متن ایمیل را اینجا بنویسید. می‌توانید از متغیرها استفاده کنید.">{{ $setting->email_body }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center gap-3">
            <div id="autosave-status" class="text-xs text-gray-400 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">cloud_done</span>
                تغییرات به صورت خودکار ذخیره می‌شوند
            </div>
        </div>
    </form>
</div>

<script>
let toastTimer = null;

function showToast(msg, type) {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toast-icon');
    const msgEl = document.getElementById('toast-msg');

    msgEl.textContent = msg;

    toast.className = toast.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').trim();

    if (type === 'success') {
        toast.classList.add('bg-green-600', 'text-white');
        icon.textContent = 'check_circle';
    } else if (type === 'error') {
        toast.classList.add('bg-red-600', 'text-white');
        icon.textContent = 'error';
    } else {
        toast.classList.add('bg-blue-600', 'text-white');
        icon.textContent = 'info';
    }

    toast.classList.remove('hidden');
    toast.classList.add('flex');

    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(hideToast, 5000);
}

function hideToast() {
    const toast = document.getElementById('toast');
    toast.classList.add('hidden');
    toast.classList.remove('flex');
}

function copyText(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('.material-symbols-outlined');
        icon.textContent = 'check';
        btn.classList.add('text-green-600');
        setTimeout(() => {
            icon.textContent = 'content_copy';
            btn.classList.remove('text-green-600');
        }, 2000);
    });
}

// Auto-save with debounce
let saveTimer = null;
const form = document.getElementById('settings-form');
const statusEl = document.getElementById('autosave-status');

function setStatus(type, msg) {
    const icons = { saving: 'sync', saved: 'cloud_done', error: 'cloud_off' };
    const colors = { saving: 'text-blue-500', saved: 'text-green-600', error: 'text-red-500' };
    statusEl.className = 'text-xs flex items-center gap-1 ' + (colors[type] || 'text-gray-400');
    statusEl.innerHTML = `<span class="material-symbols-outlined text-sm ${type === 'saving' ? 'animate-spin' : ''}">${icons[type]}</span>${msg}`;
}

function autoSave() {
    setStatus('saving', 'در حال ذخیره...');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        setStatus('saved', 'ذخیره شد ✓');
        setTimeout(() => setStatus('saved', 'تغییرات به صورت خودکار ذخیره می‌شوند'), 3000);
    })
    .catch(() => setStatus('error', 'خطا در ذخیره'));
}

form.addEventListener('change', () => {
    clearTimeout(saveTimer);
    setStatus('saving', 'در انتظار ذخیره...');
    saveTimer = setTimeout(autoSave, 1200);
});

// Also trigger on textarea input (not just change)
form.addEventListener('input', (e) => {
    if (e.target.tagName === 'TEXTAREA' || e.target.tagName === 'INPUT') {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(autoSave, 1500);
    }
});

function toggleEmailEditor(id) {
    const el = document.getElementById('email-editor-' + id);
    el.classList.toggle('hidden');
}

function testEmail(id, url) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span>';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => {
        showToast('خطا در ارتباط با سرور', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function testSms(id, url) {
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span>';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(r => r.json())
    .then(data => {
        showToast(data.message, data.success ? 'success' : 'error');
    })
    .catch(() => {
        showToast('خطا در ارتباط با سرور', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
@endsection
