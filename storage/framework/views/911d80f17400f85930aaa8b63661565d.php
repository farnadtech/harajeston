

<?php $__env->startSection('title', 'مدیریت ویژگی‌های دسته‌بندی'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ویژگی‌های دسته‌بندی: <?php echo e($category->name); ?></h1>
            <p class="text-sm text-gray-600 mt-1">مسیر: <?php echo e($category->getFullPath()); ?></p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('admin.categories.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                بازگشت
            </a>
            <a href="<?php echo e(route('admin.category-attributes.create', $category)); ?>" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                افزودن ویژگی جدید
            </a>
        </div>
    </div>

    
    <?php if($category->children && $category->children->count() > 0): ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
        <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
        <div>
            <p class="font-medium">ارث‌بری ویژگی‌ها</p>
            <p class="text-sm mt-1">ویژگی‌های این دسته به تمام زیردسته‌هایی که ویژگی خاص ندارند، به صورت خودکار اعمال می‌شود.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <?php if($attributes->isEmpty()): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl text-gray-400">tune</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">هیچ ویژگی‌ای تعریف نشده</h3>
            <p class="text-gray-600 mb-4">برای این دسته‌بندی هنوز ویژگی‌ای اضافه نشده است.</p>
            <a href="<?php echo e(route('admin.category-attributes.create', $category)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                <span class="material-symbols-outlined text-sm">add</span>
                افزودن اولین ویژگی
            </a>
        </div>
        <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نام ویژگی</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">گزینه‌ها</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الزامی</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">قابل فیلتر</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">ترتیب</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo e($attribute->name); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?php if($attribute->type === 'select'): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">انتخابی</span>
                        <?php elseif($attribute->type === 'text'): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">متنی</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">عددی</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?php if($attribute->options): ?>
                            <div class="flex flex-wrap gap-1">
                                <?php $__currentLoopData = array_slice($attribute->options, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs"><?php echo e($option); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($attribute->options) > 3): ?>
                                <span class="text-xs text-gray-500">+<?php echo e(count($attribute->options) - 3); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($attribute->is_required): ?>
                            <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-gray-300 text-sm">cancel</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($attribute->is_filterable): ?>
                            <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                        <?php else: ?>
                            <span class="material-symbols-outlined text-gray-300 text-sm">cancel</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600"><?php echo e($attribute->order); ?></td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?php echo e(route('admin.category-attributes.edit', [$category, $attribute])); ?>" class="text-blue-600 hover:text-blue-800">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="<?php echo e(route('admin.category-attributes.destroy', [$category, $attribute])); ?>" method="POST" class="inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\category-attributes\index.blade.php ENDPATH**/ ?>