

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900">جزئیات فروشنده</h1>
            <p class="text-gray-600 mt-1"><?php echo e($seller->name); ?></p>
        </div>
        <a href="<?php echo e(route('admin.sellers.index')); ?>" class="text-gray-600 hover:text-gray-900">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- اطلاعات اصلی -->
        <div class="lg:col-span-2 space-y-6">
            <!-- وضعیت -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">وضعیت فروشندگی</h2>
                
                <div class="flex items-center gap-4 mb-6">
                    <?php if($seller->seller_status === 'pending'): ?>
                        <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 px-4 py-2 rounded-lg">
                            <span class="material-symbols-outlined">schedule</span>
                            در انتظار تایید
                        </span>
                    <?php elseif($seller->seller_status === 'active'): ?>
                        <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-lg">
                            <span class="material-symbols-outlined">check_circle</span>
                            فعال
                        </span>
                    <?php elseif($seller->seller_status === 'suspended'): ?>
                        <span class="inline-flex items-center gap-2 bg-red-100 text-red-800 px-4 py-2 rounded-lg">
                            <span class="material-symbols-outlined">block</span>
                            تعلیق شده
                        </span>
                    <?php elseif($seller->seller_status === 'rejected'): ?>
                        <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-800 px-4 py-2 rounded-lg">
                            <span class="material-symbols-outlined">cancel</span>
                            رد شده
                        </span>
                    <?php endif; ?>
                </div>

                <?php if($seller->seller_rejection_reason): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm font-bold text-red-900 mb-1">دلیل رد/تعلیق:</p>
                        <p class="text-sm text-red-800"><?php echo e($seller->seller_rejection_reason); ?></p>
                    </div>
                <?php endif; ?>

                <!-- دکمه‌های عملیات -->
                <div class="flex flex-wrap gap-2">
                    <?php if($seller->seller_status === 'pending'): ?>
                        <button onclick="showApproveModal()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check</span>
                            تایید
                        </button>

                        <button onclick="showRejectModal()" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">close</span>
                            رد کردن
                        </button>
                    <?php endif; ?>

                    <?php if($seller->seller_status === 'active'): ?>
                        <button onclick="showSuspendModal()" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">block</span>
                            تعلیق
                        </button>
                    <?php endif; ?>

                    <?php if(in_array($seller->seller_status, ['suspended', 'rejected'])): ?>
                        <button onclick="showActivateModal()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            فعال‌سازی
                        </button>
                    <?php endif; ?>

                    <button onclick="showDeleteModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">person_remove</span>
                        تبدیل به خریدار
                    </button>
                </div>
            </div>

            <!-- اطلاعات درخواست -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">اطلاعات درخواست</h2>
                
                <?php if($seller->seller_request_data): ?>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">نام فروشگاه:</span>
                            <span class="font-bold"><?php echo e($seller->seller_request_data['store_name'] ?? '-'); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">شماره تماس:</span>
                            <span class="font-bold"><?php echo e($seller->seller_request_data['phone'] ?? '-'); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">کد ملی:</span>
                            <span class="font-bold"><?php echo e($seller->seller_request_data['national_id'] ?? '-'); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">بانک:</span>
                            <span class="font-bold"><?php echo e($seller->seller_request_data['bank_name'] ?? '-'); ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-600">شماره حساب:</span>
                            <span class="font-bold text-sm"><?php echo e($seller->seller_request_data['bank_account'] ?? '-'); ?></span>
                        </div>
                        <?php if(isset($seller->seller_request_data['store_description'])): ?>
                        <div class="py-2">
                            <span class="text-gray-600 block mb-2">توضیحات فروشگاه:</span>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                <?php echo e($seller->seller_request_data['store_description']); ?>

                            </p>
                        </div>
                        <?php endif; ?>
                        <?php if(isset($seller->seller_request_data['address'])): ?>
                        <div class="py-2">
                            <span class="text-gray-600 block mb-2">آدرس:</span>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                <?php echo e($seller->seller_request_data['address']); ?>

                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">اطلاعات درخواست موجود نیست</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ستون سمت راست -->
        <div class="space-y-6">
            <!-- آمار -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">آمار فروش</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">کل آگهی‌ها</p>
                        <p class="text-2xl font-black text-gray-900"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['total_listings']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">آگهی‌های فعال</p>
                        <p class="text-2xl font-black text-green-600"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['active_listings']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">فروش موفق</p>
                        <p class="text-2xl font-black text-blue-600"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($stats['completed_sales']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">درآمد کل</p>
                        <p class="text-xl font-black text-primary"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($stats['total_revenue'])); ?> تومان</p>
                    </div>
                </div>
            </div>

            <!-- اطلاعات کاربر -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">اطلاعات کاربر</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">شناسه:</span>
                        <span class="font-bold"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($seller->id); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">ایمیل:</span>
                        <span class="font-bold"><?php echo e($seller->email); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">تاریخ ثبت‌نام:</span>
                        <span class="font-bold"><?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($seller->created_at)->format('Y/m/d')); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">تاریخ درخواست:</span>
                        <span class="font-bold"><?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($seller->seller_requested_at)->format('Y/m/d')); ?></span>
                    </div>
                    <?php if($seller->seller_approved_at): ?>
                    <div>
                        <span class="text-gray-600">تاریخ تایید:</span>
                        <span class="font-bold"><?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($seller->seller_approved_at)->format('Y/m/d')); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($seller->store): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">فروشگاه</h2>
                <a href="<?php echo e(route('stores.show', $seller->store->slug)); ?>" 
                   target="_blank"
                   class="block bg-primary hover:bg-primary-dark text-white text-center py-3 rounded-lg">
                    مشاهده فروشگاه
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- لیست آگهی‌ها -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">آگهی‌های فروشنده</h2>
        
        <?php if($listings->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">تصویر</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">عنوان</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">دسته‌بندی</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">قیمت پایه</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">وضعیت</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">تاریخ</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-700">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <?php if($listing->images->count() > 0): ?>
                                    <img src="<?php echo e(asset('storage/' . $listing->images->first()->path)); ?>" 
                                         alt="<?php echo e($listing->title); ?>"
                                         class="w-12 h-12 object-cover rounded">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400 text-sm">image</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-bold text-gray-900"><?php echo e($listing->title); ?></div>
                                <div class="text-xs text-gray-500">شناسه: <?php echo app(\App\Services\PersianNumberService::class)->toPersian($listing->id); ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?php echo e($listing->category->name ?? '-'); ?>

                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                <?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($listing->starting_price)); ?> تومان
                            </td>
                            <td class="px-4 py-3">
                                <?php if($listing->status === 'active'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">فعال</span>
                                <?php elseif($listing->status === 'pending'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">در انتظار</span>
                                <?php elseif($listing->status === 'completed'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">تکمیل شده</span>
                                <?php elseif($listing->status === 'suspended'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">تعلیق</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800"><?php echo e($listing->status); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">
                                <?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($listing->created_at)->format('Y/m/d')); ?>

                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="<?php echo e(route('listings.show', $listing)); ?>" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800" 
                                       title="مشاهده">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="<?php echo e(route('admin.listings.manage', $listing)); ?>" 
                                       class="text-primary hover:text-primary/80" 
                                       title="مدیریت">
                                        <span class="material-symbols-outlined text-[18px]">settings</span>
                                    </a>
                                    <?php if($listing->status === 'active'): ?>
                                        <button onclick="showListingSuspendModal(<?php echo e($listing->id); ?>)" 
                                                class="text-red-600 hover:text-red-800" 
                                                title="تعلیق">
                                            <span class="material-symbols-outlined text-[18px]">block</span>
                                        </button>
                                    <?php elseif($listing->status === 'suspended'): ?>
                                        <button onclick="showListingActivateModal(<?php echo e($listing->id); ?>)" 
                                                class="text-green-600 hover:text-green-800" 
                                                title="فعال‌سازی">
                                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <?php echo e($listings->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <span class="material-symbols-outlined text-gray-300 text-6xl mb-3">inventory_2</span>
                <p class="text-gray-500">هیچ آگهی‌ای یافت نشد</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal تایید -->
<div id="approveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
            <h3 class="text-lg font-bold">تایید فروشنده</h3>
        </div>
        <p class="text-gray-600 mb-6">آیا از تایید این فروشنده اطمینان دارید؟ فروشگاه برای او ایجاد خواهد شد.</p>
        <form action="<?php echo e(route('admin.sellers.approve', $seller)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium">
                    تایید
                </button>
                <button type="button" onclick="hideApproveModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal رد کردن -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600 text-2xl">cancel</span>
            </div>
            <h3 class="text-lg font-bold">رد کردن درخواست</h3>
        </div>
        <form action="<?php echo e(route('admin.sellers.reject', $seller)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">دلیل رد:</label>
                <textarea name="reason" rows="4" required
                          placeholder="دلیل رد درخواست را وارد کنید..."
                          class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg font-medium">
                    رد کردن
                </button>
                <button type="button" onclick="hideRejectModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal تعلیق -->
<div id="suspendModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-orange-600 text-2xl">block</span>
            </div>
            <h3 class="text-lg font-bold">تعلیق فروشنده</h3>
        </div>
        <form action="<?php echo e(route('admin.sellers.suspend', $seller)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">دلیل تعلیق:</label>
                <textarea name="reason" rows="4" required
                          placeholder="دلیل تعلیق فروشنده را وارد کنید..."
                          class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                <p class="text-xs text-gray-500 mt-2">تمام آگهی‌های فعال این فروشنده تعلیق خواهند شد.</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-medium">
                    تعلیق
                </button>
                <button type="button" onclick="hideSuspendModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal فعال‌سازی -->
<div id="activateModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
            <h3 class="text-lg font-bold">فعال‌سازی فروشنده</h3>
        </div>
        <p class="text-gray-600 mb-6">آیا از فعال‌سازی مجدد این فروشنده اطمینان دارید؟</p>
        <form action="<?php echo e(route('admin.sellers.activate', $seller)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium">
                    فعال‌سازی
                </button>
                <button type="button" onclick="hideActivateModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal حذف -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-red-600 text-2xl">warning</span>
            </div>
            <h3 class="text-lg font-bold">تبدیل به خریدار</h3>
        </div>
        <p class="text-gray-600 mb-6">آیا از تبدیل این فروشنده به خریدار اطمینان دارید؟ فروشگاه حذف خواهد شد و این عملیات غیرقابل بازگشت است.</p>
        <form action="<?php echo e(route('admin.sellers.destroy', $seller)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg font-medium">
                    تبدیل به خریدار
                </button>
                <button type="button" onclick="hideDeleteModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal تعلیق آگهی -->
<div id="listingSuspendModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-orange-600 text-2xl">block</span>
            </div>
            <h3 class="text-lg font-bold">تعلیق آگهی</h3>
        </div>
        <p class="text-gray-600 mb-6">آیا از تعلیق این آگهی اطمینان دارید؟</p>
        <form id="listingSuspendForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-medium">
                    تعلیق
                </button>
                <button type="button" onclick="hideListingSuspendModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal فعال‌سازی آگهی -->
<div id="listingActivateModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
            <h3 class="text-lg font-bold">فعال‌سازی آگهی</h3>
        </div>
        <p class="text-gray-600 mb-6">آیا از فعال‌سازی این آگهی اطمینان دارید؟</p>
        <form id="listingActivateForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium">
                    فعال‌سازی
                </button>
                <button type="button" onclick="hideListingActivateModal()" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                    انصراف
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('approveModal').classList.add('flex');
}

function hideApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveModal').classList.remove('flex');
}

function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

function showSuspendModal() {
    document.getElementById('suspendModal').classList.remove('hidden');
    document.getElementById('suspendModal').classList.add('flex');
}

function hideSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
    document.getElementById('suspendModal').classList.remove('flex');
}

function showActivateModal() {
    document.getElementById('activateModal').classList.remove('hidden');
    document.getElementById('activateModal').classList.add('flex');
}

function hideActivateModal() {
    document.getElementById('activateModal').classList.add('hidden');
    document.getElementById('activateModal').classList.remove('flex');
}

function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function showListingSuspendModal(listingId) {
    const form = document.getElementById('listingSuspendForm');
    form.action = '<?php echo e(url("/admin/listings")); ?>/' + listingId + '/suspend';
    document.getElementById('listingSuspendModal').classList.remove('hidden');
    document.getElementById('listingSuspendModal').classList.add('flex');
}

function hideListingSuspendModal() {
    document.getElementById('listingSuspendModal').classList.add('hidden');
    document.getElementById('listingSuspendModal').classList.remove('flex');
}

function showListingActivateModal(listingId) {
    const form = document.getElementById('listingActivateForm');
    form.action = '<?php echo e(url("/admin/listings")); ?>/' + listingId + '/activate';
    document.getElementById('listingActivateModal').classList.remove('hidden');
    document.getElementById('listingActivateModal').classList.add('flex');
}

function hideListingActivateModal() {
    document.getElementById('listingActivateModal').classList.add('hidden');
    document.getElementById('listingActivateModal').classList.remove('flex');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/sellers/show.blade.php ENDPATH**/ ?>