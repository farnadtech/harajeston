

<?php $__env->startSection('title', 'جزئیات آگهی - ' . $listing->title); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.listings.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined text-2xl">arrow_forward</span>
            </a>
            <div>
                <h2 class="text-2xl font-black text-gray-900">جزئیات آگهی</h2>
                <p class="text-sm text-gray-500 mt-1">مشاهده اطلاعات کامل آگهی</p>
            </div>
        </div>
        
        <a href="<?php echo e(route('admin.listings.manage', $listing)); ?>" 
           class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 flex items-center gap-2 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">settings</span>
            مدیریت پیشرفته
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Listing Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start gap-4 mb-6">
                    <?php if($listing->images->first()): ?>
                    <img src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>" 
                         alt="<?php echo e($listing->title); ?>"
                         class="w-32 h-32 rounded-lg object-cover">
                    <?php else: ?>
                    <div class="w-32 h-32 rounded-lg bg-gray-200 flex items-center justify-center">
                        <span class="material-symbols-outlined text-gray-400 text-4xl">image</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-xl font-bold text-gray-900"><?php echo e($listing->title); ?></h3>
                            <?php if($listing->status === 'active'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">فعال</span>
                            <?php elseif($listing->status === 'pending'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">در انتظار</span>
                            <?php elseif($listing->status === 'completed'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">تکمیل شده</span>
                            <?php elseif($listing->status === 'cancelled'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">لغو شده</span>
                            <?php elseif($listing->status === 'suspended'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">معلق</span>
                            <?php elseif($listing->status === 'ended'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">پایان یافته</span>
                            <?php elseif($listing->status === 'failed'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">ناموفق</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">نامشخص</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-600 mb-3"><?php echo e($listing->description); ?></p>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span>شناسه: <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->id)); ?></span>
                            <span>•</span>
                            <span><?php echo e(\App\Services\JalaliDateService::toJalali($listing->created_at, 'Y/m/d')); ?></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">نوع</p>
                        <p class="text-sm font-bold text-gray-900">
                            <?php if($listing->type === 'auction'): ?>
                                مزایده
                            <?php elseif($listing->type === 'direct_sale'): ?>
                                فروش مستقیم
                            <?php else: ?>
                                ترکیبی
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">قیمت پایه</p>
                        <p class="text-sm font-bold text-gray-900 font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->starting_price))); ?>

                        </p>
                    </div>
                    
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">قیمت فعلی</p>
                        <p class="text-sm font-bold text-primary font-mono">
                            <?php
                                $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
                                $currentPrice = $highestBid ? $highestBid->amount : $listing->starting_price;
                            ?>
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($currentPrice))); ?>

                        </p>
                    </div>
                    
                    <?php if($listing->buy_now_price): ?>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">خرید فوری</p>
                        <p class="text-sm font-bold text-gray-900 font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->buy_now_price))); ?>

                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Auction Details -->
            <?php if($listing->type === 'auction' || $listing->type === 'both'): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">gavel</span>
                    جزئیات مزایده
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">زمان شروع</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($listing->starts_at, 'Y/m/d H:i')); ?>

                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">زمان پایان</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($listing->ends_at, 'Y/m/d H:i')); ?>

                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">سپرده شرکت</p>
                        <p class="text-sm font-medium text-gray-900 font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->required_deposit))); ?> تومان
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">تعداد پیشنهادات</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids->count())); ?> پیشنهاد
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">تعداد شرکت‌کنندگان</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->participations->count())); ?> نفر
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">تمدید خودکار</p>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo e($listing->auto_extend ? 'فعال' : 'غیرفعال'); ?>

                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Bids List -->
            <?php if($listing->bids->count() > 0): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">format_list_bulleted</span>
                    لیست پیشنهادات
                </h3>
                
                <div class="space-y-2">
                    <?php $__currentLoopData = $listing->bids->sortByDesc('created_at')->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-3 <?php echo e($index === 0 ? 'bg-blue-50 border border-blue-100' : 'bg-gray-50'); ?> rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full <?php echo e($index === 0 ? 'bg-blue-200' : 'bg-gray-200'); ?> flex items-center justify-center text-sm font-bold <?php echo e($index === 0 ? 'text-blue-700' : 'text-gray-600'); ?>">
                                <?php echo e(mb_substr($bid->user->name, 0, 2)); ?>

                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($bid->user->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e(\App\Services\JalaliDateService::toJalali($bid->created_at, 'Y/m/d H:i')); ?></p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold <?php echo e($index === 0 ? 'text-primary' : 'text-gray-900'); ?> font-mono">
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($bid->amount))); ?>

                            </p>
                            <p class="text-xs text-gray-500">تومان</p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Seller Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">storefront</span>
                    فروشنده
                </h3>
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold">
                        <?php echo e(mb_substr($listing->seller->name, 0, 2)); ?>

                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900"><?php echo e($listing->seller->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($listing->store->name); ?></p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ایمیل:</span>
                        <span class="text-gray-900"><?php echo e($listing->seller->email); ?></span>
                    </div>
                    <?php if($listing->seller->phone): ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500">تلفن:</span>
                        <span class="text-gray-900 font-mono"><?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->seller->phone)); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span class="text-gray-500">عضویت:</span>
                        <span class="text-gray-900"><?php echo e(\App\Services\JalaliDateService::toJalali($listing->seller->created_at, 'Y/m/d')); ?></span>
                    </div>
                </div>
                
                <a href="<?php echo e(route('admin.users.show', $listing->seller)); ?>" 
                   class="mt-4 block text-center py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    مشاهده پروفایل
                </a>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">analytics</span>
                    آمار
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-700">بازدیدها</span>
                        <span class="text-lg font-bold text-blue-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->views)); ?>

                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-700">علاقه‌مندی‌ها</span>
                        <span class="text-lg font-bold text-purple-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->favorites)); ?>

                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-700">اشتراک‌گذاری</span>
                        <span class="text-lg font-bold text-green-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->shares)); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">عملیات سریع</h3>
                
                <div class="space-y-2">
                    <?php if($listing->status === 'pending'): ?>
                    <button onclick="activateListing()" 
                            class="w-full py-2 px-4 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        تایید و فعال‌سازی
                    </button>
                    <?php endif; ?>
                    
                    <?php if($listing->status === 'active'): ?>
                    <button onclick="suspendListing()" 
                            class="w-full py-2 px-4 bg-orange-50 hover:bg-orange-100 text-orange-700 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">block</span>
                        تعلیق آگهی
                    </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('listings.show', $listing)); ?>" 
                       target="_blank"
                       class="w-full py-2 px-4 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                        مشاهده در سایت
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function activateListing() {
    if (confirm('آیا از فعال‌سازی این آگهی اطمینان دارید؟')) {
        fetch(`/admin/listings/<?php echo e($listing->id); ?>/activate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function suspendListing() {
    const reason = prompt('لطفاً دلیل تعلیق را وارد کنید:');
    if (reason) {
        fetch(`/admin/listings/<?php echo e($listing->id); ?>/suspend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/listings/show.blade.php ENDPATH**/ ?>