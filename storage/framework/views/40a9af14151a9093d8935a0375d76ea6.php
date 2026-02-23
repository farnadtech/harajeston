<div class="category-megamenu relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
        <span class="material-symbols-outlined text-[20px]">apps</span>
        <span class="font-medium">دسته‌بندی‌ها</span>
        <span class="material-symbols-outlined text-[18px]">expand_more</span>
    </button>
    
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute top-full right-0 mt-2 w-[1000px] bg-white rounded-xl shadow-2xl border border-gray-100 z-50"
         x-data="{ activeParent: null }">
        <div class="grid grid-cols-12 max-h-[600px]">
            
            <div class="col-span-3 border-l border-gray-100 overflow-y-auto">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button @mouseenter="activeParent = <?php echo e($parent->id); ?>"
                        @click="window.location.href='<?php echo e(route('listings.index', ['category' => $parent->slug])); ?>'"
                        :class="activeParent === <?php echo e($parent->id); ?> ? 'bg-primary/5 text-primary border-r-2 border-primary' : 'text-gray-700 hover:bg-gray-50'"
                        class="w-full flex items-center gap-3 p-4 transition-colors text-right">
                    <span class="material-symbols-outlined text-[22px]"><?php echo e($parent->icon ?? 'category'); ?></span>
                    <span class="text-sm font-medium"><?php echo e($parent->name); ?></span>
                    <?php if($parent->children && count($parent->children) > 0): ?>
                        <span class="material-symbols-outlined text-sm mr-auto">chevron_left</span>
                    <?php endif; ?>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            
            <div class="col-span-9 p-6 overflow-y-auto">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div x-show="activeParent === <?php echo e($parent->id); ?>" x-transition>
                    <?php if($parent->children && count($parent->children) > 0): ?>
                        <div class="grid grid-cols-3 gap-6">
                            <?php $__currentLoopData = $parent->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <a href="<?php echo e(route('listings.index', ['category' => $child->slug])); ?>" 
                                   class="flex items-center gap-2 font-bold text-gray-800 hover:text-primary transition-colors mb-3 group">
                                    <?php if($child->icon): ?>
                                        <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform"><?php echo e($child->icon); ?></span>
                                    <?php endif; ?>
                                    <span class="text-sm"><?php echo e($child->name); ?></span>
                                </a>
                                
                                <?php if($child->children && count($child->children) > 0): ?>
                                <ul class="space-y-2">
                                    <?php $__currentLoopData = $child->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('listings.index', ['category' => $grandchild->slug])); ?>"
                                           class="block text-xs text-gray-600 hover:text-primary hover:translate-x-1 transition-all py-1">
                                            <?php echo e($grandchild->name); ?>

                                        </a>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-symbols-outlined text-4xl mb-2">category</span>
                            <p class="text-sm">این دسته زیرمجموعه ندارد</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/components/category-megamenu.blade.php ENDPATH**/ ?>