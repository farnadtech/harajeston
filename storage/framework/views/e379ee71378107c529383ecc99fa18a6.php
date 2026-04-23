

<?php $__env->startSection('title', 'تیکت ' . $ticket->ticket_number); ?>
<?php $__env->startSection('page-title', 'مشاهده تیکت'); ?>
<?php $__env->startSection('header-title', 'تیکت ' . $ticket->ticket_number); ?>
<?php $__env->startSection('header-subtitle', $ticket->subject); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-3xl mx-auto">

    
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-400 text-xs mb-1">فرستنده</p>
                <?php if($ticket->creator): ?>
                    <a href="<?php echo e(route('admin.users.show', $ticket->creator)); ?>"
                       class="font-medium text-primary hover:underline">
                        <?php echo e($ticket->creator->name); ?>

                    </a>
                <?php else: ?>
                    <span class="font-medium text-gray-500">-</span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">گیرنده</p>
                <?php if($ticket->recipient): ?>
                    <a href="<?php echo e(route('admin.users.show', $ticket->recipient)); ?>"
                       class="font-medium text-primary hover:underline">
                        <?php echo e($ticket->recipient->name); ?>

                    </a>
                <?php else: ?>
                    <span class="font-medium text-gray-500">ادمین</span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">نوع</p>
                <p class="font-medium"><?php echo e($ticket->type_label); ?></p>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">حراجی</p>
                <?php if($ticket->listing): ?>
                    <a href="<?php echo e(route('admin.listings.show', $ticket->listing)); ?>"
                       class="font-medium text-primary hover:underline truncate block">
                        <?php echo e($ticket->listing->title); ?>

                    </a>
                <?php else: ?>
                    <span class="font-medium text-gray-500">-</span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">وضعیت</p>
                <span class="px-2 py-1 rounded-full text-xs
                    <?php echo e($ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')); ?>">
                    <?php echo e($ticket->status_label); ?>

                </span>
            </div>
            <div>
                <p class="text-gray-400 text-xs mb-1">اولویت</p>
                <span class="px-2 py-1 rounded-full text-xs
                    <?php echo e($ticket->priority === 'high' ? 'bg-red-100 text-red-700' : ($ticket->priority === 'normal' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')); ?>">
                    <?php echo e($ticket->priority_label); ?>

                </span>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="space-y-4 mb-6">
        <?php $__currentLoopData = $ticket->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $isAdmin = $msg->user->role === 'admin'; ?>
            <div class="flex gap-3 <?php echo e($isAdmin ? 'flex-row-reverse' : ''); ?>">
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 text-sm font-bold
                    <?php echo e($isAdmin ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600'); ?>">
                    <?php echo e(mb_substr($msg->user->name ?? '?', 0, 1)); ?>

                </div>
                <div class="max-w-[75%]">
                    <div class="flex items-center gap-2 mb-1 <?php echo e($isAdmin ? 'flex-row-reverse' : ''); ?>">
                        <span class="text-xs font-medium text-gray-700">
                            <?php echo e($msg->user->name ?? 'کاربر'); ?>

                            <?php if($isAdmin): ?>
                                <span class="bg-red-100 text-red-600 text-[10px] px-1.5 py-0.5 rounded-full mr-1">ادمین</span>
                            <?php endif; ?>
                        </span>
                        <span class="text-[11px] text-gray-400"><?php echo e($msg->created_at->diffForHumans()); ?></span>
                    </div>
                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
                        <?php echo e($isAdmin ? 'bg-red-50 border border-red-100 text-gray-800 rounded-tl-sm' : 'bg-white border border-gray-200 text-gray-800 rounded-tr-sm'); ?>">
                        <?php echo nl2br(e($msg->message)); ?>

                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <?php if(!$ticket->isClosed()): ?>
            <form action="<?php echo e(route('admin.tickets.reply', $ticket)); ?>" method="POST" class="mb-5">
                <?php echo csrf_field(); ?>
                <label class="block text-sm font-medium text-gray-700 mb-2">پاسخ ادمین</label>
                <textarea name="message" rows="4"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary resize-none mb-3"
                    placeholder="پاسخ خود را بنویسید..." required maxlength="5000"></textarea>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl font-medium hover:bg-primary/90 transition-colors text-sm">
                    ارسال پاسخ
                </button>
            </form>
        <?php endif; ?>

        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <?php if(!$ticket->isClosed()): ?>
                <form action="<?php echo e(route('admin.tickets.close', $ticket)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors"
                        onclick="return confirm('بستن تیکت؟')">
                        بستن تیکت
                    </button>
                </form>
            <?php else: ?>
                <form action="<?php echo e(route('admin.tickets.reopen', $ticket)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm px-4 py-2 bg-blue-100 text-blue-700 rounded-xl hover:bg-blue-200 transition-colors">
                        باز کردن مجدد
                    </button>
                </form>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.tickets.index')); ?>" class="text-sm px-4 py-2 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
                بازگشت به لیست
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\tickets\show.blade.php ENDPATH**/ ?>