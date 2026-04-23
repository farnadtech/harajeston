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

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group relative">
    <?php echo $__env->make('components.listing-cards.partials.status-badge', ['listing' => $listing], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <a href="<?php echo e(route('listings.show', $listing)); ?>" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
        <?php if($listing->images->isNotEmpty()): ?>
            <img alt="<?php echo e($listing->title); ?>" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                 src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"/>
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <span class="material-symbols-outlined text-6xl">image</span>
            </div>
        <?php endif; ?>
    </a>

    <div class="p-4">
        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
            <?php echo e($listing->category ? $listing->category->name : 'بدون دسته'); ?>

        </span>
        <a href="<?php echo e(route('listings.show', $listing)); ?>">
            <h3 class="text-lg font-bold text-gray-900 mt-2 mb-1 group-hover:text-primary transition-colors line-clamp-1">
                <?php echo e($listing->title); ?>

            </h3>
        </a>
        <?php $displayPrice = $listing->bids()->orderBy('amount','desc')->value('amount') ?? $listing->starting_price; ?>
        <div class="flex items-baseline gap-2 mb-3">
            <span class="text-2xl font-black text-primary"><?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($displayPrice))); ?></span>
            <span class="text-sm text-gray-500">تومان</span>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 mb-3 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">gavel</span>
                <span><?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0)); ?> پیشنهاد</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">visibility</span>
                <span><?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->views)); ?> بازدید</span>
            </div>
        </div>
        <?php echo $__env->make('components.listing-cards.partials.action-button', ['listing' => $listing], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-cards\classic.blade.php ENDPATH**/ ?>