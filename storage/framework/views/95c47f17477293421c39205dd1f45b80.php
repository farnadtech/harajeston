

<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900">مدیریت دسته‌بندی‌ها</h2>
            <p class="text-sm text-gray-500 mt-1">برای تغییر ترتیب، دسته‌ها را بکشید - برای مشاهده زیردسته‌ها کلیک کنید</p>
        </div>
        <a href="<?php echo e(route('admin.categories.create')); ?>" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 flex items-center gap-2 text-sm font-medium transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span>
            افزودن دسته‌بندی
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- Categories List -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div id="sortable-categories" class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="category-group" data-id="<?php echo e($category->id); ?>">
                <!-- Parent Category -->
                <div class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-gray-400 cursor-grab active:cursor-grabbing drag-handle">
                        drag_indicator
                    </span>
                    
                    <?php if($category->children->count() > 0): ?>
                    <button onclick="toggleChildren(<?php echo e($category->id); ?>)" class="p-1 hover:bg-gray-200 rounded transition-colors">
                        <span class="material-symbols-outlined text-gray-600 expand-icon" id="icon-<?php echo e($category->id); ?>">
                            chevron_left
                        </span>
                    </button>
                    <?php else: ?>
                    <span class="w-8"></span>
                    <?php endif; ?>
                    
                    <?php if($category->icon): ?>
                        <span class="material-symbols-outlined text-gray-600"><?php echo e($category->icon); ?></span>
                    <?php endif; ?>
                    
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900"><?php echo e($category->name); ?></h3>
                        <p class="text-xs text-gray-500">
                            <?php echo app(\App\Services\PersianNumberService::class)->toPersian($category->listings()->count()); ?> حراجی
                            <?php if($category->children->count() > 0): ?>
                                • <?php echo app(\App\Services\PersianNumberService::class)->toPersian($category->children->count()); ?> زیردسته
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <?php if($category->is_active): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            فعال
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            غیرفعال
                        </span>
                    <?php endif; ?>
                    
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('admin.categories.edit', $category)); ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </a>
                        <form action="<?php echo e(route('admin.categories.destroy', $category)); ?>" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Children Categories -->
                <?php if($category->children->count() > 0): ?>
                <div id="children-<?php echo e($category->id); ?>" class="hidden bg-gray-50 border-t border-gray-200">
                    <?php $__currentLoopData = $category->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center gap-3 p-4 pr-16 hover:bg-gray-100 transition-colors">
                            <?php if($child->children->count() > 0): ?>
                            <button onclick="toggleGrandchildren(<?php echo e($child->id); ?>)" class="p-1 hover:bg-gray-200 rounded transition-colors">
                                <span class="material-symbols-outlined text-gray-600 text-sm expand-icon" id="icon-grand-<?php echo e($child->id); ?>">
                                    chevron_left
                                </span>
                            </button>
                            <?php else: ?>
                            <span class="text-gray-400">└─</span>
                            <?php endif; ?>
                            
                            <?php if($child->icon): ?>
                                <span class="material-symbols-outlined text-gray-500 text-[20px]"><?php echo e($child->icon); ?></span>
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800"><?php echo e($child->name); ?></h4>
                                <p class="text-xs text-gray-500">
                                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian($child->listings()->count()); ?> حراجی
                                    <?php if($child->children->count() > 0): ?>
                                        • <?php echo app(\App\Services\PersianNumberService::class)->toPersian($child->children->count()); ?> زیردسته
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <?php if($child->is_active): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    فعال
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    غیرفعال
                                </span>
                            <?php endif; ?>
                            
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('admin.category-attributes.index', $child)); ?>" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="مدیریت ویژگی‌ها">
                                    <span class="material-symbols-outlined text-[18px]">tune</span>
                                </a>
                                <a href="<?php echo e(route('admin.categories.edit', $child)); ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <form action="<?php echo e(route('admin.categories.destroy', $child)); ?>" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این زیردسته مطمئن هستید؟')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Grandchildren (Level 3) -->
                        <?php if($child->children->count() > 0): ?>
                        <div id="grandchildren-<?php echo e($child->id); ?>" class="hidden bg-gray-100">
                            <?php $__currentLoopData = $child->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 p-4 pr-24 hover:bg-gray-200 transition-colors border-b border-gray-300 last:border-b-0">
                                <span class="text-gray-400">└──</span>
                                
                                <?php if($grandchild->icon): ?>
                                    <span class="material-symbols-outlined text-gray-500 text-[18px]"><?php echo e($grandchild->icon); ?></span>
                                <?php endif; ?>
                                
                                <div class="flex-1">
                                    <h5 class="font-medium text-gray-700 text-sm"><?php echo e($grandchild->name); ?></h5>
                                    <p class="text-xs text-gray-500"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($grandchild->listings()->count()); ?> حراجی</p>
                                </div>
                                
                                <?php if($grandchild->is_active): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        فعال
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        غیرفعال
                                    </span>
                                <?php endif; ?>
                                
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('admin.category-attributes.index', $grandchild)); ?>" class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="مدیریت ویژگی‌ها">
                                        <span class="material-symbols-outlined text-[18px]">tune</span>
                                    </a>
                                    <a href="<?php echo e(route('admin.categories.edit', $grandchild)); ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form action="<?php echo e(route('admin.categories.destroy', $grandchild)); ?>" method="POST" class="inline" onsubmit="return confirm('آیا از حذف این دسته مطمئن هستید؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-gray-400 text-5xl">category</span>
                    <p class="text-gray-500">دسته‌بندی‌ای یافت نشد</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Toggle children visibility
function toggleChildren(categoryId) {
    const childrenDiv = document.getElementById('children-' + categoryId);
    const icon = document.getElementById('icon-' + categoryId);
    
    if (childrenDiv.classList.contains('hidden')) {
        childrenDiv.classList.remove('hidden');
        icon.textContent = 'expand_more';
    } else {
        childrenDiv.classList.add('hidden');
        icon.textContent = 'chevron_left';
    }
}

// Toggle grandchildren visibility
function toggleGrandchildren(categoryId) {
    const grandchildrenDiv = document.getElementById('grandchildren-' + categoryId);
    const icon = document.getElementById('icon-grand-' + categoryId);
    
    if (grandchildrenDiv.classList.contains('hidden')) {
        grandchildrenDiv.classList.remove('hidden');
        icon.textContent = 'expand_more';
    } else {
        grandchildrenDiv.classList.add('hidden');
        icon.textContent = 'chevron_left';
    }
}

// Sortable for parent categories
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sortable-categories');
    if (!container) return;

    new Sortable(container, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'bg-blue-50',
        onEnd: function(evt) {
            const groups = Array.from(container.querySelectorAll('.category-group'));
            const order = groups.map((group, index) => ({
                id: group.dataset.id,
                order: index + 1
            }));

            // ارسال ترتیب جدید به سرور
            fetch('<?php echo e(route("admin.categories.reorder")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({ order: order })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('ترتیب با موفقیت ذخیره شد', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('خطا در ذخیره ترتیب', 'error');
            });
        }
    });
});

function showNotification(message, type) {
    const alert = document.createElement('div');
    alert.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-4 py-3 rounded-lg z-50 ${
        type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
    }`;
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>