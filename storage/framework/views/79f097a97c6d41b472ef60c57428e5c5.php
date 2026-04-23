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
?>
<div class="bg-white rounded-xl overflow-hidden hover:shadow-md transition-all duration-200 group border border-gray-100 hover:border-primary/20 flex flex-col">

    
    <a href="<?php echo e(route('listings.show', $listing)); ?>" class="relative block overflow-hidden" style="height:180px; flex-shrink:0;">
        <?php if($listing->images->isNotEmpty()): ?>
            <img alt="<?php echo e($listing->title); ?>"
                 src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
            <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-gray-200">image</span>
            </div>
        <?php endif; ?>
        <?php if($isActive): ?>
            <span class="absolute top-2 right-2 w-2 h-2 bg-green-500 rounded-full ring-2 ring-white"></span>
        <?php endif; ?>
    </a>

    
    <div class="p-3 flex flex-col flex-1">
        <a href="<?php echo e(route('listings.show', $listing)); ?>">
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 group-hover:text-primary transition-colors leading-snug mb-2">
                <?php echo e($listing->title); ?>

            </h3>
        </a>

        <?php if($listing->category): ?>
            <span class="text-xs text-gray-400 mb-2"><?php echo e($listing->category->name); ?></span>
        <?php endif; ?>

        <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-between">
            <div>
                <span class="text-base font-black text-gray-900">
                    <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($displayPrice))); ?>

                </span>
                <span class="text-xs text-gray-400 mr-0.5">تومان</span>
            </div>
            <a href="<?php echo e(route('listings.show', $listing)); ?>"
               class="text-xs font-bold px-3 py-1.5 rounded-lg transition-colors
                      <?php echo e($isActive ? 'text-primary border border-primary hover:bg-primary hover:text-white' : 'text-gray-400 border border-gray-200'); ?>">
                <?php echo e($isActive ? 'پیشنهاد' : 'مشاهده'); ?>

            </a>
        </div>

        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
            <span class="flex items-center gap-0.5">
                <span class="material-symbols-outlined" style="font-size:13px;">gavel</span>
                <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0)); ?>

            </span>
            <?php if($listing->ends_at && $isActive): ?>
                <span class="flex items-center gap-0.5 mr-auto">
                    <span class="material-symbols-outlined" style="font-size:13px;">schedule</span>
                    <?php
                        $h = (int)now()->diffInHours($listing->ends_at);
                        $d = (int)now()->diffInDays($listing->ends_at);
                    ?>
                    <?php echo e($d > 0 ? \App\Services\PersianNumberService::convertToPersian($d).' روز' : \App\Services\PersianNumberService::convertToPersian($h).' ساعت'); ?>

                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-cards\minimal.blade.php ENDPATH**/ ?>