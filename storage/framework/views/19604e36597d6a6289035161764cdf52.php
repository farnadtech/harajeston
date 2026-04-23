<div dir="rtl">
    <div class="mb-6 flex gap-3">
        <button 
            wire:click="setFilter('all')"
            class="px-4 py-2 rounded-lg transition <?php echo e($filterType === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'); ?>"
        >
            همه
        </button>
        <button 
            wire:click="setFilter('auction')"
            class="px-4 py-2 rounded-lg transition <?php echo e($filterType === 'auction' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'); ?>"
        >
            مزایده
        </button>
        <button 
            wire:click="setFilter('direct_sale')"
            class="px-4 py-2 rounded-lg transition <?php echo e($filterType === 'direct_sale' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'); ?>"
        >
            فروش مستقیم
        </button>
        <button 
            wire:click="setFilter('hybrid')"
            class="px-4 py-2 rounded-lg transition <?php echo e($filterType === 'hybrid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'); ?>"
        >
            ترکیبی
        </button>
    </div>

    <?php if($listings->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <?php if($listing->images->count() > 0): ?>
                        <img 
                            src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>" 
                            alt="<?php echo e($listing->title); ?>"
                            class="w-full h-48 object-cover"
                        >
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">بدون تصویر</span>
                        </div>
                    <?php endif; ?>

                    <div class="p-4">
                        <div class="mb-2">
                            <?php if($listing->type === 'auction'): ?>
                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">مزایده</span>
                            <?php elseif($listing->type === 'direct_sale'): ?>
                                <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">فروش مستقیم</span>
                            <?php else: ?>
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">ترکیبی</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-lg font-bold mb-2"><?php echo e($listing->title); ?></h3>
                        
                        <?php if($listing->type === 'auction' || $listing->type === 'hybrid'): ?>
                            <p class="text-gray-600 mb-2">
                                قیمت پایه: <span class="font-bold"><?php echo app(\App\Services\PersianNumberService::class)->formatCurrency($listing->base_price); ?></span>
                            </p>
                        <?php endif; ?>

                        <?php if($listing->type === 'direct_sale' || $listing->type === 'hybrid'): ?>
                            <p class="text-gray-600 mb-2">
                                قیمت: <span class="font-bold text-green-600"><?php echo app(\App\Services\PersianNumberService::class)->formatCurrency($listing->price); ?></span>
                            </p>
                        <?php endif; ?>

                        <a 
                            href="<?php echo e(route('listings.show', $listing->id)); ?>"
                            class="block text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
                        >
                            مشاهده جزئیات
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6">
            <?php echo e($listings->links()); ?>

        </div>
    <?php else: ?>
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500 text-lg">هیچ آگهی فعالی یافت نشد</p>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\livewire\store-listings.blade.php ENDPATH**/ ?>