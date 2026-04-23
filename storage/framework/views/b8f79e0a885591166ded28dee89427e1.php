<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['listing', 'size' => 'sm']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['listing', 'size' => 'sm']); ?>
<?php foreach (array_filter((['listing', 'size' => 'sm']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if($listing->rating_count > 0): ?>
    <div class="flex items-center gap-1">
        <?php for($i = 1; $i <= 5; $i++): ?>
            <span class="text-<?php echo e($i <= round($listing->average_rating) ? 'yellow-400' : 'gray-300'); ?> text-<?php echo e($size === 'lg' ? 'lg' : 'xs'); ?>">★</span>
        <?php endfor; ?>
        <span class="text-<?php echo e($size === 'lg' ? 'sm' : 'xs'); ?> text-gray-600 font-medium mr-1">
            <?php echo e(number_format($listing->average_rating, 1)); ?>

            <?php if($size === 'lg'): ?>
                (<?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->rating_count)); ?>)
            <?php endif; ?>
        </span>
    </div>
<?php endif; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-rating.blade.php ENDPATH**/ ?>