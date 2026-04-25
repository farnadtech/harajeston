@extends('layouts.admin')
@section('title', $page ? 'ویرایش صفحه' : 'ایجاد صفحه جدید')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors mb-3">
            <span class="material-symbols-outlined text-base">arrow_forward</span>
            بازگشت به لیست صفحات
        </a>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">{{ $page ? 'edit' : 'add' }}</span>
            {{ $page ? 'ویرایش صفحه' : 'ایجاد صفحه جدید' }}
        </h1>
    </div>

    <form method="POST" action="{{ $page ? route('admin.pages.update', $page) : route('admin.pages.store') }}">
        @csrf
        @if($page) @method('PUT') @endif

        <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">عنوان صفحه</label>
                <input type="text" name="title" value="{{ old('title', $page->title ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                       placeholder="مثال: درباره ما" required>
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">آدرس صفحه (Slug)</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400">/page/</span>
                    <input type="text" name="slug" value="{{ old('slug', $page->slug ?? '') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary font-mono"
                           placeholder="about-us" dir="ltr">
                </div>
                <p class="text-xs text-gray-400 mt-1">اگر خالی بگذارید، به صورت خودکار از عنوان ساخته می‌شود</p>
                @error('slug')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content Editor --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">محتوای صفحه</label>
                <textarea id="content-editor" name="content" rows="15"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('content', $page->content ?? '') }}</textarea>
                @error('content')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- SEO Section --}}
            <div class="border-t border-gray-200 pt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">search</span>
                    تنظیمات SEO
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">عنوان متا (Meta Title)</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                               placeholder="عنوان برای موتورهای جستجو">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">توضیحات متا (Meta Description)</label>
                        <textarea name="meta_description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                                  placeholder="توضیح کوتاه برای موتورهای جستجو">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="border-t border-gray-200 pt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">وضعیت انتشار</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="published"
                               {{ old('status', $page->status ?? 'published') === 'published' ? 'checked' : '' }}
                               class="w-4 h-4 text-primary focus:ring-primary">
                        <span class="text-sm text-gray-700">منتشر شده</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="draft"
                               {{ old('status', $page->status ?? '') === 'draft' ? 'checked' : '' }}
                               class="w-4 h-4 text-primary focus:ring-primary">
                        <span class="text-sm text-gray-700">پیش‌نویس</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 mt-6">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">save</span>
                {{ $page ? 'ذخیره تغییرات' : 'ایجاد صفحه' }}
            </button>
            <a href="{{ route('admin.pages.index') }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                انصراف
            </a>
        </div>
    </form>
</div>

{{-- TinyMCE Editor - Offline --}}
<script src="/haraj/public/js/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#content-editor',
    height: 500,
    directionality: 'rtl',
    language: 'fa',
    language_url: '/haraj/public/js/tinymce/langs/fa.js',
    menubar: 'edit insert format table',
    plugins: 'link image code lists table wordcount',
    toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignright aligncenter alignleft alignjustify | bullist numlist | link image | table | code | wordcount',
    content_style: 'body { font-family: Vazirmatn, Tahoma, sans-serif; font-size: 14px; direction: rtl; text-align: right; padding: 10px; }',
    branding: false,
    promotion: false,
    license_key: 'gpl',
    // sync content to textarea before form submit
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    },
    // image upload handler
    images_upload_handler: function(blobInfo, progress) {
        return new Promise(function(resolve, reject) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("admin.pages.upload-image") }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) progress(e.loaded / e.total * 100);
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.location) {
                            resolve(json.location);
                        } else {
                            reject('پاسخ نامعتبر از سرور');
                        }
                    } catch(e) {
                        reject('خطا در پردازش پاسخ');
                    }
                } else {
                    reject('آپلود ناموفق: ' + xhr.status);
                }
            };

            xhr.onerror = function() { reject('خطا در اتصال'); };
            xhr.send(formData);
        });
    },
});

// sync TinyMCE content to textarea before form submit
document.querySelector('form').addEventListener('submit', function(e) {
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
        // validate content not empty
        var content = tinymce.get('content-editor');
        if (content && content.getContent().trim() === '') {
            e.preventDefault();
            alert('لطفاً محتوای صفحه را وارد کنید');
            return false;
        }
    }
});
</script>
@endsection
