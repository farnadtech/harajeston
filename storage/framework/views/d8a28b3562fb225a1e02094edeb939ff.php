<div wire:poll.5s="loadBiddingData">
    
    <div class="flex justify-between items-end mb-3">
        <div>
            <span class="block text-gray-500 text-sm mb-1">
                <?php if($listing->bids->count() > 0): ?>
                    بالاترین پیشنهاد فعلی
                <?php else: ?>
                    قیمت پایه
                <?php endif; ?> <!-- __ENDBLOCK__ -->
            </span>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-black text-primary">
                    <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($currentHighestBid))); ?>

                </span>
                <span class="text-gray-500 font-medium">تومان</span>
            </div>
        </div>
        <div class="text-left">
            <span class="block text-xs text-gray-400 mb-1">تعداد پیشنهادها</span>
            <span class="font-bold text-gray-800 text-lg"><?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids->count())); ?> پیشنهاد</span>
        </div>
    </div>

    
    <div class="space-y-4">
        <!-- __BLOCK__ --><?php if($successMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm">
                <?php echo e($successMessage); ?>

            </div>
        <?php endif; ?> <!-- __ENDBLOCK__ -->

        <!-- __BLOCK__ --><?php if($errorMessage): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl text-sm">
                <?php echo e($errorMessage); ?>

            </div>
        <?php endif; ?> <!-- __ENDBLOCK__ -->

        <!-- __BLOCK__ --><?php if($listing->status === 'active'): ?>
            <form wire:submit.prevent="placeBid">
                <label class="block text-sm font-bold text-gray-700 mb-3">پیشنهاد خود را وارد کنید</label>
                <div class="relative mb-4">
                    <input 
                        type="number" 
                        wire:model="bidAmount"
                        class="block w-full text-left ltr h-14 pr-4 pl-16 bg-white border-2 border-gray-200 rounded-xl focus:bg-white focus:border-primary focus:ring-primary text-xl font-bold transition-colors"
                        placeholder="<?php echo e(number_format($currentHighestBid + 100000)); ?>"
                        min="<?php echo e($currentHighestBid + 1); ?>"
                        step="any"
                    />
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-medium pointer-events-none">تومان</span>
                </div>
                <!-- __BLOCK__ --><?php $__errorArgs = ['bidAmount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-red-500 text-sm mb-3"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <!-- __ENDBLOCK__ -->
                
                <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar mb-4">
                    <button type="button" wire:click="incrementBid(50000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                        + 50,000
                    </button>
                    <button type="button" wire:click="incrementBid(100000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                        + 100,000
                    </button>
                    <button type="button" wire:click="incrementBid(200000)" class="whitespace-nowrap px-4 py-2 rounded-lg border border-gray-200 hover:border-primary hover:bg-primary/5 text-sm font-medium text-gray-600 hover:text-primary transition-all">
                        + 200,000
                    </button>
                </div>
                
                <button 
                    type="submit"
                    class="w-full h-14 bg-primary hover:bg-blue-600 text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 flex items-center justify-center gap-2 transition-all transform active:scale-[0.99]"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove class="material-symbols-outlined">gavel</span>
                    <span wire:loading.remove>ثبت پیشنهاد</span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                        در حال ثبت...
                    </span>
                </button>
                
                <p class="text-xs text-center text-gray-500 mt-2">
                    با ثبت پیشنهاد، <a class="text-primary hover:underline" href="#">قوانین مزایده</a> را می‌پذیرید.
                </p>
            </form>
        <?php else: ?>
            <div class="bg-gray-100 text-gray-600 px-4 py-3 rounded-xl text-center">
                مزایده پایان یافته است
            </div>
        <?php endif; ?> <!-- __ENDBLOCK__ -->
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/livewire/auction-bidding.blade.php ENDPATH**/ ?>