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
     <?php $__env->slot('title', null, []); ?> پیشنهادات من <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> مزایده‌هایی که شرکت کرده‌ام <?php $__env->endSlot(); ?>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="flex items-center gap-2 p-2">
            <a href="<?php echo e(route('my-bids', ['status' => 'all'])); ?>" 
               class="px-6 py-3 rounded-xl font-medium transition-colors <?php echo e(request('status', 'all') === 'all' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                همه (<?php echo e(\App\Services\PersianNumberService::convertToPersian($counts['all'])); ?>)
            </a>
            <a href="<?php echo e(route('my-bids', ['status' => 'active'])); ?>" 
               class="px-6 py-3 rounded-xl font-medium transition-colors <?php echo e(request('status') === 'active' ? 'bg-green-500 text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                فعال (<?php echo e(\App\Services\PersianNumberService::convertToPersian($counts['active'])); ?>)
            </a>
            <a href="<?php echo e(route('my-bids', ['status' => 'completed'])); ?>" 
               class="px-6 py-3 rounded-xl font-medium transition-colors <?php echo e(request('status') === 'completed' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-gray-50'); ?>">
                تمام شده (<?php echo e(\App\Services\PersianNumberService::convertToPersian($counts['completed'])); ?>)
            </a>
        </div>
    </div>

    <!-- Listings Grid -->
    <?php if($listings->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-100">
                        <?php if($listing->images->count() > 0): ?>
                            <img src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>" alt="<?php echo e($listing->title); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-gray-300 text-6xl">image</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Status Badge -->
                        <?php
                            $statusColors = [
                                'active' => 'bg-green-500',
                                'pending' => 'bg-yellow-500',
                                'completed' => 'bg-blue-500',
                                'cancelled' => 'bg-red-500',
                            ];
                            $statusLabels = [
                                'active' => 'فعال',
                                'pending' => 'در انتظار',
                                'completed' => 'تمام شده',
                                'cancelled' => 'لغو شده',
                            ];
                        ?>
                        <span class="absolute top-3 right-3 px-3 py-1 <?php echo e($statusColors[$listing->status] ?? 'bg-gray-500'); ?> text-white text-xs font-bold rounded-full">
                            <?php echo e($statusLabels[$listing->status] ?? $listing->status); ?>

                        </span>

                        <!-- Winner Badge -->
                        <?php if($listing->status === 'completed' && $listing->current_winner_id === auth()->id()): ?>
                            <span class="absolute top-3 left-3 px-3 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">emoji_events</span>
                                برنده
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2"><?php echo e($listing->title); ?></h3>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <span class="material-symbols-outlined text-lg">store</span>
                            <span><?php echo e($listing->seller->name); ?></span>
                        </div>

                        <!-- My Bid Info -->
                        <?php if($listing->my_bid): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600">پیشنهاد من:</span>
                                    <span class="text-sm font-bold text-blue-600"><?php echo app(\App\Services\PersianNumberService::class)->formatNumber($listing->my_bid->amount, true); ?> تومان</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600">بالاترین پیشنهاد:</span>
                                    <span class="text-sm font-bold text-gray-900"><?php echo app(\App\Services\PersianNumberService::class)->formatNumber($listing->current_price, true); ?> تومان</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Price & Bids -->
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs text-gray-500">قیمت فعلی</p>
                                <p class="text-lg font-bold text-primary"><?php echo app(\App\Services\PersianNumberService::class)->formatNumber($listing->current_price, true); ?> تومان</p>
                            </div>
                            <div class="text-left">
                                <p class="text-xs text-gray-500">تعداد پیشنهادات</p>
                                <p class="text-lg font-bold text-gray-900"><?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids_count)); ?></p>
                            </div>
                        </div>

                        <!-- Time Remaining -->
                        <?php if($listing->status === 'active' && $listing->ends_at): ?>
                            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-orange-500">schedule</span>
                                    <span class="text-gray-600">زمان باقی‌مانده:</span>
                                    <?php
                                        try {
                                            $now = now();
                                            $endTime = \Carbon\Carbon::parse($listing->ends_at);
                                            if ($endTime->isFuture()) {
                                                $diff = $now->diff($endTime);
                                                $timeRemaining = '';
                                                if ($diff->days > 0) {
                                                    $timeRemaining .= \App\Services\PersianNumberService::convertToPersian($diff->days) . ' روز ';
                                                }
                                                if ($diff->h > 0) {
                                                    $timeRemaining .= \App\Services\PersianNumberService::convertToPersian($diff->h) . ' ساعت ';
                                                }
                                                if ($diff->i > 0) {
                                                    $timeRemaining .= \App\Services\PersianNumberService::convertToPersian($diff->i) . ' دقیقه';
                                                }
                                                if (empty($timeRemaining)) {
                                                    $timeRemaining = 'کمتر از یک دقیقه';
                                                }
                                            } else {
                                                $timeRemaining = 'پایان یافته';
                                            }
                                        } catch (\Exception $e) {
                                            $timeRemaining = 'نامشخص';
                                        }
                                    ?>
                                    <span class="font-bold text-gray-900"><?php echo e($timeRemaining); ?></span>
                                </div>
                            </div>
                        <?php elseif($listing->status === 'completed'): ?>
                            <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-gray-500">check_circle</span>
                                    <span class="text-gray-600 font-bold">حراجی به پایان رسیده است</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Action Button -->
                        <a href="<?php echo e(route('listings.show', $listing)); ?>" 
                           class="block w-full bg-primary text-white text-center py-3 rounded-xl hover:bg-blue-700 transition-colors font-medium">
                            مشاهده جزئیات
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <?php if($listings->hasPages()): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <?php echo e($listings->links('vendor.pagination.custom')); ?>

            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <span class="material-symbols-outlined text-gray-300 text-6xl mb-4 block">gavel</span>
            <h3 class="text-xl font-bold text-gray-900 mb-2">هیچ مزایده‌ای یافت نشد</h3>
            <p class="text-gray-500 mb-6">شما هنوز در هیچ مزایده‌ای شرکت نکرده‌اید</p>
            <a href="<?php echo e(route('listings.index')); ?>" class="inline-block bg-primary text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition-colors font-medium">
                مشاهده مزایده‌های فعال
            </a>
        </div>
    <?php endif; ?>
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
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/listings/my-bids.blade.php ENDPATH**/ ?>