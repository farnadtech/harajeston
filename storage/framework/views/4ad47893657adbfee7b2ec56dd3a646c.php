
<?php $__env->startSection('title', 'شخصی‌سازی هدر و فوتر'); ?>

<?php $__env->startSection('content'); ?>
<div dir="rtl" x-data="{ tab: 'header' }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">شخصی‌سازی هدر و فوتر</h1>
            <p class="text-sm text-gray-500 mt-1">تنظیمات در کل سایت اعمال می‌شود</p>
        </div>
        <a href="<?php echo e(route('home')); ?>" target="_blank"
           class="flex items-center gap-2 border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-base">open_in_new</span>
            پیش‌نمایش
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6 w-fit">
        <?php $__currentLoopData = [['header','هدر عمومی','web'], ['footer','فوتر','bottom_panel_close'], ['dashboard','هدر داشبورد','dashboard']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$key,$label,$icon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button @click="tab='<?php echo e($key); ?>'"
                :class="tab==='<?php echo e($key); ?>' ? 'bg-white shadow text-primary font-semibold' : 'text-gray-600 hover:text-gray-900'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all">
            <span class="material-symbols-outlined text-base"><?php echo e($icon); ?></span>
            <?php echo e($label); ?>

        </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="POST" action="<?php echo e(route('admin.theme.save')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        
        <div x-show="tab==='header'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">image</span>
                        لوگو
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تصویر لوگو (اختیاری)</label>
                            <?php if($settings['header_logo']): ?>
                                <img src="<?php echo e(url('storage/'.$settings['header_logo'])); ?>" class="h-10 mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" name="header_logo_file" accept="image/*" class="text-sm text-gray-600 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 file:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">متن لوگو</label>
                            <input type="text" name="header_logo_text" value="<?php echo e($settings['header_logo_text']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">آیکون لوگو</label>
                            <div class="flex gap-2">
                                <input type="text" name="header_logo_icon" value="<?php echo e($settings['header_logo_icon']); ?>"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="gavel">
                                <button type="button" onclick="openIconPicker(v=>document.querySelector('[name=header_logo_icon]').value=v)"
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-primary hover:bg-blue-50">
                                    <span class="material-symbols-outlined text-base">grid_view</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">پیش‌نمایش: <span class="material-symbols-outlined text-sm align-middle" id="header-icon-preview"><?php echo e($settings['header_logo_icon']); ?></span></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">سایز لوگو (px)</label>
                            <input type="number" name="header_logo_size" value="<?php echo e($settings['header_logo_size'] ?? 40); ?>" min="20" max="120"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                    </div>
                </div>

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">palette</span>
                        رنگ و استایل
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">رنگ پس‌زمینه</label>
                            <input type="color" name="header_bg" value="<?php echo e($settings['header_bg']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['header_bg']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=header_bg]').value=this.value">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">رنگ متن</label>
                            <input type="color" name="header_text_color" value="<?php echo e($settings['header_text_color']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['header_text_color']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=header_text_color]').value=this.value">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">ارتفاع هدر (px)</label>
                            <input type="number" name="header_height" value="<?php echo e($settings['header_height']); ?>" min="50" max="120"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">هدر چسبنده</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="header_sticky" value="1" <?php echo e($settings['header_sticky'] ? 'checked' : ''); ?> class="accent-primary w-4 h-4">
                                <span class="text-sm text-gray-600">فعال</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">نمایش جستجو</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="header_show_search" value="1" <?php echo e($settings['header_show_search'] ? 'checked' : ''); ?> class="accent-primary w-4 h-4">
                                <span class="text-sm text-gray-600">فعال</span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">منوی دسته‌بندی</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="header_show_cats" value="1" <?php echo e($settings['header_show_cats'] ? 'checked' : ''); ?> class="accent-primary w-4 h-4">
                                <span class="text-sm text-gray-600">فعال</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">menu</span>
                    لینک‌های منوی هدر (اختیاری)
                </h3>
                <div id="nav-links-container" class="space-y-2 mb-3">
                    <?php $__currentLoopData = $settings['header_nav_links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="nav-link-row grid gap-2 p-3 bg-gray-50 rounded-lg" style="grid-template-columns:1fr 1fr 120px auto;" data-index="<?php echo e($i); ?>">
                        <div><label class="text-xs text-gray-500 block mb-1">عنوان</label>
                            <input type="text" name="header_nav_links[label][]" value="<?php echo e($link['label']); ?>" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
                        <div><label class="text-xs text-gray-500 block mb-1">لینک</label>
                            <input type="text" name="header_nav_links[url][]" value="<?php echo e($link['url']); ?>" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="/listings"></div>
                        <div><label class="text-xs text-gray-500 block mb-1">آیکون</label>
                            <input type="text" name="header_nav_links[icon][]" value="<?php echo e($link['icon'] ?? ''); ?>" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="home"></div>
                        <div class="flex items-end pb-0.5">
                            <button type="button" onclick="this.closest('.nav-link-row').remove()"
                                    class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500 hover:bg-red-100">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="button" onclick="addNavLink()"
                        class="w-full py-2 border border-dashed border-primary rounded-lg text-primary text-sm flex items-center justify-center gap-2 hover:bg-blue-50">
                    <span class="material-symbols-outlined text-base">add</span> افزودن لینک
                </button>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">preview</span>
                    پیش‌نمایش هدر
                </h3>
                <div id="header-preview" class="rounded-xl overflow-hidden border border-gray-200">
                    <div class="flex items-center justify-between px-6 gap-4" id="preview-header-bar"
                         style="background:<?php echo e($settings['header_bg']); ?>; height:<?php echo e($settings['header_height']); ?>px; color:<?php echo e($settings['header_text_color']); ?>;">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-xl" id="preview-logo-icon"><?php echo e($settings['header_logo_icon']); ?></span>
                            </div>
                            <span class="font-black text-lg" id="preview-logo-text"><?php echo e($settings['header_logo_text']); ?></span>
                        </div>
                        <div class="flex-1 max-w-sm mx-4">
                            <?php if($settings['header_show_search']): ?>
                            <div class="bg-gray-100 rounded-lg h-9 flex items-center px-3 gap-2 text-gray-400 text-sm">
                                <span class="material-symbols-outlined text-base">search</span>
                                جستجو...
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined">notifications</span>
                            <span class="material-symbols-outlined">account_circle</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="tab==='footer'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">info</span>
                        اطلاعات فوتر
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تصویر لوگو فوتر</label>
                            <?php if($settings['footer_logo']): ?>
                                <img src="<?php echo e(url('storage/'.$settings['footer_logo'])); ?>" class="h-8 mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" name="footer_logo_file" accept="image/*" class="text-sm text-gray-600 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 file:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">متن لوگو فوتر</label>
                            <input type="text" name="footer_logo_text" value="<?php echo e($settings['footer_logo_text']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">سایز لوگو فوتر (px)</label>
                            <input type="number" name="footer_logo_size" value="<?php echo e($settings['footer_logo_size'] ?? 32); ?>" min="16" max="80"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات</label>
                            <textarea name="footer_description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary resize-none"><?php echo e($settings['footer_description']); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">متن کپی‌رایت</label>
                            <input type="text" name="footer_copyright" value="<?php echo e($settings['footer_copyright']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">نمایش فوتر</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="footer_show" value="1" <?php echo e($settings['footer_show'] ? 'checked' : ''); ?> class="accent-primary w-4 h-4">
                                <span class="text-sm text-gray-600">فعال</span>
                            </label>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">palette</span>
                        رنگ فوتر
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">رنگ پس‌زمینه</label>
                            <input type="color" name="footer_bg" value="<?php echo e($settings['footer_bg']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['footer_bg']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=footer_bg]').value=this.value">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-32">رنگ متن</label>
                            <input type="color" name="footer_text_color" value="<?php echo e($settings['footer_text_color']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['footer_text_color']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=footer_text_color]').value=this.value">
                        </div>
                    </div>

                    
                    <h4 class="font-semibold text-gray-700 mt-5 mb-3">لینک‌های شبکه اجتماعی</h4>
                    <div id="social-links-container" class="space-y-2 mb-3">
                        <?php $__currentLoopData = $settings['footer_social']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="social-row grid gap-2 p-2 bg-gray-50 rounded-lg" style="grid-template-columns:120px 1fr auto;" data-index="<?php echo e($i); ?>">
                            <div>
                                <input type="text" name="footer_social[icon][]" value="<?php echo e($social['icon']); ?>" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="آیکون">
                            </div>
                            <div>
                                <input type="text" name="footer_social[url][]" value="<?php echo e($social['url']); ?>" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="https://...">
                            </div>
                            <button type="button" onclick="this.closest('.social-row').remove()"
                                    class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button type="button" onclick="addSocialLink()"
                            class="w-full py-2 border border-dashed border-primary rounded-lg text-primary text-sm flex items-center justify-center gap-2 hover:bg-blue-50">
                        <span class="material-symbols-outlined text-base">add</span> افزودن شبکه اجتماعی
                    </button>
                </div>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">view_column</span>
                    ستون‌های فوتر
                </h3>
                <div id="footer-cols-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-3">
                    <?php
                        $defaultCols = $settings['footer_columns'] ?: [
                            ['title'=>'دسترسی سریع','links'=>[['label'=>'خانه','url'=>'/'],['label'=>'مزایده‌ها','url'=>'/listings']]],
                            ['title'=>'راهنمای مشتریان','links'=>[['label'=>'قوانین','url'=>'#'],['label'=>'پرسش‌های متداول','url'=>'#']]],
                        ];
                    ?>
                    <?php $__currentLoopData = $defaultCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ci => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="footer-col-card border border-gray-200 rounded-lg p-3" data-col="<?php echo e($ci); ?>">
                        <div class="flex items-center justify-between mb-2">
                            <input type="text" name="footer_columns[<?php echo e($ci); ?>][title]" value="<?php echo e($col['title']); ?>"
                                   class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm font-semibold" placeholder="عنوان ستون">
                            <button type="button" onclick="this.closest('.footer-col-card').remove()"
                                    class="mr-2 p-1 text-red-400 hover:text-red-600">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                        <div class="col-links space-y-1">
                            <?php $__currentLoopData = $col['links'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $li => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-link-row flex gap-1" data-link="<?php echo e($li); ?>">
                                <input type="text" name="footer_columns[<?php echo e($ci); ?>][links][<?php echo e($li); ?>][label]" value="<?php echo e($link['label']); ?>"
                                       class="flex-1 px-2 py-1 border border-gray-200 rounded text-xs" placeholder="عنوان">
                                <input type="text" name="footer_columns[<?php echo e($ci); ?>][links][<?php echo e($li); ?>][url]" value="<?php echo e($link['url']); ?>"
                                       class="flex-1 px-2 py-1 border border-gray-200 rounded text-xs" placeholder="/url">
                                <button type="button" onclick="this.closest('.col-link-row').remove()"
                                        class="text-red-400 hover:text-red-600 px-1">
                                    <span class="material-symbols-outlined text-xs">close</span>
                                </button>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <button type="button" onclick="addColLink(this, <?php echo e($ci); ?>)"
                                class="mt-2 w-full py-1 border border-dashed border-gray-300 rounded text-xs text-gray-500 hover:border-primary hover:text-primary">
                            + افزودن لینک
                        </button>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="button" onclick="addFooterCol()"
                        class="py-2 px-4 border border-dashed border-primary rounded-lg text-primary text-sm flex items-center gap-2 hover:bg-blue-50">
                    <span class="material-symbols-outlined text-base">add</span> افزودن ستون
                </button>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">link</span>
                    لینک‌های پایین فوتر
                </h3>
                <div id="bottom-links-container" class="space-y-2 mb-3">
                    <?php
                        $bottomLinksRaw = $settings['footer_bottom_links'] ?? '[]';
                        $bottomLinks = is_array($bottomLinksRaw) ? $bottomLinksRaw : (json_decode($bottomLinksRaw, true) ?? []);
                        if (empty($bottomLinks)) {
                            $bottomLinks = [
                                ['label' => $settings['footer_privacy_text'] ?? 'حریم خصوصی', 'url' => $settings['footer_privacy_url'] ?? '#'],
                                ['label' => $settings['footer_terms_text'] ?? 'شرایط استفاده', 'url' => $settings['footer_terms_url'] ?? '#'],
                            ];
                        }
                    ?>
                    <?php $__currentLoopData = $bottomLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $bl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bottom-link-row grid gap-2 p-2 bg-gray-50 rounded-lg" style="grid-template-columns:1fr 1fr auto;" data-index="<?php echo e($i); ?>">
                        <input type="text" name="footer_bottom_links[label][]" value="<?php echo e($bl['label']); ?>" class="px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="متن لینک">
                        <input type="text" name="footer_bottom_links[url][]" value="<?php echo e($bl['url']); ?>" class="px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="/url">
                        <button type="button" onclick="this.closest('.bottom-link-row').remove()"
                                class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="button" onclick="addBottomLink()"
                        class="w-full py-2 border border-dashed border-primary rounded-lg text-primary text-sm flex items-center justify-center gap-2 hover:bg-blue-50">
                    <span class="material-symbols-outlined text-base">add</span> افزودن لینک
                </button>
                <p class="text-xs text-gray-400 mt-2">این لینک‌ها در پایین فوتر نمایش داده می‌شوند.</p>
            </div>

            
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-lg">verified</span>
                    نماد اعتماد
                </h3>
                <p class="text-xs text-gray-500 mb-3">می‌توانید کد HTML نماد اعتماد را وارد کنید یا یک تصویر آپلود کنید. تصویر به صورت خودکار responsive می‌شود.</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">کد HTML نماد (اختیاری)</label>
                        <textarea name="footer_trust_html" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary font-mono resize-none"
                                  placeholder="<a href='...'><img src='...' /></a>"><?php echo e($settings['footer_trust_html'] ?? ''); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">یا آپلود تصویر نماد</label>
                        <?php if(!empty($settings['footer_trust_image'])): ?>
                            <img src="<?php echo e(url('storage/'.$settings['footer_trust_image'])); ?>" class="h-16 mb-2 rounded border border-gray-200">
                        <?php endif; ?>
                        <input type="file" name="footer_trust_image_file" accept="image/*"
                               class="text-sm text-gray-600 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 file:text-sm">
                        <?php if(!empty($settings['footer_trust_image'])): ?>
                            <input type="hidden" name="footer_trust_image" value="<?php echo e($settings['footer_trust_image']); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div x-show="tab==='dashboard'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">image</span>
                        لوگوی داشبورد
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تصویر لوگو</label>
                            <?php if($settings['dashboard_logo']): ?>
                                <img src="<?php echo e(url('storage/'.$settings['dashboard_logo'])); ?>" class="h-10 mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" name="dashboard_logo_file" accept="image/*" class="text-sm text-gray-600 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 file:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">متن لوگو</label>
                            <input type="text" name="dashboard_logo_text" value="<?php echo e($settings['dashboard_logo_text']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">آیکون لوگو</label>
                            <div class="flex gap-2">
                                <input type="text" name="dashboard_logo_icon" value="<?php echo e($settings['dashboard_logo_icon']); ?>"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary" placeholder="storefront">
                                <button type="button" onclick="openIconPicker(v=>document.querySelector('[name=dashboard_logo_icon]').value=v)"
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-primary hover:bg-blue-50">
                                    <span class="material-symbols-outlined text-base">grid_view</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white border border-gray-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">palette</span>
                        رنگ‌های داشبورد
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-36">رنگ پس‌زمینه sidebar</label>
                            <input type="color" name="dashboard_sidebar_bg" value="<?php echo e($settings['dashboard_sidebar_bg']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['dashboard_sidebar_bg']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=dashboard_sidebar_bg]').value=this.value">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-gray-700 w-36">رنگ اصلی (primary)</label>
                            <input type="color" name="dashboard_primary" value="<?php echo e($settings['dashboard_primary']); ?>"
                                   class="w-10 h-9 rounded border border-gray-300 cursor-pointer">
                            <input type="text" value="<?php echo e($settings['dashboard_primary']); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   oninput="document.querySelector('[name=dashboard_primary]').value=this.value">
                        </div>
                    </div>

                    
                    <div class="mt-5 border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex" style="height:120px;">
                            <div class="w-40 flex flex-col p-3 gap-1" id="preview-sidebar"
                                 style="background:<?php echo e($settings['dashboard_sidebar_bg']); ?>; border-left:1px solid #e5e7eb;">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background:<?php echo e($settings['dashboard_primary']); ?>20;">
                                        <span class="material-symbols-outlined text-sm" style="color:<?php echo e($settings['dashboard_primary']); ?>;"><?php echo e($settings['dashboard_logo_icon']); ?></span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800"><?php echo e($settings['dashboard_logo_text']); ?></span>
                                </div>
                                <?php $__currentLoopData = ['داشبورد','مزایده‌ها','کیف پول']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs text-gray-600">
                                    <span class="material-symbols-outlined text-xs">chevron_left</span>
                                    <?php echo e($item); ?>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="flex-1 bg-gray-50 flex items-center justify-center text-xs text-gray-400">
                                محتوای صفحه
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
            <button type="submit"
                    class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-600 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-base">save</span>
                ذخیره تنظیمات
            </button>
        </div>
    </form>
</div>


<div id="theme-icon-modal" style="position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;padding:16px;" dir="rtl">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.6);" onclick="closeThemeIconPicker()"></div>
    <div style="position:relative;width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;background:white;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,.25);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;flex-shrink:0;">
            <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">انتخاب آیکون</h3>
            <button onclick="closeThemeIconPicker()" style="background:none;border:none;cursor:pointer;color:#9ca3af;">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div style="padding:12px 20px;border-bottom:1px solid #f1f5f9;flex-shrink:0;">
            <input id="theme-icon-search" type="text" placeholder="جستجوی آیکون..." oninput="filterThemeIcons(this.value)"
                   style="width:100%;padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
            <div id="theme-icon-toast" style="display:none;margin-top:8px;padding:6px 12px;background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:6px;font-size:12px;"></div>
        </div>
        <div id="theme-icon-grid" style="overflow-y:auto;padding:16px 20px;flex:1;display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;"></div>
        <div style="padding:10px 20px;border-top:1px solid #f1f5f9;flex-shrink:0;display:flex;justify-content:space-between;align-items:center;">
            <span id="theme-icon-count" style="font-size:12px;color:#6b7280;"></span>
            <span style="font-size:11px;color:#9ca3af;">روی آیکون کلیک کنید تا انتخاب شود</span>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// ===== Icon Picker =====
const THEME_ICONS = [
    'home','search','settings','favorite','star','delete','edit','add','remove','close','check',
    'menu','more_vert','refresh','share','download','upload','save','copy',
    'visibility','lock','person','people','group','account_circle','face','badge',
    'email','phone','message','chat','notifications','alarm','schedule','calendar_today','event',
    'location_on','map','navigation','place','explore','public','language',
    'shopping_cart','store','storefront','inventory','local_shipping','delivery_dining',
    'payment','credit_card','wallet','account_balance','attach_money','money','receipt',
    'gavel','auction','sell','local_offer','discount','percent',
    'image','photo','camera','videocam','mic','volume_up','music_note','tv','monitor',
    'computer','laptop','phone_android','tablet','watch','keyboard',
    'wifi','bluetooth','battery_full','power',
    'folder','file_copy','description','article','book','note',
    'dashboard','widgets','grid_view','list','bar_chart','pie_chart',
    'trending_up','analytics','insights','assessment',
    'check_circle','cancel','error','warning','info','help','flag','bookmark',
    'thumb_up','grade','star_border','emoji_events',
    'verified','verified_user','security','shield','admin_panel_settings',
    'build','construction','engineering','handyman',
    'restaurant','local_cafe','fastfood',
    'flight','hotel','car_rental','directions_car','train',
    'school','science','calculate','code','terminal',
    'palette','brush','design_services',
    'sunny','cloud','water_drop',
    'pets','nature','park','eco','recycling',
    'sports_soccer','sports_basketball','sports_esports',
    'celebration','cake','card_giftcard','volunteer_activism','handshake',
    'open_in_new','link','qr_code','fingerprint',
    'tune','filter_list','sort','sync',
    'zoom_in','zoom_out','fullscreen','crop','rotate_right',
    'format_bold','format_italic','title','text_fields',
    'attach_file','cloud_upload','cloud_download','backup',
    'history','undo','redo','play_arrow','pause','stop',
    'brightness_high','dark_mode',
    'add_circle','remove_circle',
    'expand_more','expand_less','chevron_right','chevron_left',
    'arrow_upward','arrow_downward','arrow_back','arrow_forward',
    'drag_indicator','touch_app',
    'support_agent','headset_mic','forum','comment',
    'send','reply','inbox','drafts',
    'category','layers',
    'tag','new_releases','update',
    'done','done_all','task_alt',
    'block','do_not_disturb',
    'pending','hourglass_empty','timer',
    'bolt','flash_on','power_settings_new',
    'key','vpn_key','password',
    'storefront','business','corporate_fare','domain','apartment','house',
    'local_atm','point_of_sale','money_off',
    'workspace_premium','diamond','auto_awesome','stars',
    'rocket_launch','travel_explore','globe_asia',
    'format_quote','chat_bubble','sms',
];

let themeIconCallback = null;

function openIconPicker(callback) {
    themeIconCallback = callback;
    const modal = document.getElementById('theme-icon-modal');
    if (!modal) return;
    modal.style.display = 'flex';
    document.getElementById('theme-icon-search').value = '';
    renderThemeIcons(THEME_ICONS);
    setTimeout(() => document.getElementById('theme-icon-search').focus(), 100);
}

function closeThemeIconPicker() {
    document.getElementById('theme-icon-modal').style.display = 'none';
    themeIconCallback = null;
}

function renderThemeIcons(icons) {
    const grid = document.getElementById('theme-icon-grid');
    document.getElementById('theme-icon-count').textContent = icons.length + ' آیکون';
    grid.innerHTML = icons.map(icon => `
        <div onclick="selectThemeIcon('${icon}')" title="${icon}"
             style="display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px;border-radius:8px;border:1px solid #f3f4f6;cursor:pointer;transition:all .15s;background:white;"
             onmouseover="this.style.borderColor='#3b82f6';this.style.background='#eff6ff'"
             onmouseout="this.style.borderColor='#f3f4f6';this.style.background='white'">
            <span class="material-symbols-outlined" style="font-size:24px;color:#374151;">${icon}</span>
            <span style="font-size:9px;color:#6b7280;text-align:center;word-break:break-all;line-height:1.2;">${icon}</span>
        </div>`).join('');
}

function filterThemeIcons(query) {
    const q = query.toLowerCase().trim();
    renderThemeIcons(q ? THEME_ICONS.filter(i => i.includes(q)) : THEME_ICONS);
}

function selectThemeIcon(icon) {
    if (themeIconCallback) {
        themeIconCallback(icon);
        closeThemeIconPicker();
    }
    const toast = document.getElementById('theme-icon-toast');
    if (toast) {
        toast.textContent = '✓ کپی شد: ' + icon;
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 2000);
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeThemeIconPicker();
});

// Nav links
function addNavLink() {
    const c = document.getElementById('nav-links-container');
    const i = c.querySelectorAll('.nav-link-row').length;
    const div = document.createElement('div');
    div.className = 'nav-link-row grid gap-2 p-3 bg-gray-50 rounded-lg';
    div.style.gridTemplateColumns = '1fr 1fr 120px auto';
    div.setAttribute('data-index', i);
    div.innerHTML = `
        <div><label class="text-xs text-gray-500 block mb-1">عنوان</label>
            <input type="text" name="header_nav_links[label][]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
        <div><label class="text-xs text-gray-500 block mb-1">لینک</label>
            <input type="text" name="header_nav_links[url][]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="/listings"></div>
        <div><label class="text-xs text-gray-500 block mb-1">آیکون</label>
            <input type="text" name="header_nav_links[icon][]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="home"></div>
        <div class="flex items-end pb-0.5">
            <button type="button" onclick="this.closest('.nav-link-row').remove()"
                    class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500">
                <span class="material-symbols-outlined text-sm">delete</span>
            </button>
        </div>`;
    c.appendChild(div);
}

// Bottom links
function addBottomLink() {
    const c = document.getElementById('bottom-links-container');
    const div = document.createElement('div');
    div.className = 'bottom-link-row grid gap-2 p-2 bg-gray-50 rounded-lg';
    div.style.gridTemplateColumns = '1fr 1fr auto';
    div.innerHTML = `
        <input type="text" name="footer_bottom_links[label][]" class="px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="متن لینک">
        <input type="text" name="footer_bottom_links[url][]" class="px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="/url">
        <button type="button" onclick="this.closest('.bottom-link-row').remove()"
                class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500">
            <span class="material-symbols-outlined text-sm">delete</span>
        </button>`;
    c.appendChild(div);
}

// Social links
function addSocialLink() {
    const c = document.getElementById('social-links-container');
    const div = document.createElement('div');
    div.className = 'social-row grid gap-2 p-2 bg-gray-50 rounded-lg';
    div.style.gridTemplateColumns = '120px 1fr auto';
    div.innerHTML = `
        <input type="text" name="footer_social[icon][]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="آیکون">
        <input type="text" name="footer_social[url][]" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm" placeholder="https://...">
        <button type="button" onclick="this.closest('.social-row').remove()"
                class="p-1.5 border border-red-200 rounded bg-red-50 text-red-500">
            <span class="material-symbols-outlined text-sm">delete</span>
        </button>`;
    c.appendChild(div);
}

// Footer columns
let footerColCount = document.querySelectorAll('.footer-col-card').length;
function addFooterCol() {
    const c = document.getElementById('footer-cols-container');
    const ci = footerColCount++;
    const div = document.createElement('div');
    div.className = 'footer-col-card border border-gray-200 rounded-lg p-3';
    div.setAttribute('data-col', ci);
    div.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <input type="text" name="footer_columns[${ci}][title]" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm font-semibold" placeholder="عنوان ستون">
            <button type="button" onclick="this.closest('.footer-col-card').remove()" class="mr-2 p-1 text-red-400 hover:text-red-600">
                <span class="material-symbols-outlined text-sm">delete</span>
            </button>
        </div>
        <div class="col-links space-y-1"></div>
        <button type="button" onclick="addColLink(this, ${ci})"
                class="mt-2 w-full py-1 border border-dashed border-gray-300 rounded text-xs text-gray-500 hover:border-primary hover:text-primary">
            + افزودن لینک
        </button>`;
    c.appendChild(div);
}

function addColLink(btn, ci) {
    const container = btn.previousElementSibling;
    const li = container.querySelectorAll('.col-link-row').length;
    const div = document.createElement('div');
    div.className = 'col-link-row flex gap-1';
    div.innerHTML = `
        <input type="text" name="footer_columns[${ci}][links][${li}][label]" class="flex-1 px-2 py-1 border border-gray-200 rounded text-xs" placeholder="عنوان">
        <input type="text" name="footer_columns[${ci}][links][${li}][url]" class="flex-1 px-2 py-1 border border-gray-200 rounded text-xs" placeholder="/url">
        <button type="button" onclick="this.closest('.col-link-row').remove()" class="text-red-400 px-1">
            <span class="material-symbols-outlined text-xs">close</span>
        </button>`;
    container.appendChild(div);
}

// Live preview
document.querySelector('[name=header_bg]')?.addEventListener('input', e => {
    const bar = document.getElementById('preview-header-bar');
    if (bar) bar.style.background = e.target.value;
});
document.querySelector('[name=header_text_color]')?.addEventListener('input', e => {
    const bar = document.getElementById('preview-header-bar');
    if (bar) bar.style.color = e.target.value;
});
document.querySelector('[name=header_logo_text]')?.addEventListener('input', e => {
    const el = document.getElementById('preview-logo-text');
    if (el) el.textContent = e.target.value;
});
document.querySelector('[name=header_logo_icon]')?.addEventListener('input', e => {
    const el = document.getElementById('preview-logo-icon');
    if (el) el.textContent = e.target.value;
    const prev = document.getElementById('header-icon-preview');
    if (prev) prev.textContent = e.target.value;
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/theme/index.blade.php ENDPATH**/ ?>