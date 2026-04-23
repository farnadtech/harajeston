
<?php $__env->startSection('title', 'همه دسته‌بندی‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">

    
    <div class="relative overflow-hidden rounded-3xl mb-12 mt-4"
         style="background: linear-gradient(135deg, #1e40af 0%, #7c3aed 50%, #db2777 100%); min-height: 220px;">
        
        <div style="position:absolute; top:-40px; right:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.06);"></div>
        <div style="position:absolute; bottom:-60px; left:100px; width:280px; height:280px; border-radius:50%; background:rgba(255,255,255,.04);"></div>
        <div style="position:absolute; top:20px; left:40%; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,.05);"></div>

        <div style="position:relative; z-index:10; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:4rem 2rem; min-height:220px;">
            <h1 style="font-size:clamp(2rem,5vw,3rem); font-weight:900; color:white; margin:0 0 1rem; line-height:1.2;">همه دسته‌بندی‌ها</h1>
            <p style="color:rgba(255,255,255,.75); font-size:1.1rem; max-width:480px; line-height:1.7; margin:0;">
                مزایده‌های خود را بر اساس دسته‌بندی پیدا کنید
            </p>
        </div>
    </div>

    
    <div class="space-y-8 pb-12">
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $palettes = [
                ['from'=>'#eff6ff','to'=>'#dbeafe','icon'=>'#3b82f6','border'=>'#93c5fd'],
                ['from'=>'#fdf4ff','to'=>'#f3e8ff','icon'=>'#a855f7','border'=>'#d8b4fe'],
                ['from'=>'#fff7ed','to'=>'#ffedd5','icon'=>'#f97316','border'=>'#fdba74'],
                ['from'=>'#f0fdf4','to'=>'#dcfce7','icon'=>'#22c55e','border'=>'#86efac'],
                ['from'=>'#fef2f2','to'=>'#fee2e2','icon'=>'#ef4444','border'=>'#fca5a5'],
                ['from'=>'#fffbeb','to'=>'#fef3c7','icon'=>'#f59e0b','border'=>'#fcd34d'],
                ['from'=>'#f0f9ff','to'=>'#e0f2fe','icon'=>'#0ea5e9','border'=>'#7dd3fc'],
                ['from'=>'#fdf2f8','to'=>'#fce7f3','icon'=>'#ec4899','border'=>'#f9a8d4'],
            ];
            $p = $palettes[$loop->index % count($palettes)];
        ?>

        <div class="bg-white rounded-2xl border overflow-hidden shadow-sm hover:shadow-md transition-shadow"
             style="border-color: <?php echo e($p['border']); ?>40;">

            
            <a href="<?php echo e(route('listings.index', ['category' => $cat->slug])); ?>"
               class="flex items-center gap-5 p-6 group transition-all"
               style="background: linear-gradient(135deg, <?php echo e($p['from']); ?> 0%, <?php echo e($p['to']); ?> 100%); border-bottom: 2px solid <?php echo e($p['border']); ?>;">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md transition-transform group-hover:scale-110 group-hover:rotate-3"
                     style="background: linear-gradient(135deg, <?php echo e($p['icon']); ?>25, <?php echo e($p['icon']); ?>10); margin-left: 1rem;">
                    <span class="material-symbols-outlined text-3xl" style="color: <?php echo e($p['icon']); ?>">
                        <?php echo e($cat->icon ?? 'category'); ?>

                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-black text-gray-900 group-hover:text-primary transition-colors">
                        <?php echo e($cat->name); ?>

                    </h2>
                    <?php if($cat->description): ?>
                        <p class="text-gray-500 text-sm mt-1 line-clamp-1"><?php echo e($cat->description); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; padding:4px 12px; border-radius:999px; background-color: <?php echo e($p['icon']); ?>20; color: <?php echo e($p['icon']); ?>; white-space:nowrap;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><path d="M9.5 16.5 4 11l1.4-1.4 4.1 4.1 9.1-9.1L20 6Z"/></svg>
                            <?php echo e($cat->listings_count ?? 0); ?> آگهی
                        </span>
                        <?php if($cat->children->count() > 0): ?>
                            <span style="display:inline-flex; align-items:center; gap:5px; font-size:12px; color:#6b7280; padding:4px 12px; border-radius:999px; background:rgba(255,255,255,.7); white-space:nowrap;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><path d="M3 3h8v2H5v2h6v2H5v2h6v2H3V3zm10 0h8v2h-6v2h6v2h-6v2h6v2h-8V3z"/></svg>
                                <?php echo e($cat->children->count()); ?> زیردسته
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center transition-all group-hover:translate-x-1"
                     style="background-color: <?php echo e($p['icon']); ?>15;">
                    <span class="material-symbols-outlined rtl:rotate-180" style="color: <?php echo e($p['icon']); ?>">arrow_right_alt</span>
                </div>
            </a>

            
            <?php if($cat->children->isNotEmpty()): ?>
            <div class="p-6 pt-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php $__currentLoopData = $cat->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl border overflow-hidden transition-all hover:shadow-md"
                         style="border-color: <?php echo e($p['border']); ?>60; background: <?php echo e($p['from']); ?>40;">

                        
                        <a href="<?php echo e(route('listings.index', ['category' => $child->slug])); ?>"
                           class="flex items-center gap-3 p-4 group hover:opacity-90 transition-opacity"
                           style="border-bottom: 1px solid <?php echo e($p['border']); ?>60;">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                 style="background-color: <?php echo e($p['icon']); ?>20;">
                                <span class="material-symbols-outlined text-lg" style="color: <?php echo e($p['icon']); ?>">
                                    <?php echo e($child->icon ?? 'folder'); ?>

                                </span>
                            </div>
                            <span class="font-bold text-gray-800 text-sm group-hover:text-primary transition-colors leading-tight flex-1">
                                <?php echo e($child->name); ?>

                            </span>
                            <span class="material-symbols-outlined text-gray-300 group-hover:text-primary transition-colors rtl:rotate-180" style="font-size:16px;">chevron_right</span>
                        </a>

                        
                        <?php if($child->children->isNotEmpty()): ?>
                        <div class="p-3 space-y-1">
                            <?php $__currentLoopData = $child->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('listings.index', ['category' => $grandchild->slug])); ?>"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/80 transition-colors group">
                                <span class="material-symbols-outlined flex-shrink-0" style="font-size:15px; color: <?php echo e($p['icon']); ?>90">
                                    <?php echo e($grandchild->icon ?? 'subdirectory_arrow_right'); ?>

                                </span>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors leading-tight">
                                    <?php echo e($grandchild->name); ?>

                                </span>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-20 text-gray-400">
            <span class="material-symbols-outlined text-6xl mb-4 block">category</span>
            <p class="text-xl font-medium">دسته‌بندی‌ای یافت نشد</p>
        </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\categories\index.blade.php ENDPATH**/ ?>