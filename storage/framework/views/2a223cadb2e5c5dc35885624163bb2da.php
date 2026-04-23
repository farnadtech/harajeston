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
<div class="rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group bg-white border border-gray-100 flex flex-col">

    
    <a href="<?php echo e(route('listings.show', $listing)); ?>" class="relative block overflow-hidden" style="height:200px; flex-shrink:0;">
        <?php if($listing->images->isNotEmpty()): ?>
            <img alt="<?php echo e($listing->title); ?>"
                 src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
                <span class="material-symbols-outlined text-5xl text-blue-300">image</span>
            </div>
        <?php endif; ?>
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

        
        <?php if($isActive): ?>
            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">فعال</span>
        <?php elseif($isEnded): ?>
            <span class="absolute top-3 right-3 bg-gray-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">پایان یافته</span>
        <?php endif; ?>

        
        <?php if($listing->category): ?>
            <span class="absolute top-3 left-3 bg-black/40 backdrop-blur-sm text-white text-xs px-2 py-0.5 rounded-full">
                <?php echo e($listing->category->name); ?>

            </span>
        <?php endif; ?>

        
        <div class="absolute bottom-0 right-0 left-0 p-4">
            <h3 class="text-white font-bold text-sm leading-snug line-clamp-2 drop-shadow"><?php echo e($listing->title); ?></h3>
        </div>
    </a>

    
    <div class="p-4 flex items-center justify-between mt-auto">
        <div>
            <p class="text-xs text-gray-400 mb-0.5">قیمت فعلی</p>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-black text-primary leading-none">
                    <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($displayPrice))); ?>

                </span>
                <span class="text-xs text-gray-400">تومان</span>
            </div>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span class="flex items-center gap-1 text-xs text-gray-400">
                <span class="material-symbols-outlined" style="font-size:14px;">gavel</span>
                <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count ?? 0)); ?> پیشنهاد
            </span>
            <a href="<?php echo e(route('listings.show', $listing)); ?>"
               class="text-xs font-bold px-4 py-2 rounded-xl transition-all
                      <?php echo e($isActive ? 'bg-primary text-white hover:bg-blue-600 hover:shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>">
                <?php echo e($isActive ? 'شرکت در مزایده' : 'مشاهده'); ?>

            </a>
        </div>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\listing-cards\modern.blade.php ENDPATH**/ ?>