

<?php $__env->startSection('title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('page-title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('header-title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('header-subtitle', 'بررسی و نصب آخرین نسخه اسکریپت'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-red-600">error</span>
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-blue-600">info</span>
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">system_update</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">وضعیت نسخه</h2>
                <p class="text-sm text-gray-500">آخرین بررسی: همین الان</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">نسخه نصب شده</p>
                <p class="text-2xl font-black text-gray-900"><?php echo e($current); ?></p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">آخرین نسخه</p>
                <?php if($error): ?>
                    <p class="text-sm text-red-500"><?php echo e($error); ?></p>
                <?php elseif($latest): ?>
                    <p class="text-2xl font-black <?php echo e($hasUpdate ? 'text-green-600' : 'text-gray-900'); ?>"><?php echo e($latest); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-400">نامشخص</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if($hasUpdate): ?>
            
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-green-600 mt-0.5">new_releases</span>
                    <div>
                        <p class="font-bold text-green-800">نسخه <?php echo e($latest); ?> آماده نصب است</p>
                        <?php if($changelog): ?>
                            <p class="text-sm text-green-700 mt-1"><?php echo e($changelog); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 flex items-start gap-2">
                <span class="material-symbols-outlined text-yellow-600 text-[18px] mt-0.5">warning</span>
                <p class="text-xs text-yellow-800">قبل از آپدیت، از دیتابیس بکاپ بگیرید. سیستم به صورت خودکار از فایل‌های قدیمی بکاپ می‌گیرد.</p>
            </div>

            <form method="POST" action="<?php echo e(route('admin.update.run')); ?>"
                  onsubmit="return confirm('آیا از نصب آپدیت مطمئن هستید؟ این عملیات چند دقیقه طول می‌کشد.')">
                <?php echo csrf_field(); ?>
                <button type="submit"
                        class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span>
                    نصب آپدیت <?php echo e($latest); ?>

                </button>
            </form>

        <?php elseif(!$error): ?>
            
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <span class="material-symbols-outlined text-green-500 text-3xl">verified</span>
                <div>
                    <p class="font-bold text-gray-800">سیستم به‌روز است</p>
                    <p class="text-sm text-gray-500">شما از آخرین نسخه استفاده می‌کنید</p>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="mt-4 text-center">
            <a href="<?php echo e(route('admin.update.index')); ?>"
               class="text-sm text-primary hover:underline inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">refresh</span>
                بررسی مجدد
            </a>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-3">اطلاعات نسخه فعلی</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <div class="flex justify-between">
                <span>نسخه:</span>
                <span class="font-mono font-bold"><?php echo e($current); ?></span>
            </div>
            <div class="flex justify-between">
                <span>مسیر نصب:</span>
                <span class="font-mono text-xs text-gray-400"><?php echo e(base_path()); ?></span>
            </div>
            <div class="flex justify-between">
                <span>PHP:</span>
                <span class="font-mono"><?php echo e(PHP_VERSION); ?></span>
            </div>
            <div class="flex justify-between">
                <span>Laravel:</span>
                <span class="font-mono"><?php echo e(app()->version()); ?></span>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/update/index.blade.php ENDPATH**/ ?>