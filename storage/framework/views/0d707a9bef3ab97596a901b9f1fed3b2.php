

<?php $__env->startSection('title', 'داشبورد فروشنده'); ?>

<?php $__env->startSection('page-title', 'خوش آمدید، ' . (auth()->user()->store->store_name ?? auth()->user()->name) . ' 👋'); ?>
<?php $__env->startSection('page-subtitle', 'خلاصه وضعیت فروشگاه شما امروز'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined">payments</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">درآمد کل</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">
                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($stats['total_sales'] ?? 0)); ?>
                    <span class="text-xs font-normal text-gray-400">تومان</span>
                </h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-50 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">مزایده‌های فعال</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_auctions'] ?? 0); ?></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <span class="material-symbols-outlined">pending</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">در انتظار تایید</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['pending_listings'] ?? 0); ?></h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">تکمیل شده</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['completed_auctions'] ?? 0); ?></h3>
            </div>
        </div>
    </div>

    <!-- Sales Chart & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">نمودار فروش هفتگی</h3>
                <p class="text-sm text-gray-500 mt-1">آمار فروش ۷ روز اخیر</p>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-end justify-between gap-2">
                    <?php
                        $maxSales = 1000000;
                        $weekDays = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
                        $salesData = [300000, 450000, 200000, 600000, 800000, 350000, 500000];
                    ?>
                    <?php $__currentLoopData = $salesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-primary/10 rounded-t-lg relative group cursor-pointer hover:bg-primary/20 transition-colors" style="height: <?php echo e(($sale / $maxSales) * 100); ?>%">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($sale)); ?> تومان
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 font-medium"><?php echo e($weekDays[$index]); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">فعالیت‌های اخیر</h3>
                <p class="text-sm text-gray-500 mt-1">آخرین رویدادها</p>
            </div>
            <div class="p-4 space-y-4 max-h-80 overflow-y-auto">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-xl">shopping_bag</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">فروش جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۳۴ تکمیل شد</p>
                        <p class="text-xs text-gray-400 mt-1">۲ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-xl">gavel</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">پیشنهاد جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">پیشنهاد ۵۰۰,۰۰۰ تومان</p>
                        <p class="text-xs text-gray-400 mt-1">۳ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-orange-600 text-xl">local_shipping</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">ارسال محصول</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۳۰ ارسال شد</p>
                        <p class="text-xs text-gray-400 mt-1">۵ ساعت پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-xl">shopping_bag</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">فروش جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">سفارش #۱۲۲۸ تکمیل شد</p>
                        <p class="text-xs text-gray-400 mt-1">۱ روز پیش</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-blue-600 text-xl">gavel</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">پیشنهاد جدید</p>
                        <p class="text-xs text-gray-500 mt-0.5">پیشنهاد ۷۵۰,۰۰۰ تومان</p>
                        <p class="text-xs text-gray-400 mt-1">۱ روز پیش</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Listings Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">مزایده‌های فعال من</h3>
                <p class="text-sm text-gray-500 mt-1">لیست مزایده‌های در حال برگزاری فروشگاه شما</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('listings.create')); ?>" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20">
                    افزودن مزایده
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">محصول</th>
                        <th class="px-6 py-4">قیمت فعلی</th>
                        <th class="px-6 py-4">زمان باقی‌مانده</th>
                        <th class="px-6 py-4 text-center">وضعیت</th>
                        <th class="px-6 py-4 text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $activeListings ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if($listing->images->count() > 0): ?>
                                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                            <img alt="<?php echo e($listing->title); ?>" class="w-full h-full object-cover" src="<?php echo e(url('storage/' . $listing->images->first()->file_path)); ?>"/>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-gray-400">image</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900"><?php echo e($listing->title); ?></p>
                                        <p class="text-xs text-gray-500">شروع: <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($listing->starting_price)); ?> تومان</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">
                                    <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($listing->current_price)); ?>
                                    <span class="text-xs font-normal text-gray-500">تومان</span>
                                </div>
                                <?php if($listing->current_price > $listing->starting_price): ?>
                                    <div class="text-xs text-green-500 mt-0.5">
                                        +<?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format((($listing->current_price - $listing->starting_price) / $listing->starting_price) * 100, 0)); ?>٪
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($listing->ends_at > now()): ?>
                                    <span class="text-sm font-medium text-gray-700">
                                        <?php echo e($listing->ends_at->diffForHumans()); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-sm font-medium text-red-600">پایان یافته</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($listing->status === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        در جریان
                                    </span>
                                <?php elseif($listing->status === 'pending'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        در انتظار تایید
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <?php echo e($listing->status); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('listings.show', $listing)); ?>" class="p-1.5 text-gray-500 hover:text-primary hover:bg-blue-50 rounded-lg transition-colors" title="مشاهده">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="material-symbols-outlined text-5xl text-gray-300">inventory_2</span>
                                    <p class="text-gray-500">هیچ مزایده فعالی وجود ندارد</p>
                                    <a href="<?php echo e(route('listings.create')); ?>" class="mt-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-blue-600 transition-colors">
                                        ایجاد اولین مزایده
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Orders -->
    <?php if(isset($recentOrders) && count($recentOrders) > 0): ?>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">سفارشات اخیر</h3>
            <p class="text-sm text-gray-500 mt-1">آخرین سفارشات دریافت شده</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4">شماره سفارش</th>
                        <th class="px-6 py-4">خریدار</th>
                        <th class="px-6 py-4">مبلغ</th>
                        <th class="px-6 py-4">وضعیت</th>
                        <th class="px-6 py-4">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-sm"><?php echo e($order->order_number); ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo e($order->buyer->name); ?></td>
                            <td class="px-6 py-4 text-sm font-bold"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($order->total)); ?> تومان</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo e($order->status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($order->created_at->diffForHumans()); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/dashboard/seller.blade.php ENDPATH**/ ?>