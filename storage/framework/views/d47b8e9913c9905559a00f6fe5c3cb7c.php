
<?php $__env->startSection('title', 'طراحی صفحه اصلی'); ?>

<?php $__env->startSection('content'); ?>
<div dir="rtl">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0;">طراحی صفحه اصلی</h1>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">بلوک‌ها را بکشید، جابجا کنید، اضافه یا حذف کنید</p>
        </div>
        <a href="<?php echo e(route('home')); ?>" target="_blank"
           style="display:flex; align-items:center; gap:6px; border:1px solid #d1d5db; color:#374151; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px; background:white;">
            <span class="material-symbols-outlined" style="font-size:16px;">open_in_new</span>
            پیش‌نمایش
        </a>
    </div>

    <?php if(session('success')): ?>
        <div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
            <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div style="display:flex; gap:20px; align-items:flex-start;">

        
        <div style="width:240px; flex-shrink:0; display:flex; flex-direction:column; gap:16px;">

            
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
                <h3 style="font-size:13px; font-weight:700; color:#374151; margin:0 0 12px; display:flex; align-items:center; gap:6px;">
                    <span class="material-symbols-outlined" style="font-size:16px; color:#3b82f6;">style</span>
                    سبک کارت محصول
                </h3>
                <form action="<?php echo e(route('admin.homepage.card-style')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php $__currentLoopData = ['classic'=>'کلاسیک','modern'=>'مدرن','minimal'=>'مینیمال','horizontal'=>'افقی']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:8px; border:1px solid <?php echo e($cardStyle === $val ? '#3b82f6' : '#e5e7eb'); ?>; background:<?php echo e($cardStyle === $val ? '#eff6ff' : 'white'); ?>; margin-bottom:6px; cursor:pointer;">
                        <input type="radio" name="card_style" value="<?php echo e($val); ?>"
                               <?php echo e($cardStyle === $val ? 'checked' : ''); ?>

                               style="accent-color:#3b82f6;" onchange="this.form.submit()">
                        <span style="font-size:13px; font-weight:<?php echo e($cardStyle === $val ? '600' : '400'); ?>; color:<?php echo e($cardStyle === $val ? '#3b82f6' : '#374151'); ?>;"><?php echo e($lbl); ?></span>
                        <?php if($cardStyle === $val): ?>
                            <span class="material-symbols-outlined" style="font-size:14px; color:#3b82f6; margin-right:auto;">check_circle</span>
                        <?php endif; ?>
                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </form>
            </div>

            
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
                <h3 style="font-size:13px; font-weight:700; color:#374151; margin:0 0 12px; display:flex; align-items:center; gap:6px;">
                    <span class="material-symbols-outlined" style="font-size:16px; color:#3b82f6;">add_circle</span>
                    افزودن بلوک
                </h3>
                <?php $__currentLoopData = $blockTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button onclick="addBlock('<?php echo e($type); ?>')"
                        style="width:100%; display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px; border:1px solid #e5e7eb; background:white; cursor:pointer; font-size:13px; color:#374151; margin-bottom:5px; text-align:right; transition:all .15s;"
                        onmouseover="this.style.borderColor='#3b82f6';this.style.color='#3b82f6';this.style.background='#eff6ff'"
                        onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='white'">
                    <span class="material-symbols-outlined" style="font-size:16px; color:#9ca3af;"><?php echo e($info['icon']); ?></span>
                    <span><?php echo e($info['label']); ?></span>
                    <span class="material-symbols-outlined" style="font-size:12px; color:#d1d5db; margin-right:auto;">add</span>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
                <h3 style="font-size:13px; font-weight:700; color:#374151; margin:0 0 12px; display:flex; align-items:center; gap:6px;">
                    <span class="material-symbols-outlined" style="font-size:16px; color:#3b82f6;">emoji_symbols</span>
                    مرجع آیکون‌ها
                </h3>
                <button onclick="openIconPicker(null)"
                        style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:10px; border-radius:8px; border:1px dashed #3b82f6; background:#eff6ff; cursor:pointer; font-size:13px; color:#3b82f6; font-weight:600; transition:all .15s;"
                        onmouseover="this.style.background='#dbeafe'"
                        onmouseout="this.style.background='#eff6ff'">
                    <span class="material-symbols-outlined" style="font-size:18px;">grid_view</span>
                    مشاهده همه آیکون‌ها
                </button>
                <p style="font-size:11px; color:#9ca3af; margin:8px 0 0; text-align:center;">روی هر آیکون کلیک کنید تا نامش کپی شود</p>
            </div>

        </div>

        
        <div style="flex:1; min-width:0;">
            <div style="background:#f3f4f6; border-radius:12px; padding:16px; min-height:500px;">
                <p style="font-size:12px; color:#9ca3af; text-align:center; margin:0 0 12px;">برای جابجایی بلوک‌ها بکشید</p>
                <div id="blocks-canvas" style="display:flex; flex-direction:column; gap:8px;">
                    <?php $__empty_1 = true; $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('admin.homepage.partials.block-item', ['block' => $block], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div id="empty-state" style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; color:#9ca3af;">
                            <span class="material-symbols-outlined" style="font-size:48px; margin-bottom:8px;">dashboard_customize</span>
                            <p style="font-weight:500; margin:0;">صفحه اصلی خالی است</p>
                            <p style="font-size:13px; margin:4px 0 0;">از منوی سمت راست بلوک اضافه کنید</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>


<div id="edit-modal" style="position:fixed; inset:0; z-index:9999; display:none; align-items:center; justify-content:center; padding:16px;" dir="rtl">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,.6);" onclick="closeModal()"></div>
    <div style="position:relative; width:100%; max-width:580px; max-height:90vh; display:flex; flex-direction:column; background:white; border-radius:16px; box-shadow:0 25px 50px rgba(0,0,0,.25); overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f5f9; flex-shrink:0; background:white;">
            <h3 id="modal-title" style="font-size:15px; font-weight:700; color:#111827; margin:0;">ویرایش بلوک</h3>
            <button onclick="closeModal()" style="background:none; border:none; cursor:pointer; color:#9ca3af; padding:4px; display:flex; align-items:center; border-radius:6px;"
                    onmouseover="this.style.color='#374151';this.style.background='#f3f4f6'"
                    onmouseout="this.style.color='#9ca3af';this.style.background='none'">
                <span class="material-symbols-outlined" style="font-size:20px;">close</span>
            </button>
        </div>
        <div id="modal-body" style="overflow-y:auto; padding:20px; flex:1;"></div>
    </div>
</div>


<div id="icon-picker-modal" style="position:fixed; inset:0; z-index:10000; display:none; align-items:center; justify-content:center; padding:16px;" dir="rtl">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,.6);" onclick="closeIconPicker()"></div>
    <div style="position:relative; width:100%; max-width:860px; max-height:90vh; display:flex; flex-direction:column; background:white; border-radius:16px; box-shadow:0 25px 50px rgba(0,0,0,.25); overflow:hidden;">
        
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f1f5f9; flex-shrink:0;">
            <div>
                <h3 style="font-size:15px; font-weight:700; color:#111827; margin:0;">مرجع آیکون‌های Material Symbols</h3>
                <p style="font-size:12px; color:#6b7280; margin:2px 0 0;">روی هر آیکون کلیک کنید تا نامش کپی شود</p>
            </div>
            <button onclick="closeIconPicker()" style="background:none; border:none; cursor:pointer; color:#9ca3af; padding:4px; display:flex; align-items:center; border-radius:6px;">
                <span class="material-symbols-outlined" style="font-size:20px;">close</span>
            </button>
        </div>
        
        <div style="padding:12px 20px; border-bottom:1px solid #f1f5f9; flex-shrink:0;">
            <div style="position:relative;">
                <span class="material-symbols-outlined" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:18px; color:#9ca3af; pointer-events:none;">search</span>
                <input id="icon-search" type="text" placeholder="جستجوی آیکون..." oninput="filterIcons(this.value)"
                       style="width:100%; padding:8px 36px 8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box;"
                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
            <div id="icon-copy-toast" style="display:none; margin-top:8px; padding:6px 12px; background:#dcfce7; border:1px solid #86efac; color:#166534; border-radius:6px; font-size:12px; text-align:center;"></div>
        </div>
        
        <div id="icon-grid" style="overflow-y:auto; padding:16px 20px; flex:1; display:grid; grid-template-columns:repeat(auto-fill, minmax(90px, 1fr)); gap:8px;">
        </div>
        
        <div style="padding:12px 20px; border-top:1px solid #f1f5f9; flex-shrink:0; display:flex; align-items:center; justify-content:space-between;">
            <span id="icon-count" style="font-size:12px; color:#6b7280;"></span>
            <span style="font-size:11px; color:#9ca3af;">برای استفاده: <code style="background:#f3f4f6; padding:2px 6px; border-radius:4px; font-family:monospace;">&lt;span class="material-symbols-outlined"&gt;نام_آیکون&lt;/span&gt;</code></span>
        </div>
    </div>
</div>


<script id="blocks-data" type="application/json"><?php echo json_encode($blocks, 15, 512) ?></script>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const CSRF = '<?php echo e(csrf_token()); ?>';
const SAVE_URL   = '<?php echo e(route('admin.homepage.blocks.save')); ?>';
const ADD_URL    = '<?php echo e(route('admin.homepage.blocks.add')); ?>';
const BASE_URL   = '<?php echo e(url('admin/homepage/blocks')); ?>';
const DELETE_URL = id => `${BASE_URL}/${id}`;
const UPDATE_URL = id => `${BASE_URL}/${id}`;
const UPLOAD_URL = id => `${BASE_URL}/${id}/upload`;

// ذخیره configs در یه Map برای دسترسی سریع
const blockConfigs = new Map();
const rawBlocks = JSON.parse(document.getElementById('blocks-data').textContent);
rawBlocks.forEach(b => blockConfigs.set(b.id, b));

// Init Sortable - drag روی کل المان
Sortable.create(document.getElementById('blocks-canvas'), {
    animation: 200,
    ghostClass: 'opacity-30',
    onEnd: saveOrder,
});

function saveOrder() {
    const items = [...document.querySelectorAll('.block-item')];
    const blocks = items.map(el => {
        const stored = blockConfigs.get(el.dataset.id) || {};
        return {
            id:      el.dataset.id,
            type:    el.dataset.type,
            enabled: el.dataset.enabled !== 'false',
            config:  stored.config || {},
        };
    });
    fetch(SAVE_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({blocks}),
    }).then(r => r.json()).then(d => {
        if (d.success) showToast('ترتیب ذخیره شد');
    });
}

function addBlock(type) {
    fetch(ADD_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({type}),
    }).then(r => r.json()).then(d => {
        if (d.success) {
            document.getElementById('empty-state')?.remove();
            blockConfigs.set(d.block.id, d.block);
            document.getElementById('blocks-canvas').insertAdjacentHTML('beforeend', buildBlockHTML(d.block));
            showToast('بلوک اضافه شد');
        }
    });
}

function toggleBlock(id, btn) {
    const item = document.querySelector(`.block-item[data-id="${id}"]`);
    const isEnabled = item.dataset.enabled !== 'false';
    item.dataset.enabled = isEnabled ? 'false' : 'true';
    item.classList.toggle('opacity-50', isEnabled);
    btn.querySelector('span').textContent = isEnabled ? 'visibility_off' : 'visibility';
    saveOrder();
}

function deleteBlock(id) {
    if (!confirm('این بلوک حذف شود؟')) return;
    fetch(DELETE_URL(id), {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN':CSRF},
    }).then(r => r.json()).then(d => {
        if (d.success) {
            document.querySelector(`.block-item[data-id="${id}"]`)?.remove();
            blockConfigs.delete(id);
            if (!document.querySelector('.block-item')) {
                document.getElementById('blocks-canvas').innerHTML =
                    `<div id="empty-state" class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-2">dashboard_customize</span>
                        <p class="font-medium">صفحه اصلی خالی است</p>
                        <p class="text-sm mt-1">از منوی سمت چپ بلوک اضافه کنید</p>
                    </div>`;
            }
            showToast('بلوک حذف شد');
        }
    });
}

function editBlock(id) {
    const block = blockConfigs.get(id);
    if (!block) { alert('بلوک یافت نشد'); return; }
    document.getElementById('modal-title').textContent = 'ویرایش: ' + getTypeLabel(block.type);
    document.getElementById('modal-body').innerHTML = buildEditForm(id, block.type, block.config || {});
    const modal = document.getElementById('edit-modal');
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

function submitBlockForm(id) {
    const form = document.getElementById('block-edit-form');

    // چک کن آیا فایل انتخاب شده
    const fileInput = form.querySelector('input[name=hero_image]');
    const sb1Input  = form.querySelector('input[name=side_banner1_image]');
    const sb2Input  = form.querySelector('input[name=side_banner2_image]');
    // banner images
    const bannerFileInputs = Array.from(form.querySelectorAll('input[type=file][name^=banner_image_]'))
                                  .filter(el => el.files.length > 0);

    const hasFile = (fileInput && fileInput.files.length > 0)
                 || (sb1Input && sb1Input.files.length > 0)
                 || (sb2Input && sb2Input.files.length > 0)
                 || bannerFileInputs.length > 0;

    // ساخت config از فرم
    const config = buildConfigFromForm(form, id);

    // اول config رو ذخیره کن
    fetch(UPDATE_URL(id), {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({config}),
    }).then(r => {
        if (!r.ok) {
            return r.text().then(t => { throw new Error(`HTTP ${r.status}: ${t.substring(0,200)}`); });
        }
        return r.json();
    }).then(d => {
        if (!d.success) { showToast('خطا: ' + (d.message || 'ناشناخته'), 'error'); return; }

        // آپدیت blockConfigs
        const block = blockConfigs.get(id);
        if (block) { block.config = {...block.config, ...config}; blockConfigs.set(id, block); }

        if (hasFile) {
            // آپلود عکس جداگانه
            const uploadData = new FormData();
            uploadData.append('_token', CSRF);
            if (fileInput && fileInput.files.length > 0) uploadData.append('hero_image', fileInput.files[0]);
            const sb1 = form.querySelector('input[name=side_banner1_image]');
            const sb2 = form.querySelector('input[name=side_banner2_image]');
            if (sb1 && sb1.files.length > 0) uploadData.append('side_banner1_image', sb1.files[0]);
            if (sb2 && sb2.files.length > 0) uploadData.append('side_banner2_image', sb2.files[0]);
            // banner images
            bannerFileInputs.forEach(el => uploadData.append(el.name, el.files[0]));

            fetch(UPLOAD_URL(id), {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': CSRF},
                body: uploadData,
            }).then(r => {
                if (r.ok) {
                    closeModal();
                    showToast('تنظیمات و تصویر ذخیره شد');
                    setTimeout(() => location.reload(), 1500);
                }
            });
        } else {
            const item = document.querySelector(`.block-item[data-id="${id}"] .block-subtitle`);
            if (item) item.textContent = config.title || config.content || 'بدون عنوان';
            closeModal();
            showToast('تنظیمات ذخیره شد');
        }
    }).catch(err => { console.error('Save error:', err); showToast('خطا: ' + err.message); });
}

function buildConfigFromForm(form, id) {
    const stored = blockConfigs.get(id);
    const config = {};

    // همه input/select/textarea (غیر از file، checkbox، و radio)
    form.querySelectorAll('input:not([type=file]):not([type=checkbox]):not([type=radio]), select, textarea').forEach(el => {
        if (!el.name) return;
        parseFormField(config, el.name, el.value);
    });

    // radio - فقط checked
    form.querySelectorAll('input[type=radio]:checked').forEach(r => {
        if (!r.name) return;
        parseFormField(config, r.name, r.value);
    });

    // checkboxes - checked=true, unchecked=false
    // ابتدا همه رو false کن
    form.querySelectorAll('input[type=checkbox]').forEach(cb => {
        if (!cb.name) return;
        // برای array checkboxes (مثل category_ids[])
        if (cb.name.endsWith('[]')) {
            const baseName = cb.name.slice(0, -2);
            if (!config[baseName.replace('config[','').replace(']','')]) {
                // init array
            }
        }
    });

    // checkboxes با [] (array) - فقط checked ها
    const arrayCheckboxes = {};
    form.querySelectorAll('input[type=checkbox]').forEach(cb => {
        if (!cb.name) return;
        if (cb.name.includes('[]')) {
            const cleanName = cb.name.replace('[]', '');
            if (!arrayCheckboxes[cleanName]) arrayCheckboxes[cleanName] = [];
            if (cb.checked) arrayCheckboxes[cleanName].push(cb.value);
        } else {
            parseFormField(config, cb.name, cb.checked);
        }
    });
    // اضافه کردن array checkboxes به config
    for (const [name, values] of Object.entries(arrayCheckboxes)) {
        parseFormField(config, name, values);
    }

    // نگه داشتن custom_image قبلی (اگر remove_image نزده)
    if (stored?.config?.custom_image && config.custom_image === undefined && !config.remove_image) {
        config.custom_image = stored.config.custom_image;
    }
    if (config.remove_image) {
        config.custom_image = null;
        delete config.remove_image;
    }

    return config;
}

// تبدیل config[key] یا config[arr][0][field] به nested object
function parseFormField(obj, name, value) {
    // فقط فیلدهایی که با config[ شروع میشن
    if (!name.startsWith('config[')) return;

    // استخراج همه کلیدها: config[a][b][c] => ['a','b','c']
    const keys = [];
    const re = /\[([^\]]*)\]/g;
    let m;
    while ((m = re.exec(name)) !== null) {
        keys.push(m[1]);
    }
    if (keys.length === 0) return;

    // ساخت nested object
    let cur = obj;
    for (let i = 0; i < keys.length - 1; i++) {
        const k = keys[i];
        const nextKey = keys[i + 1];
        if (cur[k] === undefined || cur[k] === null || typeof cur[k] !== 'object') {
            cur[k] = isNaN(nextKey) || nextKey === '' ? {} : [];
        }
        cur = cur[k];
    }
    const lastKey = keys[keys.length - 1];
    cur[lastKey] = value;
}

function getTypeLabel(type) {
    const labels = {
        hero:'بنر اصلی', categories:'دسته‌بندی‌ها', auction_list:'لیست حراجی‌ها',
        trust_badges:'نشان‌های اعتماد', stats:'آمار سایت', newsletter:'خبرنامه',
        banner:'بنر تبلیغاتی', text_block:'متن آزاد', divider:'خط جداکننده'
    };
    return labels[type] || type;
}

function buildBlockHTML(block) {
    const icons = {
        hero:'image', categories:'category', auction_list:'gavel',
        trust_badges:'verified', stats:'bar_chart', newsletter:'mail',
        banner:'campaign', text_block:'text_fields', divider:'horizontal_rule'
    };
    const icon = icons[block.type] || 'widgets';
    const subtitle = block.config?.title || block.config?.content || 'بدون عنوان';
    return `<div class="block-item bg-white rounded-xl cursor-grab active:cursor-grabbing select-none"
         style="display:block; width:100%; border:1px solid #e5e7eb; border-radius:12px; transition:border-color .15s, box-shadow .15s;"
         onmouseover="this.style.borderColor='rgba(59,130,246,.4)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.08)'"
         onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
         data-id="${block.id}" data-type="${block.type}" data-enabled="true">
        <div style="display:flex; align-items:center; gap:12px; padding:12px 16px;">
            <span class="material-symbols-outlined" style="color:#d1d5db; font-size:20px; flex-shrink:0;">${icon}</span>
            <div style="flex:1; min-width:0;">
                <p style="font-weight:600; color:#1f2937; font-size:14px; margin:0;">${getTypeLabel(block.type)}</p>
                <p class="block-subtitle" style="font-size:12px; color:#9ca3af; margin:2px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${subtitle}</p>
            </div>
            <div style="display:flex; align-items:center; gap:2px; flex-shrink:0;" onclick="event.stopPropagation()">
                <button onclick="editBlock('${block.id}')"
                        style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                        onmouseover="this.style.color='#3b82f6';this.style.background='rgba(59,130,246,.1)'"
                        onmouseout="this.style.color='#9ca3af';this.style.background='none'" title="ویرایش">
                    <span class="material-symbols-outlined" style="font-size:18px;">edit</span>
                </button>
                <button onclick="toggleBlock('${block.id}', this)"
                        style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                        onmouseover="this.style.color='#374151';this.style.background='#f3f4f6'"
                        onmouseout="this.style.color='#9ca3af';this.style.background='none'" title="نمایش/پنهان">
                    <span class="material-symbols-outlined" style="font-size:18px;">visibility</span>
                </button>
                <button onclick="deleteBlock('${block.id}')"
                        style="padding:6px; color:#9ca3af; border:none; background:none; cursor:pointer; border-radius:8px; display:flex; align-items:center;"
                        onmouseover="this.style.color='#ef4444';this.style.background='#fef2f2'"
                        onmouseout="this.style.color='#9ca3af';this.style.background='none'" title="حذف">
                    <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
                </button>
            </div>
        </div>
    </div>`;
}

function buildEditForm(id, type, config) {
    let fields = '';

    if (type === 'hero') {
        const storageBase = '<?php echo e(url('storage')); ?>';
        const mode = config.mode || 'image'; // 'image' یا 'listings'
        fields = `
        <div style="display:flex; flex-direction:column; gap:16px;">

            
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                <p style="font-size:13px; font-weight:600; color:#374151; margin:0 0 10px;">نوع نمایش بنر اصلی</p>
                <div style="display:flex; gap:10px;">
                    <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:8px; border:2px solid ${mode==='image'?'#3b82f6':'#e5e7eb'}; background:${mode==='image'?'#eff6ff':'white'}; cursor:pointer;">
                        <input type="radio" name="config[mode]" value="image" ${mode==='image'?'checked':''} style="accent-color:#3b82f6;" onchange="toggleHeroMode(this.value)">
                        <div>
                            <p style="font-size:13px; font-weight:600; color:${mode==='image'?'#3b82f6':'#374151'}; margin:0;">تصویری</p>
                            <p style="font-size:11px; color:#6b7280; margin:0;">بنر با تصویر و متن</p>
                        </div>
                    </label>
                    <label style="flex:1; display:flex; align-items:center; gap:8px; padding:10px 12px; border-radius:8px; border:2px solid ${mode==='listings'?'#3b82f6':'#e5e7eb'}; background:${mode==='listings'?'#eff6ff':'white'}; cursor:pointer;">
                        <input type="radio" name="config[mode]" value="listings" ${mode==='listings'?'checked':''} style="accent-color:#3b82f6;" onchange="toggleHeroMode(this.value)">
                        <div>
                            <p style="font-size:13px; font-weight:600; color:${mode==='listings'?'#3b82f6':'#374151'}; margin:0;">لیست محصولات</p>
                            <p style="font-size:11px; color:#6b7280; margin:0;">نمایش آگهی‌های ویژه</p>
                        </div>
                    </label>
                </div>
            </div>

            
            <div id="hero-image-section" style="display:${mode==='image'?'flex':'none'}; flex-direction:column; gap:12px;">
                <div>
                    <label class="label">عنوان</label>
                    <input type="text" name="config[title]" value="${esc(config.title||'')}" class="input">
                </div>
                <div>
                    <label class="label">زیرعنوان</label>
                    <input type="text" name="config[subtitle]" value="${esc(config.subtitle||'')}" class="input">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label class="label">متن دکمه</label>
                        <input type="text" name="config[button_text]" value="${esc(config.button_text||'مشاهده مزایده‌ها')}" class="input">
                    </div>
                    <div>
                        <label class="label">لینک دکمه</label>
                        <input type="text" name="config[button_url]" value="${esc(config.button_url||'')}" class="input" placeholder="<?php echo e(route('listings.index')); ?>">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label class="label">رنگ پس‌زمینه</label>
                        <input type="color" name="config[bg_color]" value="${config.bg_color||'#1e40af'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;">
                    </div>
                    <div>
                        <label class="label">رنگ متن روی بنر</label>
                        <input type="color" name="config[text_color]" value="${config.text_color||'#ffffff'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;">
                    </div>
                    <div>
                        <label class="label">رنگ دکمه</label>
                        <input type="color" name="config[btn_bg]" value="${config.btn_bg||'#3b82f6'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;">
                    </div>
                    <div>
                        <label class="label">رنگ متن دکمه</label>
                        <input type="color" name="config[btn_text_color]" value="${config.btn_text_color||'#ffffff'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;">
                    </div>
                </div>
                <div>
                    <label class="label">موقعیت متن روی بنر</label>
                    <select name="config[text_position]" class="input">
                        <option value="right"  ${(config.text_position||'right')==='right'?'selected':''}>راست</option>
                        <option value="center" ${config.text_position==='center'?'selected':''}>وسط</option>
                        <option value="left"   ${config.text_position==='left'?'selected':''}>چپ</option>
                    </select>
                </div>
                <div>
                    <label class="label">بنرهای کناری</label>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; margin-top:4px;">
                        <input type="checkbox" name="config[show_side_banners]" value="1" ${config.show_side_banners !== false ? 'checked' : ''} style="accent-color:#3b82f6; width:16px; height:16px;" id="side-banners-toggle" onchange="toggleSideBanners(this.checked)">
                        <span style="font-size:13px; color:#374151;">نمایش بنرهای کناری</span>
                    </label>
                </div>
                

                <div>
                    <label class="label">تصویر بنر</label>
                    ${config.custom_image
                        ? `<div style="position:relative; margin-bottom:8px;">
                               <img src="${storageBase}/${config.custom_image}" style="width:100%; height:120px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;">
                               <span style="position:absolute; top:6px; right:6px; background:rgba(0,0,0,.5); color:white; font-size:11px; padding:2px 8px; border-radius:4px;">تصویر فعلی</span>
                           </div>`
                        : '<p style="font-size:12px; color:#9ca3af; margin:0 0 6px;">تصویری انتخاب نشده - از رنگ پس‌زمینه استفاده می‌شود</p>'
                    }
                    <input type="file" name="hero_image" accept="image/*" class="file-input">
                    <p style="font-size:11px; color:#9ca3af; margin:4px 0 0;">هر فرمتی (jpg, png, webp, gif) - حداکثر ۱۰ مگابایت</p>
                    ${config.custom_image ? `<label style="display:flex; align-items:center; gap:6px; margin-top:6px; cursor:pointer;">
                        <input type="checkbox" name="config[remove_image]" value="1" style="accent-color:#ef4444; width:14px; height:14px;">
                        <span style="font-size:12px; color:#ef4444;">حذف تصویر فعلی</span>
                    </label>` : ''}
                </div>
            </div>

            
            <div id="hero-listings-section" style="display:${mode==='listings'?'flex':'none'}; flex-direction:column; gap:12px;">
                <div>
                    <label class="label">عنوان بخش</label>
                    <input type="text" name="config[listings_title]" value="${esc(config.listings_title||'محصولات ویژه')}" class="input">
                </div>
                <div>
                    <label class="label">نوع آگهی‌های نمایشی</label>
                    <select name="config[listings_filter]" class="input">
                        <option value="ending_soon"   ${(config.listings_filter||'ending_soon')==='ending_soon'?'selected':''}>حراجی‌های لحظه آخری</option>
                        <option value="most_bids"     ${config.listings_filter==='most_bids'?'selected':''}>بیشترین پیشنهاد</option>
                        <option value="highest_price" ${config.listings_filter==='highest_price'?'selected':''}>بالاترین قیمت</option>
                        <option value="newest"        ${config.listings_filter==='newest'?'selected':''}>جدیدترین</option>
                        <option value="active"        ${config.listings_filter==='active'?'selected':''}>همه فعال‌ها</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div>
                        <label class="label">تعداد نمایش</label>
                        <input type="number" name="config[listings_count]" value="${config.listings_count||6}" min="2" max="12" class="input">
                    </div>
                    <div>
                        <label class="label">موقعیت متن روی اسلاید</label>
                        <select name="config[listings_text_pos]" class="input">
                            <option value="right"  ${(config.listings_text_pos||'right')==='right'?'selected':''}>راست</option>
                            <option value="center" ${config.listings_text_pos==='center'?'selected':''}>وسط</option>
                            <option value="left"   ${config.listings_text_pos==='left'?'selected':''}>چپ</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; align-items:end;">
                    <div>
                        <label class="label">رنگ پس‌زمینه (fallback)</label>
                        <input type="color" name="config[listings_bg]" value="${config.listings_bg||'#1e40af'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;">
                    </div>
                    <div>
                        <label class="label">بنرهای کناری</label>
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; margin-top:4px;">
                            <input type="checkbox" name="config[show_side_banners]" value="1" ${config.show_side_banners !== false ? 'checked' : ''} style="accent-color:#3b82f6; width:16px; height:16px;" onchange="toggleSideBanners(this.checked)">
                            <span style="font-size:13px; color:#374151;">نمایش بنرهای کناری</span>
                        </label>
                    </div>
                </div>
            </div>

            
            <div id="side-banners-config" style="display:${config.show_side_banners !== false ? 'block' : 'none'}; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                <p style="font-size:13px; font-weight:600; color:#374151; margin:0 0 12px;">تنظیمات بنرهای کناری</p>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                        <p style="font-size:12px; font-weight:600; color:#6b7280; margin:0 0 8px;">بنر بالایی</p>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div><label class="label">برچسب رنگی</label>
                                <input type="text" name="config[side_banner1_tag]" value="${esc(config.side_banner1_tag||'محصولات دیجیتال')}" class="input"></div>
                            <div><label class="label">عنوان</label>
                                <input type="text" name="config[side_banner1_title]" value="${esc(config.side_banner1_title||'گوشی و تبلت')}" class="input"></div>
                            <div><label class="label">توضیح</label>
                                <input type="text" name="config[side_banner1_desc]" value="${esc(config.side_banner1_desc||'جدیدترین محصولات در مزایده')}" class="input"></div>
                            <div><label class="label">لینک</label>
                                <input type="text" name="config[side_banner1_url]" value="${esc(config.side_banner1_url||'')}" class="input" placeholder="/listings?category=..."></div>
                            <div><label class="label">رنگ پس‌زمینه</label>
                                <input type="color" name="config[side_banner1_bg]" value="${config.side_banner1_bg||'#e0e7ff'}" style="width:40px; height:32px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;"></div>
                            <div><label class="label">رنگ متن</label>
                                <input type="color" name="config[side_banner1_color]" value="${config.side_banner1_color||'#3b82f6'}" style="width:40px; height:32px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;"></div>
                        </div>
                        <div style="margin-top:8px;">
                            <label class="label">تصویر بنر (سمت چپ)</label>
                            ${config.side_banner1_image ? `<img src="${storageBase}/${config.side_banner1_image}" style="width:80px; height:60px; object-fit:cover; border-radius:6px; margin-bottom:6px; border:1px solid #e5e7eb;">` : ''}
                            <input type="file" name="side_banner1_image" accept="image/*" class="file-input">
                        </div>
                    </div>
                    <div style="background:white; border:1px solid #e5e7eb; border-radius:8px; padding:10px;">
                        <p style="font-size:12px; font-weight:600; color:#6b7280; margin:0 0 8px;">بنر پایینی</p>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div><label class="label">برچسب رنگی</label>
                                <input type="text" name="config[side_banner2_tag]" value="${esc(config.side_banner2_tag||'ساعت و جواهرات')}" class="input"></div>
                            <div><label class="label">عنوان</label>
                                <input type="text" name="config[side_banner2_title]" value="${esc(config.side_banner2_title||'ساعت‌های کلاسیک')}" class="input"></div>
                            <div><label class="label">توضیح</label>
                                <input type="text" name="config[side_banner2_desc]" value="${esc(config.side_banner2_desc||'مزایده برندهای معتبر')}" class="input"></div>
                            <div><label class="label">لینک</label>
                                <input type="text" name="config[side_banner2_url]" value="${esc(config.side_banner2_url||'')}" class="input" placeholder="/listings?category=..."></div>
                            <div><label class="label">رنگ پس‌زمینه</label>
                                <input type="color" name="config[side_banner2_bg]" value="${config.side_banner2_bg||'#fff7ed'}" style="width:40px; height:32px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;"></div>
                            <div><label class="label">رنگ متن</label>
                                <input type="color" name="config[side_banner2_color]" value="${config.side_banner2_color||'#f97316'}" style="width:40px; height:32px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;"></div>
                        </div>
                        <div style="margin-top:8px;">
                            <label class="label">تصویر بنر (سمت چپ)</label>
                            ${config.side_banner2_image ? `<img src="${storageBase}/${config.side_banner2_image}" style="width:80px; height:60px; object-fit:cover; border-radius:6px; margin-bottom:6px; border:1px solid #e5e7eb;">` : ''}
                            <input type="file" name="side_banner2_image" accept="image/*" class="file-input">
                        </div>
                    </div>
                </div>
            </div>

        </div>`;
    } else if (type === 'categories') {
        fields = `
        <div class="space-y-4">
            <div><label class="label">عنوان</label>
                <input type="text" name="config[title]" value="${esc(config.title||'')}" class="input"></div>
            <div><label class="label">تعداد نمایش</label>
                <input type="number" name="config[count]" value="${config.count||8}" min="4" max="20" class="input"></div>
            <div><label class="label">سبک نمایش</label>
                <select name="config[style]" class="input">
                    <option value="circle" ${config.style==='circle'?'selected':''}>دایره‌ای</option>
                    <option value="card"   ${config.style==='card'?'selected':''}>کارتی</option>
                    <option value="pill"   ${config.style==='pill'?'selected':''}>قرص‌شکل</option>
                </select>
            </div>
        </div>`;
    } else if (type === 'listings_grid') {
        fields = `
        <div style="display:flex; flex-direction:column; gap:12px;">
            <div><label class="label">عنوان</label>
                <input type="text" name="config[title]" value="${esc(config.title||'')}" class="input"></div>
            <div><label class="label">زیرعنوان (اختیاری)</label>
                <input type="text" name="config[subtitle]" value="${esc(config.subtitle||'')}" class="input"></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div><label class="label">تعداد آگهی</label>
                    <input type="number" name="config[count]" value="${config.count||8}" min="2" max="24" class="input"></div>
                <div><label class="label">تعداد ستون</label>
                    <select name="config[columns]" class="input">
                        <option value="2" ${config.columns==2?'selected':''}>۲ ستون</option>
                        <option value="3" ${config.columns==3?'selected':''}>۳ ستون</option>
                        <option value="4" ${config.columns==4?'selected':''}>۴ ستون</option>
                    </select>
                </div>
            </div>
            <div><label class="label">نوع آگهی‌ها</label>
                <select name="config[filter]" class="input">
                    <option value="active"       ${config.filter==='active'?'selected':''}>فعال (در حال مزایده)</option>
                    <option value="latest"       ${config.filter==='latest'?'selected':''}>جدیدترین</option>
                    <option value="ending"       ${config.filter==='ending'?'selected':''}>در حال اتمام</option>
                    <option value="most_bids"    ${config.filter==='most_bids'?'selected':''}>بیشترین پیشنهاد</option>
                    <option value="ending_soon"  ${config.filter==='ending_soon'?'selected':''}>لحظه آخری</option>
                </select>
            </div>
            <div><label class="label">آیکون بخش (Material Symbol)</label>
                <input type="text" name="config[icon]" value="${esc(config.icon||'grid_view')}" class="input" placeholder="local_fire_department">
            </div>
        </div>`;
    } else if (type === 'auction_list') {
        const allCats = <?php echo json_encode(\App\Models\Category::whereNull('parent_id')->orderBy('name')->with(['children' => fn($q) => $q->orderBy('name')->with(['children' => fn($q2) => $q2->orderBy('name')])])->get(['id', 'name', 'parent_id'])) ?>;
        const selectedCats = (config.category_ids || []).map(String);

        function buildCatTree(cats, level = 0) {
            return cats.map(c => {
                const checked = selectedCats.includes(String(c.id));
                const indent = level * 16;
                const prefix = level === 0 ? '' : (level === 1 ? '└ ' : '  └ ');
                let html = `<label style="display:flex; align-items:center; gap:6px; padding:4px 0; cursor:pointer; font-size:${13 - level}px; padding-right:${indent}px;">
                    <input type="checkbox" name="config[category_ids][]" value="${c.id}"
                           ${checked ? 'checked' : ''}
                           style="accent-color:#3b82f6; width:14px; height:14px; flex-shrink:0;">
                    <span style="font-weight:${level === 0 ? '600' : '400'}; color:${level === 0 ? '#1f2937' : '#6b7280'};">${prefix}${c.name}</span>
                </label>`;
                if (c.children && c.children.length > 0) {
                    html += buildCatTree(c.children, level + 1);
                }
                return html;
            }).join('');
        }

        const catCheckboxes = buildCatTree(allCats);
        fields = `
        <div style="display:flex; flex-direction:column; gap:12px;">
            <div><label class="label">عنوان</label>
                <input type="text" name="config[title]" value="${esc(config.title||'آگهی‌های ویژه')}" class="input"></div>
            <div><label class="label">زیرعنوان (اختیاری)</label>
                <input type="text" name="config[subtitle]" value="${esc(config.subtitle||'')}" class="input"></div>
            <div><label class="label">نوع آگهی‌ها</label>
                <select name="config[filter]" class="input">
                    <option value="ending_soon"   ${(config.filter||'ending_soon')==='ending_soon'?'selected':''}>لحظه آخری (کمترین زمان)</option>
                    <option value="most_bids"     ${config.filter==='most_bids'?'selected':''}>بیشترین پیشنهاد</option>
                    <option value="highest_price" ${config.filter==='highest_price'?'selected':''}>بالاترین قیمت</option>
                    <option value="newest"        ${config.filter==='newest'?'selected':''}>جدیدترین</option>
                    <option value="active"        ${config.filter==='active'?'selected':''}>همه فعال‌ها</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div><label class="label">تعداد نمایش</label>
                    <input type="number" name="config[count]" value="${config.count||6}" min="2" max="12" class="input"></div>
                <div><label class="label">تعداد ستون</label>
                    <select name="config[columns]" class="input">
                        <option value="2" ${(config.columns||3)==2?'selected':''}>۲ ستون</option>
                        <option value="3" ${(config.columns||3)==3?'selected':''}>۳ ستون</option>
                        <option value="4" ${(config.columns||3)==4?'selected':''}>۴ ستون</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="label">فیلتر دسته‌بندی (خالی = همه دسته‌ها)</label>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; max-height:160px; overflow-y:auto;">
                    ${catCheckboxes || '<p style="font-size:12px;color:#9ca3af;">دسته‌بندی یافت نشد</p>'}
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div><label class="label">رنگ پس‌زمینه</label>
                    <input type="color" name="config[bg_color]" value="${config.bg_color||'#1e40af'}" style="width:48px; height:36px; border-radius:6px; border:1px solid #d1d5db; cursor:pointer; padding:2px;"></div>
                <div><label class="label">رنگ متن</label>
                    <select name="config[text_color]" class="input">
                        <option value="white" ${(config.text_color||'white')==='white'?'selected':''}>سفید</option>
                        <option value="dark"  ${config.text_color==='dark'?'selected':''}>تیره</option>
                    </select>
                </div>
            </div>
        </div>`;
    } else if (type === 'trust_badges') {
        const badges = config.badges || [];
        const renderBadgeRows = (items) => items.map((b,i) => `
            <div class="badge-row grid gap-2 p-3 bg-gray-50 rounded-lg border border-gray-100 mb-2" style="grid-template-columns:1fr 1fr 1fr auto;" data-index="${i}">
                <div>
                    <label class="label">آیکون</label>
                    <div style="display:flex;gap:4px;">
                        <input type="text" name="config[badges][${i}][icon]" value="${esc(b.icon||'')}" class="input text-xs" placeholder="verified_user" style="flex:1;">
                        <button type="button" onclick="openIconPicker(v=>{this.previousElementSibling.value=v})" style="padding:4px 6px;border:1px solid #e5e7eb;border-radius:6px;background:white;cursor:pointer;font-size:11px;color:#3b82f6;" title="انتخاب آیکون">
                            <span class="material-symbols-outlined" style="font-size:14px;">grid_view</span>
                        </button>
                    </div>
                </div>
                <div><label class="label">عنوان</label>
                    <input type="text" name="config[badges][${i}][title]" value="${esc(b.title||'')}" class="input text-xs"></div>
                <div><label class="label">توضیح</label>
                    <input type="text" name="config[badges][${i}][desc]" value="${esc(b.desc||'')}" class="input text-xs"></div>
                <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                    <button type="button" onclick="removeDynamicRow(this,'badges-container')"
                            style="padding:6px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;">
                        <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                    </button>
                </div>
            </div>`).join('');

        fields = `<div class="space-y-2">
            <div id="badges-container">${renderBadgeRows(badges)}</div>
            <button type="button" onclick="addDynamicRow('badges-container','badge')"
                    style="width:100%;padding:8px;border:1px dashed #3b82f6;border-radius:8px;background:#eff6ff;color:#3b82f6;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:16px;">add</span> افزودن نشان
            </button>
        </div>`;
    } else if (type === 'stats') {
        const items = config.items || [];
        const renderStatRows = (its) => its.map((s,i) => `
            <div class="stat-row grid gap-2 p-3 bg-gray-50 rounded-lg border border-gray-100 mb-2" style="grid-template-columns:1fr 1fr 1fr auto;" data-index="${i}">
                <div>
                    <label class="label">آیکون</label>
                    <div style="display:flex;gap:4px;">
                        <input type="text" name="config[items][${i}][icon]" value="${esc(s.icon||'')}" class="input text-xs" placeholder="star" style="flex:1;">
                        <button type="button" onclick="openIconPicker(v=>{this.previousElementSibling.value=v})" style="padding:4px 6px;border:1px solid #e5e7eb;border-radius:6px;background:white;cursor:pointer;font-size:11px;color:#3b82f6;" title="انتخاب آیکون">
                            <span class="material-symbols-outlined" style="font-size:14px;">grid_view</span>
                        </button>
                    </div>
                </div>
                <div><label class="label">مقدار</label>
                    <input type="text" name="config[items][${i}][value]" value="${esc(s.value||'')}" class="input text-xs" placeholder="۱۰,۰۰۰+"></div>
                <div><label class="label">برچسب</label>
                    <input type="text" name="config[items][${i}][label]" value="${esc(s.label||'')}" class="input text-xs"></div>
                <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                    <button type="button" onclick="removeDynamicRow(this,'stats-container')"
                            style="padding:6px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;">
                        <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                    </button>
                </div>
            </div>`).join('');

        fields = `<div class="space-y-2">
            <div id="stats-container">${renderStatRows(items)}</div>
            <button type="button" onclick="addDynamicRow('stats-container','stat')"
                    style="width:100%;padding:8px;border:1px dashed #3b82f6;border-radius:8px;background:#eff6ff;color:#3b82f6;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:16px;">add</span> افزودن آمار
            </button>
        </div>`;
    } else if (type === 'newsletter') {
        fields = `
        <div class="space-y-4">
            <div><label class="label">عنوان</label>
                <input type="text" name="config[title]" value="${esc(config.title||'')}" class="input"></div>
            <div><label class="label">زیرعنوان</label>
                <input type="text" name="config[subtitle]" value="${esc(config.subtitle||'')}" class="input"></div>
            <div><label class="label">رنگ پس‌زمینه</label>
                <input type="color" name="config[bg_color]" value="${config.bg_color||'#1e40af'}" class="w-12 h-10 rounded border border-gray-300 cursor-pointer"></div>
        </div>`;
    } else if (type === 'banner') {
        const banners = config.banners || [];
        // backward compat
        if (banners.length === 0 && (config.title || config.custom_image)) {
            banners.push({
                title: config.title||'', subtitle: config.subtitle||'',
                button_text: config.button_text||'', button_url: config.button_url||'',
                bg_color: config.bg_color||'#f59e0b', custom_image: config.custom_image||''
            });
        }
        const renderBannerRows = (items) => items.map((b,i) => `
            <div class="banner-row p-3 bg-gray-50 rounded-lg border border-gray-100 mb-3" data-index="${i}">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                    <div><label class="label">عنوان</label>
                        <input type="text" name="config[banners][${i}][title]" value="${esc(b.title||'')}" class="input text-xs"></div>
                    <div><label class="label">زیرعنوان</label>
                        <input type="text" name="config[banners][${i}][subtitle]" value="${esc(b.subtitle||'')}" class="input text-xs"></div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                    <div><label class="label">متن دکمه</label>
                        <input type="text" name="config[banners][${i}][button_text]" value="${esc(b.button_text||'')}" class="input text-xs"></div>
                    <div><label class="label">لینک دکمه</label>
                        <input type="text" name="config[banners][${i}][button_url]" value="${esc(b.button_url||'')}" class="input text-xs" placeholder="https://..."></div>
                </div>
                <div style="display:flex; gap:8px; align-items:flex-end; margin-bottom:8px;">
                    <div style="flex:1;"><label class="label">رنگ پس‌زمینه</label>
                        <input type="color" name="config[banners][${i}][bg_color]" value="${b.bg_color||'#f59e0b'}" style="width:48px;height:32px;border-radius:6px;border:1px solid #d1d5db;cursor:pointer;"></div>
                    <button type="button" onclick="removeDynamicRow(this,'banners-container')"
                            style="padding:6px 10px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;font-size:12px;">
                        <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                    </button>
                </div>
                <div>
                    <label class="label">تصویر پس‌زمینه (اختیاری)</label>
                    ${b.custom_image ? `<img src="<?php echo e(url('storage')); ?>/${b.custom_image}" style="width:100%; height:80px; object-fit:cover; border-radius:6px; margin-bottom:6px;">
                    <input type="hidden" name="config[banners][${i}][custom_image]" value="${esc(b.custom_image||'')}">` : `<input type="hidden" name="config[banners][${i}][custom_image]" value="">`}
                    <input type="file" name="banner_image_${i}" accept="image/*" class="file-input">
                </div>
            </div>`).join('');

        fields = `<div class="space-y-2">
            <div id="banners-container">${renderBannerRows(banners)}</div>
            <button type="button" onclick="addDynamicRow('banners-container','banner')"
                    style="width:100%;padding:8px;border:1px dashed #3b82f6;border-radius:8px;background:#eff6ff;color:#3b82f6;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:16px;">add</span> افزودن بنر
            </button>
            <p class="text-xs text-gray-400 mt-2">بنرها به صورت خودکار responsive می‌شوند و در دستگاه‌های کوچک به سطر بعدی می‌روند.</p>
        </div>`;
    } else if (type === 'text_block') {
        fields = `
        <div class="space-y-4">
            <div><label class="label">محتوا</label>
                <textarea name="config[content]" rows="4" class="input">${esc(config.content||'')}</textarea></div>
            <div><label class="label">تراز متن</label>
                <select name="config[align]" class="input">
                    <option value="right"  ${config.align==='right'?'selected':''}>راست</option>
                    <option value="center" ${config.align==='center'?'selected':''}>وسط</option>
                    <option value="left"   ${config.align==='left'?'selected':''}>چپ</option>
                </select>
            </div>
        </div>`;
    } else {
        fields = `<p class="text-gray-500 text-sm py-4 text-center">این بلوک تنظیمات ندارد.</p>`;
    }

    const hasFileUpload = (type === 'hero' || type === 'banner');

    return `<form id="block-edit-form" ${hasFileUpload ? 'enctype="multipart/form-data"' : ''}>
        <input type="hidden" name="_token" value="${CSRF}">
        ${fields}
        <div class="flex justify-end gap-3 pt-5 mt-5 border-t border-gray-100">
            <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                انصراف
            </button>
            <button type="button" onclick="submitBlockForm('${id}')"
                    class="px-6 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                ذخیره
            </button>
        </div>
    </form>`;
}

function toggleHeroMode(mode) {
    const imgSection = document.getElementById('hero-image-section');
    const lstSection = document.getElementById('hero-listings-section');
    if (!imgSection || !lstSection) return;
    imgSection.style.display = mode === 'image' ? 'flex' : 'none';
    lstSection.style.display = mode === 'listings' ? 'flex' : 'none';
    document.querySelectorAll('input[name="config[mode]"]').forEach(r => {
        const lbl = r.closest('label');
        if (lbl) {
            lbl.style.borderColor = r.checked ? '#3b82f6' : '#e5e7eb';
            lbl.style.background  = r.checked ? '#eff6ff' : 'white';
        }
    });
}

function toggleSideBanners(checked) {
    const el = document.getElementById('side-banners-config');
    if (el) el.style.display = checked ? 'block' : 'none';
}

function esc(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/"/g,'&quot;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/'/g,'&#39;');
}

function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.className = 'fixed top-5 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-5 py-2.5 rounded-xl shadow-xl z-[200] text-sm flex items-center gap-2';
    t.innerHTML = `<span class="material-symbols-outlined text-green-400 text-sm">check_circle</span>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

// ===== Dynamic Row Add/Remove =====
function removeDynamicRow(btn, containerId) {
    btn.closest('[data-index]').remove();
    reindexRows(containerId);
}

function reindexRows(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const rows = container.querySelectorAll('[data-index]');
    const isBadge = containerId === 'badges-container';
    const isBanner = containerId === 'banners-container';
    const key = isBadge ? 'badges' : (isBanner ? 'banners' : 'items');
    rows.forEach((row, i) => {
        row.setAttribute('data-index', i);
        row.querySelectorAll('input, select, textarea').forEach(inp => {
            if (inp.name) inp.name = inp.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
}

function addDynamicRow(containerId, rowType) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const i = container.querySelectorAll('[data-index]').length;
    const isBadge = rowType === 'badge';
    const isBanner = rowType === 'banner';

    const div = document.createElement('div');
    div.setAttribute('data-index', i);

    if (isBanner) {
        div.className = 'banner-row';
        div.style.cssText = 'padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;margin-bottom:12px;';
        div.innerHTML = `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                <div><label class="label">عنوان</label>
                    <input type="text" name="config[banners][${i}][title]" value="" class="input text-xs"></div>
                <div><label class="label">زیرعنوان</label>
                    <input type="text" name="config[banners][${i}][subtitle]" value="" class="input text-xs"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                <div><label class="label">متن دکمه</label>
                    <input type="text" name="config[banners][${i}][button_text]" value="" class="input text-xs"></div>
                <div><label class="label">لینک دکمه</label>
                    <input type="text" name="config[banners][${i}][button_url]" value="" class="input text-xs" placeholder="https://..."></div>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end;margin-bottom:8px;">
                <div style="flex:1;"><label class="label">رنگ پس‌زمینه</label>
                    <input type="color" name="config[banners][${i}][bg_color]" value="#f59e0b" style="width:48px;height:32px;border-radius:6px;border:1px solid #d1d5db;cursor:pointer;"></div>
                <button type="button" onclick="removeDynamicRow(this,'banners-container')"
                        style="padding:6px 10px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;">
                    <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                </button>
            </div>
            <div>
                <label class="label">تصویر پس‌زمینه (اختیاری)</label>
                <input type="hidden" name="config[banners][${i}][custom_image]" value="">
                <input type="file" name="banner_image_${i}" accept="image/*" class="file-input">
            </div>`;
        container.appendChild(div);
        return;
    }

    div.className = (isBadge ? 'badge-row' : 'stat-row');
    div.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:8px;padding:12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;margin-bottom:8px;';

    if (isBadge) {
        div.innerHTML = `
            <div><label class="label">آیکون</label>
                <div style="display:flex;gap:4px;">
                    <input type="text" name="config[badges][${i}][icon]" value="" class="input text-xs" placeholder="verified_user" style="flex:1;">
                    <button type="button" onclick="openIconPicker(v=>{this.previousElementSibling.value=v})" style="padding:4px 6px;border:1px solid #e5e7eb;border-radius:6px;background:white;cursor:pointer;color:#3b82f6;">
                        <span class="material-symbols-outlined" style="font-size:14px;">grid_view</span>
                    </button>
                </div>
            </div>
            <div><label class="label">عنوان</label>
                <input type="text" name="config[badges][${i}][title]" value="" class="input text-xs"></div>
            <div><label class="label">توضیح</label>
                <input type="text" name="config[badges][${i}][desc]" value="" class="input text-xs"></div>
            <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                <button type="button" onclick="removeDynamicRow(this,'badges-container')"
                        style="padding:6px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;">
                    <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                </button>
            </div>`;
    } else {
        div.innerHTML = `
            <div><label class="label">آیکون</label>
                <div style="display:flex;gap:4px;">
                    <input type="text" name="config[items][${i}][icon]" value="" class="input text-xs" placeholder="star" style="flex:1;">
                    <button type="button" onclick="openIconPicker(v=>{this.previousElementSibling.value=v})" style="padding:4px 6px;border:1px solid #e5e7eb;border-radius:6px;background:white;cursor:pointer;color:#3b82f6;">
                        <span class="material-symbols-outlined" style="font-size:14px;">grid_view</span>
                    </button>
                </div>
            </div>
            <div><label class="label">مقدار</label>
                <input type="text" name="config[items][${i}][value]" value="" class="input text-xs" placeholder="۱۰,۰۰۰+"></div>
            <div><label class="label">برچسب</label>
                <input type="text" name="config[items][${i}][label]" value="" class="input text-xs"></div>
            <div style="display:flex;align-items:flex-end;padding-bottom:2px;">
                <button type="button" onclick="removeDynamicRow(this,'stats-container')"
                        style="padding:6px;border:1px solid #fecaca;border-radius:6px;background:#fff5f5;cursor:pointer;color:#ef4444;">
                    <span class="material-symbols-outlined" style="font-size:14px;">delete</span>
                </button>
            </div>`;
    }
    container.appendChild(div);
}

// ===== Icon Picker =====
const ALL_ICONS = [
    'home','search','settings','favorite','star','delete','edit','add','remove','close','check','arrow_back','arrow_forward',
    'menu','more_vert','more_horiz','refresh','share','download','upload','print','save','copy','cut','paste',
    'visibility','visibility_off','lock','lock_open','person','people','group','account_circle','face','badge',
    'email','phone','message','chat','notifications','notification_add','alarm','schedule','calendar_today','event',
    'location_on','map','navigation','directions','place','explore','public','language','translate',
    'shopping_cart','store','storefront','inventory','local_shipping','delivery_dining','package_2',
    'payment','credit_card','wallet','account_balance','attach_money','money','currency_exchange','receipt',
    'gavel','auction','sell','local_offer','discount','percent','price_tag',
    'image','photo','camera','videocam','mic','volume_up','music_note','headphones','tv','monitor',
    'computer','laptop','phone_android','tablet','watch','keyboard','mouse','print','scanner',
    'wifi','bluetooth','signal_cellular_alt','battery_full','power','usb','cable',
    'folder','file_copy','description','article','book','library_books','note','sticky_note_2',
    'dashboard','widgets','grid_view','list','view_list','view_module','table_chart','bar_chart','pie_chart',
    'trending_up','trending_down','analytics','insights','assessment','leaderboard',
    'check_circle','cancel','error','warning','info','help','report','flag','bookmark','label',
    'thumb_up','thumb_down','grade','star_border','star_half','emoji_events','military_tech',
    'verified','verified_user','security','shield','admin_panel_settings','manage_accounts',
    'build','construction','engineering','handyman','plumbing','electrical_services',
    'local_hospital','medical_services','health_and_safety','fitness_center','spa',
    'restaurant','local_cafe','local_bar','fastfood','lunch_dining','dinner_dining',
    'flight','hotel','car_rental','directions_car','train','bus_alert','two_wheeler',
    'school','science','psychology','biotech','calculate','functions','code','terminal',
    'palette','brush','format_paint','design_services','architecture','auto_fix_high',
    'sunny','cloud','thunderstorm','water_drop','ac_unit','local_fire_department',
    'pets','nature','park','forest','eco','recycling','energy_savings_leaf',
    'sports_soccer','sports_basketball','sports_tennis','sports_esports','casino','toys',
    'celebration','cake','card_giftcard','redeem','volunteer_activism','handshake',
    'open_in_new','link','launch','qr_code','barcode','fingerprint','face_retouching_natural',
    'tune','filter_list','sort','swap_vert','swap_horiz','compare_arrows','sync',
    'zoom_in','zoom_out','fullscreen','fullscreen_exit','crop','rotate_right','flip',
    'format_bold','format_italic','format_underlined','format_list_bulleted','format_list_numbered',
    'title','text_fields','font_download','spellcheck','translate','record_voice_over',
    'attach_file','attachment','cloud_upload','cloud_download','backup','restore',
    'history','undo','redo','replay','fast_forward','fast_rewind','play_arrow','pause','stop',
    'skip_next','skip_previous','shuffle','repeat','queue_music','playlist_add',
    'brightness_high','brightness_low','contrast','exposure','wb_sunny','dark_mode',
    'add_circle','remove_circle','add_box','indeterminate_check_box','check_box',
    'radio_button_checked','toggle_on','toggle_off','switch_left','switch_right',
    'expand_more','expand_less','chevron_right','chevron_left','unfold_more','unfold_less',
    'first_page','last_page','navigate_next','navigate_before','arrow_upward','arrow_downward',
    'open_with','drag_indicator','drag_handle','pan_tool','touch_app','gesture',
    'support_agent','headset_mic','contact_support','live_help','forum','comment','rate_review',
    'send','reply','forward','inbox','outbox','drafts','markunread','mark_email_read',
    'inventory_2','category','layers','view_in_ar','3d_rotation','360',
    'emoji_symbols','emoji_emotions','emoji_nature','emoji_food_beverage','emoji_travel',
    'tag','new_releases','fiber_new','update','upgrade','system_update',
    'done','done_all','done_outline','task_alt','assignment_turned_in','grading',
    'block','do_not_disturb','not_interested','hide_source','unpublished',
    'pending','pending_actions','hourglass_empty','hourglass_top','timer','timer_off',
    'bolt','flash_on','electric_bolt','power_settings_new','restart_alt',
    'key','vpn_key','password','pin','pattern','security_update_good',
    'storefront','business','corporate_fare','domain','apartment','house','cottage',
    'local_atm','point_of_sale','request_quote','price_check','money_off',
    'workspace_premium','diamond','crown','auto_awesome','stars','flare',
    'rocket_launch','satellite','travel_explore','globe_asia','language',
    'format_quote','chat_bubble','sms','textsms','mark_chat_read','mark_chat_unread',
];

let iconPickerCallback = null;

function openIconPicker(callback) {
    iconPickerCallback = callback;
    document.getElementById('icon-picker-modal').style.display = 'flex';
    document.getElementById('icon-search').value = '';
    renderIcons(ALL_ICONS);
    setTimeout(() => document.getElementById('icon-search').focus(), 100);
}

function closeIconPicker() {
    document.getElementById('icon-picker-modal').style.display = 'none';
    iconPickerCallback = null;
}

function renderIcons(icons) {
    const grid = document.getElementById('icon-grid');
    document.getElementById('icon-count').textContent = icons.length + ' آیکون';
    grid.innerHTML = icons.map(icon => `
        <div onclick="selectIcon('${icon}')"
             title="${icon}"
             style="display:flex; flex-direction:column; align-items:center; gap:4px; padding:10px 6px; border-radius:8px; border:1px solid #f3f4f6; cursor:pointer; transition:all .15s; background:white;"
             onmouseover="this.style.borderColor='#3b82f6';this.style.background='#eff6ff';this.style.transform='scale(1.05)'"
             onmouseout="this.style.borderColor='#f3f4f6';this.style.background='white';this.style.transform='scale(1)'">
            <span class="material-symbols-outlined" style="font-size:24px; color:#374151;">${icon}</span>
            <span style="font-size:9px; color:#6b7280; text-align:center; word-break:break-all; line-height:1.2;">${icon}</span>
        </div>
    `).join('');
}

function filterIcons(query) {
    const q = query.toLowerCase().trim();
    const filtered = q ? ALL_ICONS.filter(i => i.includes(q)) : ALL_ICONS;
    renderIcons(filtered);
}

function selectIcon(icon) {
    // کپی به clipboard
    navigator.clipboard.writeText(icon).then(() => {
        const toast = document.getElementById('icon-copy-toast');
        toast.textContent = '✓ نام آیکون کپی شد: ' + icon;
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 2000);
    }).catch(() => {
        // fallback
        const el = document.createElement('textarea');
        el.value = icon;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        const toast = document.getElementById('icon-copy-toast');
        toast.textContent = '✓ نام آیکون کپی شد: ' + icon;
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 2000);
    });

    // اگر callback داشت (از input field)، مقدار رو set کن
    if (iconPickerCallback) {
        iconPickerCallback(icon);
        closeIconPicker();
    }
}

// ESC برای بستن
document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && document.getElementById('icon-picker-modal').style.display === 'flex') {
        closeIconPicker();
    }
});
</script>
<style>
.label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.3rem; }
.input { width:100%; border:1px solid #d1d5db; border-radius:.5rem; padding:.5rem .75rem; font-size:.875rem; outline:none; background:white; }
.input:focus { border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
.file-input { width:100%; font-size:.8rem; color:#6b7280; }
.file-input::file-selector-button { margin-left:.75rem; padding:.4rem .75rem; border-radius:.5rem; border:none; font-size:.8rem; font-weight:500; background:rgba(59,130,246,.1); color:#3b82f6; cursor:pointer; }
.file-input::file-selector-button:hover { background:rgba(59,130,246,.2); }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\homepage\index.blade.php ENDPATH**/ ?>