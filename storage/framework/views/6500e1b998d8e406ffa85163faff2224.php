

<?php $__env->startSection('title', 'تست داشبورد'); ?>

<?php $__env->startSection('page-title', 'داشبورد فروشنده'); ?>
<?php $__env->startSection('page-subtitle', 'نام: ' . auth()->user()->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">آمار فروشگاه</h2>
        <div class="space-y-3">
            <p><strong>نام:</strong> <?php echo e(auth()->user()->name); ?></p>
            <p><strong>مزایده‌های فعال:</strong> <?php echo e($stats['active_auctions'] ?? 0); ?></p>
            <p><strong>در انتظار تایید:</strong> <?php echo e($stats['pending_listings'] ?? 0); ?></p>
            <p><strong>تکمیل شده:</strong> <?php echo e($stats['completed_auctions'] ?? 0); ?></p>
            <p><strong>درآمد کل:</strong> <?php echo e(number_format($stats['total_sales'] ?? 0)); ?> تومان</p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\dashboard\seller-simple.blade.php ENDPATH**/ ?>