

<?php $__env->startSection('title', 'مدیریت تیکت‌ها'); ?>
<?php $__env->startSection('page-title', 'تیکت‌ها'); ?>
<?php $__env->startSection('header-title', 'مدیریت تیکت‌های پشتیبانی'); ?>
<?php $__env->startSection('header-subtitle', 'نظارت و پاسخ به تمام تیکت‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    
    <div class="flex items-center justify-between mb-6">
        <div class="grid grid-cols-2 gap-4 flex-1">
            <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">inbox</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($openCount); ?></p>
                    <p class="text-sm text-gray-500">تیکت باز</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-gray-600">confirmation_number</span>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($totalCount); ?></p>
                    <p class="text-sm text-gray-500">کل تیکت‌ها</p>
                </div>
            </div>
        </div>
        <div class="mr-4">
            <a href="<?php echo e(route('admin.tickets.create')); ?>" class="flex items-center gap-2 bg-primary text-white px-4 py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-xl">add</span>
                تیکت جدید
            </a>
        </div>
    </div>

    
    <form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>"
            placeholder="جستجو در موضوع یا شماره تیکت..."
            class="flex-1 min-w-48 border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
        <select name="status" class="border border-gray-300 rounded-xl px-4 py-2 text-sm">
            <option value="">همه وضعیت‌ها</option>
            <option value="open" <?php echo e(request('status') === 'open' ? 'selected' : ''); ?>>باز</option>
            <option value="answered" <?php echo e(request('status') === 'answered' ? 'selected' : ''); ?>>پاسخ داده شده</option>
            <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>بسته</option>
        </select>
        <select name="type" class="border border-gray-300 rounded-xl px-4 py-2 text-sm">
            <option value="">همه انواع</option>
            <option value="buyer_to_seller" <?php echo e(request('type') === 'buyer_to_seller' ? 'selected' : ''); ?>>خریدار به فروشنده</option>
            <option value="buyer_to_admin" <?php echo e(request('type') === 'buyer_to_admin' ? 'selected' : ''); ?>>خریدار به ادمین</option>
            <option value="seller_to_buyer" <?php echo e(request('type') === 'seller_to_buyer' ? 'selected' : ''); ?>>فروشنده به خریدار</option>
            <option value="seller_to_admin" <?php echo e(request('type') === 'seller_to_admin' ? 'selected' : ''); ?>>فروشنده به ادمین</option>
            <option value="admin_to_buyer" <?php echo e(request('type') === 'admin_to_buyer' ? 'selected' : ''); ?>>ادمین به خریدار</option>
            <option value="admin_to_seller" <?php echo e(request('type') === 'admin_to_seller' ? 'selected' : ''); ?>>ادمین به فروشنده</option>
        </select>
        <button type="submit" class="bg-primary text-white px-5 py-2 rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors">
            فیلتر
        </button>
    </form>

    
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('admin.tickets.show', $ticket)); ?>"
                class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    <?php echo e($ticket->status === 'open' ? 'bg-blue-100 text-blue-600' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500')); ?>">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 truncate"><?php echo e($ticket->subject); ?></p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <?php echo e($ticket->ticket_number); ?> &bull;
                        <?php echo e($ticket->creator->name ?? '-'); ?> &bull;
                        <?php echo e($ticket->type_label); ?> &bull;
                        <?php echo e($ticket->listing->title ?? '-'); ?>

                    </p>
                </div>
                <div class="text-left shrink-0 space-y-1">
                    <span class="block text-xs px-2 py-1 rounded-full text-center
                        <?php echo e($ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')); ?>">
                        <?php echo e($ticket->status_label); ?>

                    </span>
                    <p class="text-xs text-gray-400 text-center"><?php echo e($ticket->last_reply_at?->diffForHumans()); ?></p>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-gray-300 text-5xl">inbox</span>
                <p class="text-gray-500 mt-3">هیچ تیکتی یافت نشد</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4"><?php echo e($tickets->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\tickets\index.blade.php ENDPATH**/ ?>