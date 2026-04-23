<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['listing']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['listing']); ?>
<?php foreach (array_filter((['listing']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<?php
    $displayPrice = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price;
    $isActive = $listing->status === 'active' && ($listing->ends_at === null || $listing->ends_at->isFuture());
    $isEnded  = in_array($listing->status, ['ended','completed']);
?>
<div class="bg-white rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group border border-gray-100 flex" style="min-height:120px;">

    
    <a href="<?php echo e(route('listings.show', $listing)); ?>"
       class="relative flex-shrink-0 overflow-hidden bg-gray-50"
       style="width:130px; min-height:120px;">
        <?php if($listing->images->isNotEmpty()): ?>
            <img alt="<?php echo e($listing->title); ?>"
                 src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
            <div class="absolute inset-0 flex items-center justify-center bg-gray-100">
                <span class="material-symbols-outlined text-4xl text-gray-300">image</span>
            </div>
        <?php endif; ?>
        
        <?php if($isActive): ?>
            <span class="absolute top-2 right-2 w-2 h-2 bg-green-500 rounded-full ring-2 ring-white"></span>
        <?php endif; ?>
    </a>

    
    <div class="flex-1 p-3 flex flex-col justify-between min-w-0">
        <div>
            <?php if($listing->category): ?>
                <span class="text-xs text-gray-400 block mb-0.5"><?php echo e($listing->category->name); ?></span>
            <?php endif; ?>
            <a href="<?php echo e(route('listings.show', $listing)); ?>">
                <h3 class="text-sm font-bold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors leading-snug">
                    <?php echo e($listing->title); ?>

                </h3>
            </a>
        </div>

        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="flex items-baseline gap-1">
                    <span class="text-base font-black text-primary leading-none">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($displayPrice))); ?>

                    </span>
                    <span class="text-xs text-gray-400">تومان</span>
                </div>
                <span class="flex items-center gap-0.5 text-xs text-gray-400 mt-1">
                    <span class="material-symbols-outlined" style="font-size:13px;">gavel</span>
                    <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0)); ?>

                </span>
            </div>
            <a href="<?php echo e(route('listings.show', $listing)); ?>"
               class="text-xs font-bold px-3 py-1.5 rounded-lg transition-all flex-shrink-0
                      <?php echo e($isActive ? 'bg-primary text-white hover:bg-blue-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">
                <?php echo e($isActive ? 'پیشنهاد' : 'مشاهده'); ?>

            </a>
        </div>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-cards\horizontal.blade.php ENDPATH**/ ?>