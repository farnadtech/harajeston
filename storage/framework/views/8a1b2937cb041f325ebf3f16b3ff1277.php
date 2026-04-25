

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
                <p class="text-sm text-gray-500 font-medium">درآمد ماه جاری</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">
                    <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($stats['total_sales'], true); ?>
                    <span class="text-xs font-normal text-gray-400">تومان</span>
                </h3>
                <p class="text-xs <?php echo e($stats['sales_growth'] >= 0 ? 'text-green-500' : 'text-red-500'); ?> flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]"><?php echo e($stats['sales_growth'] >= 0 ? 'trending_up' : 'trending_down'); ?></span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(abs($stats['sales_growth'])); ?>٪ <?php echo e($stats['sales_growth'] >= 0 ? 'رشد' : 'کاهش'); ?> نسبت به ماه قبل
                </p>
                <p class="text-xs text-gray-400 mt-0.5">ماه قبل: <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($stats['prev_month_sales'], true); ?> تومان</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">مزایده‌های فعال</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_auctions']); ?></h3>
                <p class="text-xs <?php echo e($stats['auctions_growth'] >= 0 ? 'text-green-500' : 'text-red-500'); ?> flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]"><?php echo e($stats['auctions_growth'] >= 0 ? 'trending_up' : 'trending_down'); ?></span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(abs($stats['auctions_growth'])); ?>٪ <?php echo e($stats['auctions_growth'] >= 0 ? 'افزایش' : 'کاهش'); ?>

                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">کل کاربران</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_users']); ?></h3>
                <p class="text-xs <?php echo e($stats['users_growth'] >= 0 ? 'text-green-500' : 'text-red-500'); ?> flex items-center gap-1 mt-1 font-bold">
                    <span class="material-symbols-outlined text-[14px]"><?php echo e($stats['users_growth'] >= 0 ? 'trending_up' : 'trending_down'); ?></span>
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(abs($stats['users_growth'])); ?>٪ <?php echo e($stats['users_growth'] >= 0 ? 'رشد ماهانه' : 'کاهش ماهانه'); ?>

                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <span class="material-symbols-outlined">verified</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">در انتظار تایید</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['pending_approvals']); ?></h3>
                <p class="text-xs text-gray-400 mt-1">
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['pending_sellers']); ?> فروشنده / <?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['pending_listings']); ?> کالا
                </p>
            </div>
        </div>
    </div>

    <!-- Chart and Pending Sellers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Activity Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">مزایده‌های فعال</h3>
                    <p class="text-xs text-gray-400 mt-0.5">تعداد مزایده‌های فعال در ۷ روز گذشته</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="w-3 h-3 rounded-full bg-primary inline-block"></span>
                    مزایده فعال
                </div>
            </div>
            <div class="w-full h-64">
                <canvas id="auctionChart"></canvas>
            </div>
        </div>

        <!-- Pending Sellers Approval -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">تایید فروشندگان</h3>
                <a class="text-sm text-primary font-bold hover:underline" href="<?php echo e(route('admin.sellers.index', ['status' => 'pending'])); ?>">مشاهده همه</a>
            </div>
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                <?php $__empty_1 = true; $__currentLoopData = $pendingSellers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-primary font-bold">
                            <?php echo e(mb_substr($seller->name, 0, 2)); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="<?php echo e(route('admin.sellers.show', $seller)); ?>" class="text-sm font-bold text-gray-900 truncate hover:text-primary transition-colors block"><?php echo e($seller->name); ?></a>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

// Auction Chart
(function() {
    const labels = <?php echo json_encode($chartLabels, 15, 512) ?>;
    const data   = <?php echo json_encode($chartData, 15, 512) ?>;

    const ctx = document.getElementById('auctionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'مزایده فعال',
                data: data,
                borderColor: '#135bec',
                backgroundColor: 'rgba(19,91,236,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#135bec',
                pointBorderWidth: 2,
                pointRadius: 5,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y + ' مزایده فعال',
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Tahoma', size: 11 }, color: '#9ca3af' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: {
                        font: { family: 'Tahoma', size: 11 },
                        color: '#9ca3af',
                        stepSize: 1,
                        precision: 0,
                    }
                }
            }
        }
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>