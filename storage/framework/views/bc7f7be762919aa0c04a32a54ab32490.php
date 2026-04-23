<?php if($listing->status === 'suspended'): ?>
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">block</span> تعلیق
        </span>
    </div>
<?php elseif($listing->status === 'pending' && !$listing->approved_at): ?>
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-full shadow">منتظر تایید</span>
    </div>
<?php elseif($listing->status === 'pending' && $listing->approved_at && $listing->starts_at && $listing->starts_at->isFuture()): ?>
    <?php
        $now = \Carbon\Carbon::now();
        $d = (int)$now->diffInDays($listing->starts_at);
        $h = (int)$now->diffInHours($listing->starts_at);
        $m = (int)$now->diffInMinutes($listing->starts_at);
        if ($d > 0) $t = \App\Services\PersianNumberService::convertToPersian($d).' روز تا شروع';
        elseif ($h > 0) $t = \App\Services\PersianNumberService::convertToPersian($h).' ساعت تا شروع';
        elseif ($m > 0) $t = \App\Services\PersianNumberService::convertToPersian($m).' دقیقه تا شروع';
        else $t = 'در حال شروع...';
    ?>
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow"><?php echo e($t); ?></span>
    </div>
<?php elseif(in_array($listing->status, ['ended','completed'])): ?>
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-gray-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">check_circle</span> تمام شده
        </span>
    </div>
<?php elseif($listing->status === 'active' && $listing->ends_at): ?>
    <?php
        $now = \Carbon\Carbon::now();
        $hoursLeft = (int)$now->diffInHours($listing->ends_at);
        $daysLeft = (int)$now->diffInDays($listing->ends_at);
        $minsLeft = (int)$now->diffInMinutes($listing->ends_at);
    ?>
    <?php if($now->greaterThanOrEqualTo($listing->ends_at)): ?>
    
    <div class="absolute top-3 right-3 z-10">
        <span class="px-2 py-1 bg-gray-600 text-white text-xs font-bold rounded-full shadow flex items-center gap-1">
            <span class="material-symbols-outlined text-xs">check_circle</span> تمام شده
        </span>
    </div>
    <?php else: ?>
    <?php
        if ($daysLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($daysLeft).' روز مانده';
        elseif ($hoursLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($hoursLeft).' ساعت مانده';
        elseif ($minsLeft > 0) $timeStr = \App\Services\PersianNumberService::convertToPersian($minsLeft).' دقیقه مانده';
        else $timeStr = 'کمتر از یک دقیقه';
    ?>
    <div class="absolute top-3 left-3 z-10">
        <span class="px-2 py-1 <?php echo e($hoursLeft < 3 ? 'bg-red-500 animate-pulse' : 'bg-orange-500'); ?> text-white text-xs font-bold rounded-md shadow">
            <?php echo e($timeStr); ?>

        </span>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/components/listing-cards/partials/status-badge.blade.php ENDPATH**/ ?>