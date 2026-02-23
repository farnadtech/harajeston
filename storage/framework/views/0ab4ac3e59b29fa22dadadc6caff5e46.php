

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <?php if($user->seller_status === 'pending'): ?>
                <span class="material-symbols-outlined text-6xl text-amber-500 mb-4">schedule</span>
                <h1 class="text-2xl font-black text-gray-900 mb-2">درخواست شما در حال بررسی است</h1>
                <p class="text-gray-600">لطفا منتظر تایید مدیریت باشید</p>
            <?php elseif($user->seller_status === 'active'): ?>
                <span class="material-symbols-outlined text-6xl text-green-500 mb-4">check_circle</span>
                <h1 class="text-2xl font-black text-gray-900 mb-2">حساب فروشندگی شما فعال است</h1>
                <p class="text-gray-600">می‌توانید محصولات خود را اضافه کنید</p>
            <?php elseif($user->seller_status === 'rejected'): ?>
                <span class="material-symbols-outlined text-6xl text-red-500 mb-4">cancel</span>
                <h1 class="text-2xl font-black text-gray-900 mb-2">درخواست شما رد شد</h1>
                <p class="text-gray-600"><?php echo e($user->seller_rejection_reason); ?></p>
            <?php elseif($user->seller_status === 'suspended'): ?>
                <span class="material-symbols-outlined text-6xl text-red-500 mb-4">block</span>
                <h1 class="text-2xl font-black text-gray-900 mb-2">حساب شما تعلیق شده است</h1>
                <p class="text-gray-600"><?php echo e($user->seller_rejection_reason); ?></p>
            <?php endif; ?>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">تاریخ درخواست:</span>
                <span class="font-bold"><?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($user->seller_requested_at)->format('Y/m/d H:i')); ?></span>
            </div>
            
            <?php if($user->seller_approved_at): ?>
            <div class="flex justify-between">
                <span class="text-gray-600">تاریخ تایید:</span>
                <span class="font-bold"><?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($user->seller_approved_at)->format('Y/m/d H:i')); ?></span>
            </div>
            <?php endif; ?>

            <div class="flex justify-between">
                <span class="text-gray-600">وضعیت:</span>
                <span class="font-bold">
                    <?php if($user->seller_status === 'pending'): ?>
                        <span class="text-amber-600">در انتظار تایید</span>
                    <?php elseif($user->seller_status === 'active'): ?>
                        <span class="text-green-600">فعال</span>
                    <?php elseif($user->seller_status === 'rejected'): ?>
                        <span class="text-red-600">رد شده</span>
                    <?php elseif($user->seller_status === 'suspended'): ?>
                        <span class="text-red-600">تعلیق شده</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="<?php echo e(route('dashboard')); ?>" 
               class="inline-block bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-lg transition-colors">
                بازگشت به داشبورد
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/seller-request/status.blade.php ENDPATH**/ ?>