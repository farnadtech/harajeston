<?php if (isset($component)) { $__componentOriginal895f6ef515592ffd4805667c75b9d7a7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal895f6ef515592ffd4805667c75b9d7a7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dashboard-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('title', null, []); ?> اعلان‌ها <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> اعلان‌ها <?php $__env->endSlot(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">notifications</span>
                </div>
                <h2 class="text-lg font-bold text-gray-900">همه اعلان‌ها</h2>
            </div>
            <?php if($notifications->where('is_read', false)->count() > 0): ?>
                <button onclick="markAllAsRead()" class="text-sm text-primary hover:text-blue-700 font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">done_all</span>
                    <span>علامت‌گذاری همه به عنوان خوانده شده</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($notification->link ? (auth()->user()->role === 'admin' ? route('admin.notifications.read', $notification->id) : route('user.notifications.read', $notification->id)) : '#'); ?>"
                   class="block px-6 py-4 hover:bg-gray-50 transition-colors <?php echo e(!$notification->is_read ? 'bg-blue-50/50' : ''); ?>"
                   <?php if(!$notification->link): ?> onclick="event.preventDefault();" style="cursor: default;" <?php endif; ?>>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-<?php echo e($notification->color); ?>-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-<?php echo e($notification->color); ?>-600 text-2xl"><?php echo e($notification->icon); ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <p class="text-base font-medium text-gray-900"><?php echo e($notification->title); ?></p>
                                <?php if(!$notification->is_read): ?>
                                    <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-600 mb-2"><?php echo e($notification->message); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($notification->time_ago); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-6xl mb-3 block">notifications_off</span>
                    <p class="text-gray-500">اعلانی وجود ندارد</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($notifications->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($notifications->links('vendor.pagination.custom')); ?>

            </div>
        <?php endif; ?>
    </div>

     <?php $__env->slot('scripts', null, []); ?> 
        <script>
        function markAllAsRead() {
            const route = '<?php echo e(auth()->user()->role === 'admin' ? route('admin.notifications.mark-all-read') : route('user.notifications.mark-all-read')); ?>';
            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        </script>
     <?php $__env->endSlot(); ?>
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
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/notifications/index.blade.php ENDPATH**/ ?>