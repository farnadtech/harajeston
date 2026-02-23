<?php if($paginator->hasPages()): ?>
    <nav aria-label="Pagination" class="flex items-center gap-2">
        
        <?php if($paginator->onFirstPage()): ?>
            <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined rtl:rotate-180">chevron_right</span>
            </span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-primary transition-colors">
                <span class="material-symbols-outlined rtl:rotate-180">chevron_right</span>
            </a>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(is_string($element)): ?>
                <span class="w-10 h-10 flex items-center justify-center text-gray-400">...</span>
            <?php endif; ?>

            
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold shadow-md shadow-primary/20"><?php echo e($page); ?></span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors font-medium"><?php echo e($page); ?></a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-primary transition-colors">
                <span class="material-symbols-outlined rtl:rotate-180">chevron_left</span>
            </a>
        <?php else: ?>
            <span class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                <span class="material-symbols-outlined rtl:rotate-180">chevron_left</span>
            </span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>