

<?php $__env->startSection('title', 'مدیریت نظرات فروشندگان'); ?>
<?php $__env->startSection('header-title', 'مدیریت نظرات فروشندگان'); ?>
<?php $__env->startSection('header-subtitle', 'تایید، رد یا حذف نظرات خریداران درباره فروشندگان'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                <option value="">همه وضعیت‌ها</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>در انتظار تایید (<?php echo app(\App\Services\PersianNumberService::class)->toPersian($pendingCount); ?>)</option>
                <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>تایید شده</option>
                <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>رد شده</option>
            </select>
            
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors">
                اعمال فیلتر
            </button>
            
            <?php if(request()->has('status')): ?>
                <a href="<?php echo e(route('admin.seller-reviews.index')); ?>" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    حذف فیلترها
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-6 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-gray-400">person</span>
                            <span class="font-bold text-gray-900"><?php echo e($review->buyer->name); ?></span>
                            <span class="text-xs text-gray-500">خریدار</span>
                            <span class="material-symbols-outlined text-gray-300 text-sm">arrow_left</span>
                            <span class="font-bold text-primary"><?php echo e($review->seller->name); ?></span>
                            <span class="text-xs text-gray-500">فروشنده</span>
                            
                            <div class="flex items-center gap-1 mr-2">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <span class="text-<?php echo e($i <= $review->rating ? 'yellow-400' : 'gray-300'); ?> text-sm">★</span>
                                <?php endfor; ?>
                                <span class="text-xs text-gray-600 mr-1">(<?php echo e($review->rating); ?>)</span>
                            </div>
                            
                            <span class="text-xs text-gray-500"><?php echo e($review->created_at->diffForHumans()); ?></span>
                            
                            <?php if($review->status === 'pending'): ?>
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-medium">در انتظار تایید</span>
                            <?php elseif($review->status === 'approved'): ?>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">تایید شده</span>
                            <?php else: ?>
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-medium">رد شده</span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo e(route('admin.orders.show', $review->order_id)); ?>" class="text-sm text-primary hover:underline mb-2 block">
                            سفارش #<?php echo e($review->order_id); ?>

                        </a>
                        
                        <p class="text-gray-700 leading-relaxed"><?php echo e($review->comment); ?></p>
                    </div>
                    
                    <div class="flex gap-2">
                        <?php if($review->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('admin.seller-reviews.approve', $review->id)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="تایید">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </button>
                            </form>
                            
                            <form method="POST" action="<?php echo e(route('admin.seller-reviews.reject', $review->id)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="رد">
                                    <span class="material-symbols-outlined">cancel</span>
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo e(route('admin.seller-reviews.destroy', $review->id)); ?>" class="inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="حذف">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-gray-300 text-6xl mb-4">rate_review</span>
                <p class="text-gray-500">نظری یافت نشد</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($reviews->hasPages()): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-sm">
            <?php echo e($reviews->links('vendor.pagination.custom')); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\seller-reviews\index.blade.php ENDPATH**/ ?>