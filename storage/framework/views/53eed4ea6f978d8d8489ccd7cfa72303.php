

<?php $__env->startSection('title', 'جزئیات کمیسیون‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">جزئیات کمیسیون‌ها</h1>
        
        <div class="flex gap-4">
            <a href="<?php echo e(route('admin.financial-reports.index')); ?>" 
               class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                بازگشت به گزارشات
            </a>
        </div>
    </div>

    <!-- فیلتر بازه زمانی -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="<?php echo e(route('admin.financial-reports.commissions')); ?>" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                <input type="date" 
                       name="start_date" 
                       value="<?php echo e($startDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                <input type="date" 
                       name="end_date" 
                       value="<?php echo e($endDate->format('Y-m-d')); ?>"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    اعمال فیلتر
                </button>
            </div>
        </form>
    </div>

    <!-- جدول کمیسیون‌ها -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-right py-4 px-6 font-bold text-gray-700">تاریخ</th>
                        <th class="text-right py-4 px-6 font-bold text-gray-700">شرح</th>
                        <th class="text-right py-4 px-6 font-bold text-gray-700">مبلغ</th>
                        <th class="text-right py-4 px-6 font-bold text-gray-700">موجودی قبل</th>
                        <th class="text-right py-4 px-6 font-bold text-gray-700">موجودی بعد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-medium"><?php echo e(\Morilog\Jalali\Jalalian::fromCarbon($commission->created_at)->format('Y/m/d')); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo e($commission->created_at->format('H:i')); ?></p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-sm"><?php echo e($commission->description); ?></p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-green-600 font-bold">
                                    +<?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commission->amount)); ?> تومان
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commission->balance_before)); ?> تومان
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commission->balance_after)); ?> تومان
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="material-symbols-outlined text-6xl text-gray-300">receipt_long</span>
                                    <p class="text-gray-500 text-lg">هیچ کمیسیونی در این بازه زمانی یافت نشد</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($commissions->hasPages()): ?>
            <div class="px-6 py-4 border-t">
                <?php echo e($commissions->links('vendor.pagination.custom')); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- خلاصه -->
    <?php if($commissions->count() > 0): ?>
        <div class="bg-blue-50 rounded-lg p-6 mt-8">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <p class="text-blue-600 text-sm mb-2">تعداد تراکنش‌ها</p>
                    <p class="text-2xl font-bold text-blue-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commissions->total())); ?></p>
                </div>
                <div>
                    <p class="text-blue-600 text-sm mb-2">کل کمیسیون دریافتی</p>
                    <p class="text-2xl font-bold text-blue-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commissions->sum('amount'))); ?> تومان</p>
                </div>
                <div>
                    <p class="text-blue-600 text-sm mb-2">میانگین کمیسیون</p>
                    <p class="text-2xl font-bold text-blue-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($commissions->avg('amount'))); ?> تومان</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\financial-reports\commissions.blade.php ENDPATH**/ ?>