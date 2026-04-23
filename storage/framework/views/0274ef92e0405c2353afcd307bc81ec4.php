

<?php $__env->startSection('title', 'افزودن روش ارسال جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="<?php echo e(route('admin.shipping-methods.index')); ?>" class="text-gray-600 hover:text-gray-900">
            <span class="material-symbols-outlined text-2xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-black text-gray-900">افزودن روش ارسال جدید</h2>
            <p class="text-sm text-gray-500 mt-1">تعریف روش ارسال جدید برای حراجی‌ها</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="<?php echo e(route('admin.shipping-methods.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        نام روش ارسال <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="<?php echo e(old('name')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="مثال: پست پیشتاز"
                           required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        توضیحات
                    </label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                              placeholder="توضیحات اختیاری درباره این روش ارسال"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Base Cost -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        هزینه پایه (تومان) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="base_cost" 
                           value="<?php echo e(old('base_cost')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="50000"
                           min="0"
                           step="1000"
                           required>
                    <?php $__errorArgs = ['base_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Estimated Days -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        مدت زمان تحویل (روز کاری)
                    </label>
                    <input type="number" 
                           name="estimated_days" 
                           value="<?php echo e(old('estimated_days')); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                           placeholder="3"
                           min="1"
                           max="30">
                    <?php $__errorArgs = ['estimated_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Is Active -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                               class="w-5 h-5 text-primary rounded focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">فعال بودن روش ارسال</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 font-medium">
                    ذخیره روش ارسال
                </button>
                <a href="<?php echo e(route('admin.shipping-methods.index')); ?>" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\shipping-methods\create.blade.php ENDPATH**/ ?>