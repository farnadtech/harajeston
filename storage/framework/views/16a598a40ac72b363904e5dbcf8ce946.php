

<?php $__env->startSection('title', 'تنظیمات عمومی سایت'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex items-center gap-3 mb-8">
        <span class="material-symbols-outlined text-3xl text-blue-600">settings</span>
        <h1 class="text-2xl font-bold">تنظیمات عمومی سایت</h1>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.settings.general.update')); ?>" id="generalForm">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">badge</span>
                هویت سایت
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نام سایت <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" value="<?php echo e($settings['site_name']); ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required maxlength="100">
                    <p class="text-xs text-gray-500 mt-1">در تایتل صفحات و لوگو نمایش داده می‌شود</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">شعار سایت</label>
                    <input type="text" name="site_tagline" value="<?php echo e($settings['site_tagline']); ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           maxlength="200">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات سایت (meta description)</label>
                    <textarea name="site_description" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              maxlength="500"><?php echo e($settings['site_description']); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">آیکون پیش‌فرض (Material Icon)</label>
                    <div class="flex gap-2">
                        <input type="text" name="site_icon" id="siteIconInput" value="<?php echo e($settings['site_icon']); ?>"
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               maxlength="50" placeholder="gavel">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center border border-blue-200">
                            <span class="material-symbols-outlined text-blue-600 text-2xl" id="iconPreview"><?php echo e($settings['site_icon'] ?: 'gavel'); ?></span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">وقتی لوگو آپلود نشده نمایش داده می‌شود. <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-600">لیست آیکون‌ها</a></p>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">image</span>
                لوگو و فاویکون
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">لوگوی سایت</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer" id="logoDropzone">
                        <?php if($settings['site_logo']): ?>
                            <img src="<?php echo e(rtrim(config('app.url'), '/') . '/storage/' . $settings['site_logo']); ?>" alt="لوگو" class="h-16 mx-auto mb-2 object-contain" id="logoPreview">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-4xl text-gray-400 mb-2 block" id="logoIcon">image</span>
                        <?php endif; ?>
                        <p class="text-sm text-gray-500 mb-2">کلیک کنید یا فایل را اینجا بکشید</p>
                        <p class="text-xs text-gray-400">PNG, JPG, SVG, WebP - حداکثر 2MB</p>
                        <input type="file" id="logoFile" accept="image/*" class="hidden">
                    </div>
                    <?php if($settings['site_logo']): ?>
                        <button type="button" onclick="removeLogo('site_logo')" class="mt-2 text-xs text-red-600 hover:underline">حذف لوگو</button>
                    <?php endif; ?>
                    <span id="logoStatus" class="text-xs mt-1 block"></span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">فاویکون (Favicon)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer" id="faviconDropzone">
                        <?php if($settings['site_favicon']): ?>
                            <img src="<?php echo e(rtrim(config('app.url'), '/') . '/storage/' . $settings['site_favicon']); ?>" alt="فاویکون" class="h-16 mx-auto mb-2 object-contain" id="faviconPreview">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-4xl text-gray-400 mb-2 block" id="faviconIcon">tab</span>
                        <?php endif; ?>
                        <p class="text-sm text-gray-500 mb-2">کلیک کنید یا فایل را اینجا بکشید</p>
                        <p class="text-xs text-gray-400">PNG, ICO - حداکثر 2MB</p>
                        <input type="file" id="faviconFile" accept="image/*" class="hidden">
                    </div>
                    <?php if($settings['site_favicon']): ?>
                        <button type="button" onclick="removeLogo('site_favicon')" class="mt-2 text-xs text-red-600 hover:underline">حذف فاویکون</button>
                    <?php endif; ?>
                    <span id="faviconStatus" class="text-xs mt-1 block"></span>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-pink-600">palette</span>
                رنگ‌بندی و تم سایت
            </h2>
            <p class="text-sm text-gray-500 mb-5">این رنگ‌ها در تمام صفحات سایت از جمله صفحات ورود، ثبت‌نام، و صفحات ساخته شده اعمال می‌شوند.</p>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ اصلی (Primary)</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="color_primary" id="colorPrimary" value="<?php echo e($settings['color_primary']); ?>"
                               class="w-12 h-10 rounded cursor-pointer border border-gray-300">
                        <input type="text" id="colorPrimaryText" value="<?php echo e($settings['color_primary']); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">دکمه‌ها، لینک‌ها، لوگو</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ اصلی hover</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="color_primary_hover" id="colorPrimaryHover" value="<?php echo e($settings['color_primary_hover']); ?>"
                               class="w-12 h-10 rounded cursor-pointer border border-gray-300">
                        <input type="text" id="colorPrimaryHoverText" value="<?php echo e($settings['color_primary_hover']); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">حالت hover دکمه‌ها</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ ثانویه (Secondary)</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="color_secondary" id="colorSecondary" value="<?php echo e($settings['color_secondary']); ?>"
                               class="w-12 h-10 rounded cursor-pointer border border-gray-300">
                        <input type="text" id="colorSecondaryText" value="<?php echo e($settings['color_secondary']); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">رنگ تاکیدی، بج‌ها</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ پس‌زمینه</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="color_bg" id="colorBg" value="<?php echo e($settings['color_bg']); ?>"
                               class="w-12 h-10 rounded cursor-pointer border border-gray-300">
                        <input type="text" id="colorBgText" value="<?php echo e($settings['color_bg']); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">پس‌زمینه کلی سایت</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رنگ متن اصلی</label>
                    <div class="flex gap-2 items-center">
                        <input type="color" name="color_text" id="colorText" value="<?php echo e($settings['color_text']); ?>"
                               class="w-12 h-10 rounded cursor-pointer border border-gray-300">
                        <input type="text" id="colorTextText" value="<?php echo e($settings['color_text']); ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">رنگ متن پیش‌فرض</p>
                </div>
            </div>

            
            <div class="mt-6 p-4 rounded-xl border border-gray-200 bg-gray-50">
                <p class="text-sm font-medium text-gray-700 mb-3">پیش‌نمایش رنگ‌ها:</p>
                <div class="flex flex-wrap gap-3 items-center">
                    <button type="button" id="previewBtn" class="px-5 py-2 rounded-lg text-white text-sm font-bold transition-colors" style="background-color: <?php echo e($settings['color_primary']); ?>">دکمه اصلی</button>
                    <button type="button" id="previewBtnSecondary" class="px-5 py-2 rounded-lg text-white text-sm font-bold" style="background-color: <?php echo e($settings['color_secondary']); ?>">دکمه ثانویه</button>
                    <span id="previewText" class="text-sm font-bold" style="color: <?php echo e($settings['color_primary']); ?>">لینک و متن رنگی</span>
                    <div id="previewBg" class="px-4 py-2 rounded-lg text-sm" style="background-color: <?php echo e($settings['color_bg']); ?>; color: <?php echo e($settings['color_text']); ?>">پس‌زمینه و متن</div>
                </div>
            </div>
        </div>

        
        
        

        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('admin.settings.index')); ?>" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                بازگشت
            </a>
            <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">save</span>
                ذخیره تنظیمات
            </button>
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// sync color pickers with text inputs
function syncColor(pickerId, textId, formName) {
    var picker = document.getElementById(pickerId);
    var text = document.getElementById(textId);
    picker.addEventListener('input', function() {
        text.value = this.value;
        updatePreview();
    });
    text.addEventListener('input', function() {
        if (/^#[0-9a-fA-F]{6}$/.test(this.value)) {
            picker.value = this.value;
            updatePreview();
        }
    });
}

syncColor('colorPrimary', 'colorPrimaryText', 'color_primary');
syncColor('colorPrimaryHover', 'colorPrimaryHoverText', 'color_primary_hover');
syncColor('colorSecondary', 'colorSecondaryText', 'color_secondary');
syncColor('colorBg', 'colorBgText', 'color_bg');
syncColor('colorText', 'colorTextText', 'color_text');

// sync text inputs back to hidden form fields
document.querySelectorAll('[id$="Text"]').forEach(function(el) {
    el.addEventListener('input', function() {
        var name = this.id.replace('Text', '').replace('color', 'color_').replace(/([A-Z])/g, function(m) { return '_' + m.toLowerCase(); }).replace('color__', 'color_');
        // find the color input by name
        var colorInput = document.querySelector('input[type="color"][name="' + name + '"]');
        if (colorInput && /^#[0-9a-fA-F]{6}$/.test(this.value)) {
            colorInput.value = this.value;
        }
    });
});

function updatePreview() {
    var primary = document.getElementById('colorPrimary').value;
    var secondary = document.getElementById('colorSecondary').value;
    var bg = document.getElementById('colorBg').value;
    var text = document.getElementById('colorText').value;
    document.getElementById('previewBtn').style.backgroundColor = primary;
    document.getElementById('previewBtnSecondary').style.backgroundColor = secondary;
    document.getElementById('previewText').style.color = primary;
    document.getElementById('previewBg').style.backgroundColor = bg;
    document.getElementById('previewBg').style.color = text;
}

// icon preview
document.getElementById('siteIconInput').addEventListener('input', function() {
    document.getElementById('iconPreview').textContent = this.value || 'gavel';
});

// logo upload
function setupDropzone(dropzoneId, fileInputId, type) {
    var dropzone = document.getElementById(dropzoneId);
    var fileInput = document.getElementById(fileInputId);

    dropzone.addEventListener('click', function() { fileInput.click(); });
    dropzone.addEventListener('dragover', function(e) { e.preventDefault(); dropzone.classList.add('border-blue-500'); });
    dropzone.addEventListener('dragleave', function() { dropzone.classList.remove('border-blue-500'); });
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-blue-500');
        if (e.dataTransfer.files[0]) uploadFile(e.dataTransfer.files[0], type, dropzoneId);
    });
    fileInput.addEventListener('change', function() {
        if (this.files[0]) uploadFile(this.files[0], type, dropzoneId);
    });
}

function uploadFile(file, type, dropzoneId) {
    var statusEl = document.getElementById(type === 'site_logo' ? 'logoStatus' : 'faviconStatus');
    if (statusEl) { statusEl.textContent = 'در حال آپلود...'; statusEl.className = 'text-xs text-blue-600 mt-1'; }

    var formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    fetch('<?php echo e(route("admin.settings.general.upload-logo")); ?>', { method: 'POST', body: formData })
        .then(function(r) {
            if (!r.ok) {
                return r.text().then(function(t) { throw new Error('HTTP ' + r.status + ': ' + t.substring(0, 200)); });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                var dropzone = document.getElementById(dropzoneId);
                var previewId = type === 'site_logo' ? 'logoPreview' : 'faviconPreview';
                var iconId = type === 'site_logo' ? 'logoIcon' : 'faviconIcon';
                var existing = document.getElementById(previewId);
                var icon = document.getElementById(iconId);
                if (icon) icon.style.display = 'none';
                if (existing) {
                    existing.src = data.url;
                    existing.style.display = 'block';
                } else {
                    var img = document.createElement('img');
                    img.id = previewId;
                    img.src = data.url;
                    img.className = 'h-16 mx-auto mb-2 object-contain';
                    dropzone.insertBefore(img, dropzone.firstChild);
                }
                if (statusEl) { statusEl.textContent = 'آپلود موفق'; statusEl.className = 'text-xs text-green-600 mt-1'; }
            } else {
                if (statusEl) { statusEl.textContent = 'خطا: ' + (data.message || 'نامشخص'); statusEl.className = 'text-xs text-red-600 mt-1'; }
            }
        })
        .catch(function(err) {
            console.error('Upload error:', err);
            if (statusEl) { statusEl.textContent = 'خطا: ' + err.message; statusEl.className = 'text-xs text-red-600 mt-1'; }
            alert('خطا در آپلود: ' + err.message);
        });
}

function removeLogo(type) {
    if (!confirm('آیا مطمئن هستید؟')) return;
    fetch('<?php echo e(route("admin.settings.general.update")); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({ _method: 'POST', [type]: '' })
    });
    // Just clear via SiteSetting directly
    var fd = new FormData();
    fd.append('_token', '<?php echo e(csrf_token()); ?>');
    fd.append(type, '');
    // reload to reflect
    location.reload();
}

setupDropzone('logoDropzone', 'logoFile', 'site_logo');
setupDropzone('faviconDropzone', 'faviconFile', 'site_favicon');
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/settings/general.blade.php ENDPATH**/ ?>