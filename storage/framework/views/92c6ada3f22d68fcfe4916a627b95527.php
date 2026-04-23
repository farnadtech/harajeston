<?php if (isset($component)) { $__componentOriginal895f6ef515592ffd4805667c75b9d7a7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal895f6ef515592ffd4805667c75b9d7a7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard-layout','data' => ['pageTitle' => 'تیکت‌های پشتیبانی']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dashboard-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'تیکت‌های پشتیبانی']); ?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تیکت‌های پشتیبانی</h1>
            <p class="text-sm text-gray-500 mt-1">مدیریت درخواست‌ها و مکاتبات</p>
        </div>
        <a href="<?php echo e(route('tickets.create')); ?>" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl font-medium hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-xl">add</span>
            تیکت جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $unread = $ticket->unreadCountFor(auth()->id()); ?>
            <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="flex items-center gap-4 px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                    <?php echo e($ticket->status === 'open' ? 'bg-blue-100 text-blue-600' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500')); ?>">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-<?php echo e($unread > 0 ? 'bold' : 'medium'); ?> text-gray-900 truncate"><?php echo e($ticket->subject); ?></p>
                        <?php if($unread > 0): ?>
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full shrink-0"><?php echo e($unread); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <?php echo e($ticket->ticket_number); ?> &bull;
                        <?php echo e($ticket->listing->title ?? '-'); ?> &bull;
                        <?php echo e($ticket->type_label); ?>

                    </p>
                </div>
                <div class="text-left shrink-0">
                    <span class="text-xs px-2 py-1 rounded-full
                        <?php echo e($ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : ($ticket->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')); ?>">
                        <?php echo e($ticket->status_label); ?>

                    </span>
                    <p class="text-xs text-gray-400 mt-1"><?php echo e($ticket->last_reply_at?->diffForHumans()); ?></p>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="py-16 text-center">
                <span class="material-symbols-outlined text-gray-300 text-5xl">inbox</span>
                <p class="text-gray-500 mt-3">هیچ تیکتی وجود ندارد</p>
                <a href="<?php echo e(route('tickets.create')); ?>" class="mt-4 inline-flex items-center gap-2 text-primary font-medium hover:underline">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    ایجاد اولین تیکت
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4"><?php echo e($tickets->links()); ?></div>
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal895f6ef515592ffd4805667c75b9d7a7)): ?>
<?php $attributes = $__attributesOriginal895f6ef515592ffd4805667c75b9d7a7; ?>
<?php unset($__attributesOriginal895f6ef515592ffd4805667c75b9d7a7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal895f6ef515592ffd4805667c75b9d7a7)): ?>
<?php $component = $__componentOriginal895f6ef515592ffd4805667c75b9d7a7; ?>
<?php unset($__componentOriginal895f6ef515592ffd4805667c75b9d7a7); ?>
<?php endif; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\tickets\index.blade.php ENDPATH**/ ?>