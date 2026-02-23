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
    
    <?php if($listing->status === 'suspended'): ?>
        <div class="absolute top-3 right-3 z-10">
            <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-full shadow-lg flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">block</span>
                تعلیق شده
            </span>
        </div>
    <?php elseif($listing->status === 'pending' && $listing->starts_at && $listing->starts_at->isFuture()): ?>
        <?php
            $now = \Carbon\Carbon::now();
            $diff = $now->diff($listing->starts_at);
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;
            
            if ($days > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($days) . ' روز تا شروع';
            } elseif ($hours > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت تا شروع';
            } elseif ($minutes > 0) {
                $timeUntilStart = \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه تا شروع';
            } else {
                $timeUntilStart = 'در حال شروع...';
            }
        ?>
        <div class="absolute top-3 right-3 z-10">
            <span class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-lg">
                <?php echo e($timeUntilStart); ?>

            </span>
        </div>
    <?php elseif($listing->status === 'active' && $listing->ends_at): ?>
        <?php
            $hoursLeft = $listing->ends_at->diffInHours(now());
        ?>
        <div class="absolute top-3 left-3 z-10">
            <span class="px-2 py-1 <?php echo e($hoursLeft < 3 ? 'bg-red-500 animate-pulse' : 'bg-orange-500'); ?> text-white text-xs font-bold rounded-md shadow-sm">
                <?php
                    $now = \Carbon\Carbon::now();
                    if ($now->greaterThanOrEqualTo($listing->ends_at)) {
                        echo 'پایان یافته';
                    } else {
                        $diff = $now->diff($listing->ends_at);
                        $days = $diff->d;
                        $hours = $diff->h;
                        $minutes = $diff->i;
                        
                        if ($days > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($days) . ' روز مانده';
                        } elseif ($hours > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت مانده';
                        } elseif ($minutes > 0) {
                            echo \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه مانده';
                        } else {
                            echo 'کمتر از یک دقیقه';
                        }
                    }
                ?>
            </span>
        </div>
    <?php elseif($listing->status === 'completed'): ?>
    <div class="absolute top-3 right-3 z-10">
        <span class="px-3 py-1.5 bg-gray-500 text-white text-xs font-bold rounded-full shadow-lg">
            تمام شده
        </span>
    </div>
    <?php endif; ?>

    <?php if($listing->buy_now_price): ?>
        <div class="absolute top-3 <?php echo e(($listing->status === 'pending' && $listing->starts_at && $listing->starts_at->isFuture()) ? 'left-3' : 'right-3'); ?> z-10">
            <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-md shadow-sm flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">bolt</span>
                خرید فوری
            </span>
        </div>
    <?php endif; ?>

    
    <a href="<?php echo e(route('listings.show', $listing)); ?>" class="h-56 w-full bg-gray-50 relative overflow-hidden block">
        <?php if($listing->images->isNotEmpty()): ?>
            <img alt="<?php echo e($listing->title); ?>" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"/>
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <span class="material-symbols-outlined text-6xl">image</span>
            </div>
        <?php endif; ?>
    </a>

    
    <div class="p-4">
        
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
                <?php echo e($listing->category ? $listing->category->name : 'بدون دسته'); ?>

            </span>
        </div>

        
        <a href="<?php echo e(route('listings.show', $listing)); ?>">
            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-primary transition-colors line-clamp-1">
                <?php echo e($listing->title); ?>

            </h3>
        </a>

        
        <div class="flex items-baseline gap-2 mb-3">
            <span class="text-2xl font-black text-primary">
                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->current_price ?? $listing->starting_price))); ?>

            </span>
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

        
        <?php if($listing->status === 'suspended'): ?>
            <button disabled class="block w-full py-2.5 bg-red-100 text-red-700 text-sm font-bold rounded-lg cursor-not-allowed text-center border border-red-300">
                این آگهی تعلیق شده است
            </button>
        <?php elseif($listing->status === 'pending'): ?>
            <button disabled class="block w-full py-2.5 bg-gray-300 text-gray-600 text-sm font-bold rounded-lg cursor-not-allowed text-center">
                هنوز شروع نشده
            </button>
        <?php elseif($listing->status === 'completed'): ?>
            <a href="<?php echo e(route('listings.show', $listing)); ?>" class="block w-full py-2.5 bg-gray-400 text-white text-sm font-bold rounded-lg hover:bg-gray-500 transition-colors shadow-lg text-center">
                مشاهده نتیجه
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('listings.show', $listing)); ?>" class="block w-full py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20 text-center">
                ثبت پیشنهاد
            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/components/listing-card.blade.php ENDPATH**/ ?>