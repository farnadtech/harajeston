

<?php $__env->startSection('title', 'جزئیات کاربر - ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.users.index')); ?>" class="text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined text-2xl">arrow_forward</span>
            </a>
            <div>
                <h2 class="text-2xl font-black text-gray-900">جزئیات کاربر</h2>
                <p class="text-sm text-gray-500 mt-1">مشاهده اطلاعات کامل کاربر</p>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-start gap-6">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-3xl">
                <?php echo e(mb_substr($user->name, 0, 2)); ?>

            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-2xl font-bold text-gray-900"><?php echo e($user->name); ?></h3>
                    <?php if($user->role === 'admin'): ?>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">ادمین</span>
                    <?php elseif($user->role === 'seller'): ?>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">فروشنده</span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">خریدار</span>
                    <?php endif; ?>
                    <?php if($user->email_verified_at): ?>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">تایید شده</span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">در انتظار تایید</span>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="material-symbols-outlined text-[20px]">email</span>
                        <span class="text-sm"><?php echo e($user->email); ?></span>
                    </div>
                    <?php if($user->phone): ?>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="material-symbols-outlined text-[20px]">phone</span>
                        <span class="text-sm"><?php echo e(\App\Services\PersianNumberService::convertToPersian($user->phone)); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                        <span class="text-sm">عضویت: <?php echo e(\App\Services\JalaliDateService::toJalali($user->created_at, 'Y/m/d')); ?></span>
                    </div>
                    <?php if($user->wallet): ?>
                    <div class="flex items-center gap-2 text-gray-600">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        <span class="text-sm font-mono">موجودی: <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($user->wallet->balance))); ?> تومان</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Adjustment Card -->
    <?php if($user->wallet): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">مدیریت کیف پول</h3>
                <p class="text-sm text-gray-500 mt-1">افزایش یا کاهش موجودی کیف پول کاربر</p>
            </div>
            <div class="text-left">
                <p class="text-xs text-gray-500">موجودی فعلی</p>
                <p class="text-2xl font-bold text-gray-900 font-mono">
                    <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($user->wallet->balance))); ?> تومان
                </p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.users.adjustWallet', $user)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع عملیات</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="add">افزایش موجودی</option>
                        <option value="subtract">کاهش موجودی</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ (تومان)</label>
                    <input type="number" name="amount" min="1" step="1" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="مبلغ را وارد کنید" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">دلیل تغییر</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="دلیل افزایش یا کاهش موجودی را وارد کنید (این متن در تاریخچه تراکنش‌های کاربر نمایش داده می‌شود)" required></textarea>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-yellow-600 mt-0.5">info</span>
                    <div class="text-sm text-yellow-800">
                        <p class="font-bold mb-1">توجه:</p>
                        <p>این تراکنش در تاریخچه کیف پول کاربر با دلیلی که وارد می‌کنید ثبت خواهد شد.</p>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold">
                <i class="fas fa-save ml-2"></i>
                اعمال تغییرات
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Stats Cards - Different for Buyer vs Seller -->
    <?php if($user->role === 'buyer'): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل پیشنهادها</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['total_bids'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 text-xl">gavel</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">مزایده برنده</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['won_auctions'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 text-xl">emoji_events</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل سفارشات</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['total_orders'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 text-xl">shopping_cart</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل خرید</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($stats['total_spent']))); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-xl">payments</span>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل آگهی‌ها</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['total_listings'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600 text-xl">inventory_2</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">آگهی فعال</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['active_listings'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل پیشنهادها</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['total_bids'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600 text-xl">gavel</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">مزایده برنده</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['won_auctions'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-600 text-xl">emoji_events</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل سفارشات</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian($stats['total_orders'])); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 text-xl">shopping_cart</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">کل خرید</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">
                        <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($stats['total_spent']))); ?>

                    </p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-xl">payments</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Store Info (if seller) -->
    <?php if($user->role === 'seller'): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">storefront</span>
            اطلاعات فروشگاه
        </h3>
        <?php if($user->store): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">نام فروشگاه</p>
                <p class="text-base font-medium text-gray-900 mt-1">
                    <?php echo e($user->store->name ?: 'فروشگاه ' . $user->name); ?>

                </p>
            </div>
            <?php if($user->store->description): ?>
            <div>
                <p class="text-sm text-gray-500">توضیحات</p>
                <p class="text-base text-gray-900 mt-1"><?php echo e($user->store->description); ?></p>
            </div>
            <?php endif; ?>
            <?php if($user->store->slug): ?>
            <div>
                <p class="text-sm text-gray-500">آدرس فروشگاه</p>
                <p class="text-base text-gray-900 mt-1">
                    <a href="<?php echo e(route('stores.show', $user->store->slug)); ?>" target="_blank" class="text-primary hover:underline">
                        <?php echo e($user->store->slug); ?>

                    </a>
                </p>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-yellow-600 mt-0.5">info</span>
                <div class="text-sm text-yellow-800">
                    <p class="font-bold mb-1">فروشگاه ایجاد نشده</p>
                    <p>این فروشنده هنوز فروشگاه خود را ایجاد نکرده است.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Recent Bids (for buyers) -->
    <?php if($user->role === 'buyer' && $user->bids->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">gavel</span>
            آخرین پیشنهادات
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">آگهی</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">مبلغ پیشنهاد</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">وضعیت</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $user->bids->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <a href="<?php echo e(route('listings.show', $bid->listing)); ?>" class="text-primary hover:underline">
                                <?php echo e($bid->listing->title); ?>

                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($bid->amount))); ?> تومان
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if($bid->listing->current_winner_id === $user->id && $bid->listing->status === 'completed'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">برنده</span>
                            <?php elseif($bid->listing->current_winner_id === $user->id): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">بالاترین پیشنهاد</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">پیشنهاد داده شده</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($bid->created_at, 'Y/m/d H:i')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Listings (for sellers) -->
    <?php if($user->role === 'seller' && $user->listings->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">inventory_2</span>
            آخرین آگهی‌ها
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">عنوان</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">نوع</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">وضعیت</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">قیمت</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $user->listings->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            <a href="<?php echo e(route('admin.listings.show', $listing)); ?>" class="text-primary hover:underline">
                                <?php echo e($listing->title); ?>

                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if($listing->type === 'auction'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">مزایده</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">فروش مستقیم</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm">
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
                        </td>
                        <td class="px-4 py-3 text-sm font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->starting_price))); ?> تومان
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($listing->created_at, 'Y/m/d')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Orders -->
    <?php if($user->orders->count() > 0): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined">shopping_cart</span>
            آخرین سفارشات
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">شماره سفارش</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">وضعیت</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">مبلغ کل</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $user->orders->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="text-primary hover:underline font-mono">
                                #<?php echo e(\App\Services\PersianNumberService::convertToPersian($order->id)); ?>

                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?php if($order->status === 'pending'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">در انتظار</span>
                            <?php elseif($order->status === 'processing'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">در حال پردازش</span>
                            <?php elseif($order->status === 'shipped'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">ارسال شده</span>
                            <?php elseif($order->status === 'delivered'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">تحویل داده شده</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">لغو شده</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-mono">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($order->total))); ?> تومان
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($order->created_at, 'Y/m/d')); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/users/show.blade.php ENDPATH**/ ?>