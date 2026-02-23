

<?php $__env->startSection('title', 'اعلان‌ها'); ?>
<?php $__env->startSection('header-title', 'مدیریت اعلان‌ها'); ?>
<?php $__env->startSection('header-subtitle', 'مشاهده و مدیریت تمام اعلان‌های سیستم'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">همه اعلان‌ها</h2>
            <?php if($notifications->where('is_read', false)->count() > 0): ?>
                <button onclick="markAllAsRead()" class="text-sm text-primary hover:text-blue-700 font-medium">
                    علامت‌گذاری همه به عنوان خوانده شده
                </button>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($notification->link ? route('admin.notifications.read', $notification->id) : '#'); ?>" 
                   class="block px-6 py-4 hover:bg-gray-50 transition-colors <?php echo e(!$notification->is_read ? 'bg-blue-50/50' : ''); ?>">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-<?php echo e($notification->color); ?>-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-<?php echo e($notification->color); ?>-600 text-2xl"><?php echo e($notification->icon); ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <p class="text-base font-medium text-gray-900"><?php echo e($notification->title); ?></p>
                                <?php if(!$notification->is_read): ?>
                                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-600 mb-2"><?php echo e($notification->message); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($notification->time_ago); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-6xl mb-3 block">notifications_off</span>
                    <p class="text-gray-500">اعلانی وجود ندارد</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($notifications->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($notifications->links('vendor.pagination.custom')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function markAllAsRead() {
    fetch('<?php echo e(route('admin.notifications.mark-all-read')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('همه اعلان‌ها به عنوان خوانده شده علامت‌گذاری شدند', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('خطا در علامت‌گذاری اعلان‌ها', 'error');
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>