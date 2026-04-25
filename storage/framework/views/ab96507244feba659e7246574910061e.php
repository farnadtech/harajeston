

<?php $__env->startSection('title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('page-title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('header-title', 'آپدیت سیستم'); ?>
<?php $__env->startSection('header-subtitle', 'مدیریت نسخه، آپدیت و بازگردانی'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

    <?php $__currentLoopData = ['success','error','info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($type)): ?>
            <?php $colors = ['success'=>'green','error'=>'red','info'=>'blue']; $c = $colors[$type]; ?>
            <div class="bg-<?php echo e($c); ?>-50 border border-<?php echo e($c); ?>-200 text-<?php echo e($c); ?>-800 px-4 py-3 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-<?php echo e($c); ?>-600"><?php echo e($type === 'success' ? 'check_circle' : ($type === 'error' ? 'error' : 'info')); ?></span>
                <?php echo e(session($type)); ?>

            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">system_update</span>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">وضعیت نسخه</h2>
                <p class="text-xs text-gray-400">آخرین بررسی: همین الان</p>
            </div>
            <a href="<?php echo e(route('admin.update.index')); ?>" class="mr-auto text-sm text-primary hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">refresh</span> بررسی مجدد
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">نسخه نصب شده</p>
                <p class="text-2xl font-black text-gray-900"><?php echo e($current); ?></p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                <p class="text-xs text-gray-500 mb-1">آخرین نسخه</p>
                <?php if($error): ?>
                    <p class="text-sm text-red-500 mt-2"><?php echo e($error); ?></p>
                <?php elseif($latest): ?>
                    <p class="text-2xl font-black <?php echo e($hasUpdate ? 'text-green-600' : 'text-gray-900'); ?>"><?php echo e($latest); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-400 mt-2">نامشخص</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if($hasUpdate): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-green-600 mt-0.5">new_releases</span>
                <div>
                    <p class="font-bold text-green-800">نسخه <?php echo e($latest); ?> آماده نصب است</p>
                    <?php if($changelog): ?>
                        <p class="text-sm text-green-700 mt-1"><?php echo e($changelog); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 flex items-start gap-2">
                <span class="material-symbols-outlined text-yellow-600 text-[18px] mt-0.5">warning</span>
                <p class="text-xs text-yellow-800">قبل از آپدیت یک بکاپ کامل از فایل‌ها و دیتابیس گرفته می‌شود. در صورت بروز مشکل می‌توانید rollback کنید.</p>
            </div>
            <form method="POST" action="<?php echo e(route('admin.update.run')); ?>"
                  onsubmit="return confirm('آیا از نصب آپدیت مطمئن هستید؟')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">download</span>
                    نصب آپدیت <?php echo e($latest); ?>

                </button>
            </form>
        <?php elseif(!$error): ?>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <span class="material-symbols-outlined text-green-500 text-3xl">verified</span>
                <div>
                    <p class="font-bold text-gray-800">سیستم به‌روز است</p>
                    <p class="text-sm text-gray-500">شما از آخرین نسخه استفاده می‌کنید</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">آپلود دستی فایل آپدیت</h3>
                <p class="text-xs text-gray-500">فایل zip آپدیت را مستقیم آپلود کنید</p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('admin.update.upload')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="flex gap-3">
                <input type="file" name="zip_file" accept=".zip" required
                       class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-2 file:ml-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary/10 file:text-primary file:text-sm cursor-pointer">
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors whitespace-nowrap">
                    نصب
                </button>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[18px]">history</span>
            </div>
            <div>
                <h3 class="font-bold text-gray-900">بکاپ‌ها و بازگردانی</h3>
                <p class="text-xs text-gray-500">قبل از هر آپدیت یک بکاپ کامل ذخیره می‌شود</p>
            </div>
        </div>

        <?php if(count($backups) > 0): ?>
            <div class="divide-y divide-gray-100">
                <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-gray-500 text-[18px]">folder_zip</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900">نسخه <?php echo e($backup['version']); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($backup['created_at']); ?> — <?php echo e($backup['size']); ?></p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <form method="POST" action="<?php echo e(route('admin.update.rollback')); ?>"
                                  onsubmit="return confirm('بازگردانی به این نسخه؟ دیتابیس و فایل‌ها برگردانده می‌شوند.')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="backup" value="<?php echo e($backup['name']); ?>">
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">restore</span>
                                    بازگردانی
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.update.backup.delete')); ?>"
                                  onsubmit="return confirm('حذف این بکاپ؟')">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="backup" value="<?php echo e($backup['name']); ?>">
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center text-gray-400">
                <span class="material-symbols-outlined text-4xl mb-2">folder_open</span>
                <p class="text-sm">هنوز بکاپی وجود ندارد</p>
                <p class="text-xs mt-1">بکاپ‌ها قبل از هر آپدیت به صورت خودکار ساخته می‌شوند</p>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-3">اطلاعات سیستم</h3>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">نسخه فعلی</span>
                <span class="font-mono font-bold"><?php echo e($current); ?></span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">PHP</span>
                <span class="font-mono"><?php echo e(PHP_VERSION); ?></span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">Laravel</span>
                <span class="font-mono"><?php echo e(app()->version()); ?></span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50">
                <span class="text-gray-500">سرور آپدیت</span>
                <span class="font-mono text-xs text-gray-400">iranbooklet.ir/harajino</span>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/update/index.blade.php ENDPATH**/ ?>