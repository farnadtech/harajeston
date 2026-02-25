

<?php $__env->startSection('title', 'مدیریت کمیسیون دسته‌بندی‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">مدیریت کمیسیون دسته‌بندی‌ها</h1>
            <a href="<?php echo e(route('admin.settings.index')); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                بازگشت به تنظیمات
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">افزودن/ویرایش کمیسیون</h2>
            
            <form action="<?php echo e(route('admin.category-commissions.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="category_id" class="block text-gray-700 font-bold mb-2">دسته‌بندی</label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">انتخاب دسته‌بندی</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>">
                                <?php echo e($category->getFullPath()); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-2">نوع کمیسیون</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="type" value="fixed" class="ml-2" required>
                            <span>مبلغ ثابت</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="type" value="percentage" class="ml-2" checked required>
                            <span>درصد</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="fixed_amount" class="block text-gray-700 font-bold mb-2">مبلغ ثابت (تومان)</label>
                    <input type="number" name="fixed_amount" id="fixed_amount" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" step="1">
                </div>

                <div>
                    <label for="percentage" class="block text-gray-700 font-bold mb-2">درصد (%)</label>
                    <input type="number" name="percentage" id="percentage" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" max="100" step="0.01">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره کمیسیون
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">لیست کمیسیون‌های تعریف شده</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">دسته‌بندی</th>
                            <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">نوع</th>
                            <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">مقدار</th>
                            <th class="px-6 py-3 text-right text-sm font-bold text-gray-700">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $categories->filter(fn($c) => $c->categoryCommission); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <?php echo e($category->getFullPath()); ?>

                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if($category->categoryCommission->type === 'fixed'): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">مبلغ ثابت</span>
                                    <?php else: ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">درصد</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold">
                                    <?php if($category->categoryCommission->type === 'fixed'): ?>
                                        <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($category->categoryCommission->fixed_amount, true); ?> تومان
                                    <?php else: ?>
                                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($category->categoryCommission->percentage)); ?>%
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <form action="<?php echo e(route('admin.category-commissions.destroy', $category->categoryCommission->id)); ?>" 
                                          method="POST" 
                                          onsubmit="return confirm('آیا از حذف این کمیسیون اطمینان دارید؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    هیچ کمیسیونی تعریف نشده است
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h3 class="font-bold text-blue-900 mb-2">نکته مهم:</h3>
            <p class="text-blue-800 text-sm">
                اگر برای یک دسته‌بندی کمیسیون تعریف نشده باشد، به صورت خودکار از کمیسیون دسته‌بندی والد استفاده می‌شود.
                اگر هیچ دسته‌بندی والدی هم کمیسیون نداشته باشد، از تنظیمات پیش‌فرض سایت استفاده خواهد شد.
            </p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/category-commissions/index.blade.php ENDPATH**/ ?>