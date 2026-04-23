

<?php $__env->startSection('content'); ?>
<main class="flex-grow w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-gray-900 mb-2">ثبت نظر درباره فروشنده</h1>
            <p class="text-gray-600">نظر شما پس از تایید مدیر منتشر خواهد شد</p>
        </div>

        
        <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-primary text-3xl">receipt_long</span>
                <div>
                    <div class="font-bold text-gray-900">سفارش #<?php echo app(\App\Services\PersianNumberService::class)->toPersian($order->id); ?></div>
                    <div class="text-sm text-gray-600">فروشنده: <?php echo e($order->seller->name); ?></div>
                </div>
            </div>
        </div>

        
        <form method="POST" action="<?php echo e(route('seller-reviews.store', $order)); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>

            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">امتیاز شما</label>
                <div class="flex items-center gap-2" x-data="{ rating: 0, hover: 0 }">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <button 
                            type="button"
                            @click="rating = <?php echo e($i); ?>"
                            @mouseenter="hover = <?php echo e($i); ?>"
                            @mouseleave="hover = 0"
                            class="text-4xl transition-colors focus:outline-none"
                            :class="(hover >= <?php echo e($i); ?> || (hover === 0 && rating >= <?php echo e($i); ?>)) ? 'text-yellow-400' : 'text-gray-300'"
                        >
                            ★
                        </button>
                    <?php endfor; ?>
                    <input type="hidden" name="rating" x-model="rating" required>
                    <span class="text-sm text-gray-600 mr-2" x-show="rating > 0" x-text="rating + ' از ۵'"></span>
                </div>
                <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-2"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div x-data="{ charCount: <?php echo e(strlen(old('comment', ''))); ?> }">
                <label for="comment" class="block text-sm font-bold text-gray-700 mb-2">نظر شما</label>
                <textarea 
                    id="comment" 
                    name="comment" 
                    rows="6" 
                    required
                    minlength="10"
                    maxlength="1000"
                    @input="charCount = $event.target.value.length"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent resize-none"
                    placeholder="تجربه خود از خرید از این فروشنده را بنویسید..."
                ><?php echo e(old('comment')); ?></textarea>
                <div class="flex justify-between items-center mt-2">
                    <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm"><?php echo e($message); ?></p>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">حداقل ۱۰ و حداکثر ۱۰۰۰ کاراکتر</p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="text-sm text-gray-400">
                        <span x-text="charCount"></span> / ۱۰۰۰
                    </p>
                </div>
            </div>

            
            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-blue-600 transition-colors shadow-lg shadow-primary/20"
                >
                    ثبت نظر
                </button>
                <a 
                    href="<?php echo e(route('orders.show', $order)); ?>" 
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors"
                >
                    انصراف
                </a>
            </div>
        </form>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\seller-reviews\create.blade.php ENDPATH**/ ?>