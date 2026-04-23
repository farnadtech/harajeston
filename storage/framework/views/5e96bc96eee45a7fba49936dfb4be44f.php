<div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
    <h3 class="text-xl font-bold mb-4">خرید مستقیم</h3>
    
    <div class="mb-4">
        <p class="text-gray-600 mb-2">قیمت:</p>
        <p class="text-3xl font-bold text-green-600">
            <?php echo app(\App\Services\PersianNumberService::class)->formatCurrency($listing->price); ?>
        </p>
    </div>

    <div class="mb-4">
        <p class="text-gray-600 mb-2">موجودی انبار:</p>
        <?php if($stock > 0): ?>
            <p class="text-lg font-bold text-blue-600">
                <?php echo app(\App\Services\PersianNumberService::class)->toPersian($stock); ?> عدد
            </p>
        <?php else: ?>
            <p class="text-lg font-bold text-red-600">
                ناموجود
            </p>
        <?php endif; ?>
    </div>

    <?php if($successMessage): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e($successMessage); ?>

        </div>
    <?php endif; ?>

    <?php if($errorMessage): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo e($errorMessage); ?>

        </div>
    <?php endif; ?>

    <?php if($stock > 0): ?>
        <div class="mb-4">
            <label for="quantity" class="block text-gray-700 mb-2">تعداد:</label>
            <input 
                type="number" 
                id="quantity"
                wire:model="quantity"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                min="1"
                max="<?php echo e($stock); ?>"
            >
            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                <span class="text-red-500 text-sm"><?php echo e($message); ?></span> 
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="space-y-3">
            <button 
                wire:click="addToCart"
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="addToCart">افزودن به سبد خرید</span>
                <span wire:loading wire:target="addToCart">در حال افزودن...</span>
            </button>

            <button 
                wire:click="buyNow"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="buyNow">خرید سریع</span>
                <span wire:loading wire:target="buyNow">در حال پردازش...</span>
            </button>
        </div>
    <?php else: ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded">
            این محصول در حال حاضر موجود نیست
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\livewire\direct-sale-purchase.blade.php ENDPATH**/ ?>