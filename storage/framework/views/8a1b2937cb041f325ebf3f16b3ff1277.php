

<?php $__env->startSection('title', 'داشبورد مدیریت'); ?>
<?php $__env->startSection('page-title', 'داشبورد'); ?>
<?php $__env->startSection('header-title', 'خوش آمدید، ادمین عزیز 👋'); ?>
<?php $__env->startSection('header-subtitle', 'گزارش کلی وضعیت بازار امروز'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">attach_money</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">فروش کل</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">
                    <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($stats['total_sales'] ?? 2500000000, true); ?>
                    <span class="text-xs font-normal text-gray-400">تومان</span>
                </h3>
                <p class="text-xs text-green-500 flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(12); ?>٪ رشد هفتگی
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">مزایده‌های فعال</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_auctions'] ?? 1240); ?></h3>
                <p class="text-xs text-green-500 flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(5); ?>٪ افزایش
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">کاربران فعال</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_users'] ?? 15800); ?></h3>
                <p class="text-xs text-red-500 flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]">trending_down</span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(1); ?>٪ کاهش
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">در انتظار تایید</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['pending_approvals'] ?? 45); ?></h3>
                <p class="text-xs text-gray-400 mt-1">فروشنده و کالا</p>
            </div>
        </div>
    </div>

    <!-- Chart and Pending Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Activity Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">نمودار فعالیت هفتگی</h3>
                <select class="bg-gray-50 border-gray-200 text-sm rounded-lg focus:ring-primary focus:border-primary py-1 px-3">
                    <option>۷ روز گذشته</option>
                    <option>۳۰ روز گذشته</option>
                    <option>امسال</option>
                </select>
            </div>
            <div class="w-full h-64 relative">
                <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 800 300">
                    <g class="chart-grid text-gray-200">
                        <line stroke="#e5e7eb" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="250" y2="250"></line>
                        <line stroke="#e5e7eb" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="200" y2="200"></line>
                        <line stroke="#e5e7eb" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="150" y2="150"></line>
                        <line stroke="#e5e7eb" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="100" y2="100"></line>
                        <line stroke="#e5e7eb" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="50" y2="50"></line>
                    </g>
                    <path d="M0,250 L0,220 C100,200 150,150 200,180 C250,210 300,120 400,100 C500,80 550,160 600,130 C650,100 700,50 800,20 L800,250 Z" fill="url(#gradient)" opacity="0.1"></path>
                    <path d="M0,220 C100,200 150,150 200,180 C250,210 300,120 400,100 C500,80 550,160 600,130 C650,100 700,50 800,20" fill="none" stroke="#135bec" stroke-linecap="round" stroke-width="3"></path>
                    <circle cx="200" cy="180" fill="white" r="4" stroke="#135bec" stroke-width="2"></circle>
                    <circle cx="400" cy="100" fill="white" r="4" stroke="#135bec" stroke-width="2"></circle>
                    <circle cx="600" cy="130" fill="white" r="4" stroke="#135bec" stroke-width="2"></circle>
                    <defs>
                        <linearGradient id="gradient" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#135bec;stop-opacity:1"></stop>
                            <stop offset="100%" style="stop-color:#135bec;stop-opacity:0"></stop>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>شنبه</span>
                    <span>یکشنبه</span>
                    <span>دوشنبه</span>
                    <span>سه‌شنبه</span>
                    <span>چهارشنبه</span>
                    <span>پنجشنبه</span>
                    <span>جمعه</span>
                </div>
            </div>
        </div>

        <!-- Pending Sellers Approval -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">تایید فروشندگان</h3>
                <a class="text-sm text-primary font-bold hover:underline" href="#">مشاهده همه</a>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                <?php $__empty_1 = true; $__currentLoopData = $pendingSellers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-primary font-bold">
                            <?php echo e(mb_substr($seller->name, 0, 2)); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate"><?php echo e($seller->name); ?></h4>
                            <p class="text-xs text-gray-500 truncate"><?php echo e($seller->store->store_name ?? 'فروشگاه'); ?></p>
                        </div>
                        <div class="flex gap-1">
                            <form method="POST" action="<?php echo e(route('admin.sellers.approve', $seller)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="p-1.5 text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors" title="تایید فروشنده">
                                    <span class="material-symbols-outlined text-lg">check</span>
                                </button>
                            </form>
                            <button type="button" onclick="showRejectModal(<?php echo e($seller->id); ?>, '<?php echo e($seller->name); ?>')" class="p-1.5 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors" title="رد درخواست">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-5xl mb-2">check_circle</span>
                        <p class="text-sm">همه فروشندگان تایید شده‌اند</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Listings Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">آخرین مزایده‌ها</h3>
                <p class="text-sm text-gray-500 mt-1">لیست ۱۰ مزایده آخر ثبت شده در سیستم</p>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">فیلترها</button>
                <button class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">خروجی اکسل</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">نام محصول</th>
                        <th class="px-6 py-4">فروشنده</th>
                        <th class="px-6 py-4">آخرین پیشنهاد</th>
                        <th class="px-6 py-4 text-center">وضعیت</th>
                        <th class="px-6 py-4 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentListings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        <?php if($listing->images->count() > 0): ?>
                                            <img alt="<?php echo e($listing->title); ?>" class="w-full h-full object-cover" src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"/>
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center">
                                                <span class="material-symbols-outlined text-gray-400">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900"><?php echo e($listing->title); ?></p>
                                        <p class="text-xs text-gray-500">شناسه: #<?php echo app(\App\Services\PersianNumberService::class)->toPersian($listing->id); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700"><?php echo e($listing->seller->store->store_name ?? $listing->seller->name); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">
                                    <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($listing->current_price ?? $listing->starting_price, true); ?>
                                    <span class="text-xs font-normal text-gray-500">تومان</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($listing->status === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">فعال</span>
                                <?php elseif($listing->status === 'pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">در انتظار تایید</span>
                                <?php elseif($listing->status === 'ended'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">پایان یافته</span>
                                <?php elseif($listing->status === 'completed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">تکمیل شده</span>
                                <?php elseif($listing->status === 'suspended'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">معلق شده</span>
                                <?php elseif($listing->status === 'cancelled'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">لغو شده</span>
                                <?php elseif($listing->status === 'failed'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">ناموفق</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">نامشخص</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('admin.listings.show', $listing)); ?>" class="p-1.5 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                    <a href="<?php echo e(route('admin.listings.edit', $listing)); ?>" class="p-1.5 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.listings.destroy', $listing)); ?>" class="inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <span class="material-symbols-outlined text-5xl mb-2">inbox</span>
                                <p>هیچ مزایده‌ای یافت نشد</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(isset($recentListings) && $recentListings->count() > 0): ?>
            <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-500">نمایش <?php echo app(\App\Services\PersianNumberService::class)->toPersian($recentListings->firstItem() ?? 1); ?> تا <?php echo app(\App\Services\PersianNumberService::class)->toPersian($recentListings->lastItem() ?? 10); ?> از <?php echo app(\App\Services\PersianNumberService::class)->toPersian($recentListings->total()); ?> مورد</span>
                <div class="flex gap-1">
                    <?php if($recentListings->onFirstPage()): ?>
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-300">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($recentListings->previousPageUrl()); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php $__currentLoopData = $recentListings->getUrlRange(1, $recentListings->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $recentListings->currentPage()): ?>
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-medium text-sm"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 text-sm"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($recentListings->hasMorePages()): ?>
                        <a href="<?php echo e($recentListings->nextPageUrl()); ?>" class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </a>
                    <?php else: ?>
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-300">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Seller Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">رد درخواست فروشندگی</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="rejectForm" method="POST" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-3">آیا از رد درخواست فروشندگی <span id="sellerName" class="font-bold"></span> مطمئن هستید؟</p>
                <label class="block text-sm font-medium text-gray-700 mb-2">دلیل رد درخواست:</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary" placeholder="لطفا دلیل رد درخواست را وارد کنید..."></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">انصراف</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">رد درخواست</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(sellerId, sellerName) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const nameSpan = document.getElementById('sellerName');
    
    form.action = `/haraj/public/admin/sellers/${sellerId}/reject`;
    nameSpan.textContent = sellerName;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}

// Close modal on outside click
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>