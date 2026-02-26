

<?php $__env->startSection('title', 'تنظیمات سایت'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">تنظیمات سایت</h1>

        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- تنظیمات سپرده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات سپرده شرکت در مزایده</h2>
            
            <form action="<?php echo e(route('admin.settings.deposit.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع محاسبه سپرده</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="deposit_type" value="fixed" 
                                   <?php echo e($depositSettings['type'] === 'fixed' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>مبلغ ثابت</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="deposit_type" value="percentage" 
                                   <?php echo e($depositSettings['type'] === 'percentage' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>درصد از قیمت پایه</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="deposit_fixed_amount" class="block text-gray-700 font-bold mb-2">
                        مبلغ ثابت سپرده (تومان)
                    </label>
                    <input type="number" 
                           id="deposit_fixed_amount" 
                           name="deposit_fixed_amount" 
                           value="<?php echo e($depositSettings['fixed_amount']); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0">
                </div>

                <div class="mb-6">
                    <label for="deposit_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سپرده (%)
                    </label>
                    <input type="number" 
                           id="deposit_percentage" 
                           name="deposit_percentage" 
                           value="<?php echo e($depositSettings['percentage']); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات سپرده
                </button>
            </form>
        </div>

        <!-- تنظیمات مدت زمان حراجی -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات مدت زمان حراجی</h2>
            
            <form action="<?php echo e(route('admin.settings.auction-duration.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="force_auction_duration" 
                               value="1"
                               id="force_auction_duration"
                               <?php echo e($auctionDurationSettings['force_duration'] ?? false ? 'checked' : ''); ?>

                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                               onchange="toggleDurationFields()">
                        <div>
                            <span class="font-bold text-gray-700">اجبار مدت زمان ثابت برای حراجی‌ها</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، فروشندگان نمی‌توانند زمان پایان حراجی را خودشان انتخاب کنند و مدت زمان به صورت خودکار محاسبه می‌شود.
                            </p>
                        </div>
                    </label>
                </div>

                <div id="duration-fields" class="<?php echo e(($auctionDurationSettings['force_duration'] ?? false) ? '' : 'opacity-50 pointer-events-none'); ?>">
                    <div class="mb-6">
                        <label for="auction_duration_days" class="block text-gray-700 font-bold mb-2">
                            مدت زمان حراجی (روز)
                        </label>
                        <input type="number" 
                               id="auction_duration_days" 
                               name="auction_duration_days" 
                               value="<?php echo e($auctionDurationSettings['duration_days'] ?? 7); ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               min="1"
                               max="365">
                        <p class="text-sm text-gray-600 mt-2">
                            حراجی‌ها به صورت خودکار X روز بعد از زمان شروع به پایان می‌رسند.
                        </p>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات مدت زمان
                </button>
            </form>
        </div>

        <!-- تنظیمات فروشندگان -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات فروشندگان</h2>
            
            <form action="<?php echo e(route('admin.settings.seller.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="require_seller_approval" 
                               value="1"
                               <?php echo e($sellerSettings['require_approval'] ? 'checked' : ''); ?>

                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نیاز به تایید دستی فروشندگان</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، درخواست‌های فروشندگی باید توسط ادمین تایید شوند. در غیر این صورت، کاربران بلافاصله پس از درخواست فعال می‌شوند.
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات فروشندگان
                </button>
            </form>
        </div>

        <!-- تنظیمات آگهی‌ها -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات آگهی‌ها</h2>
            
            <form action="<?php echo e(route('admin.settings.listing.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="require_listing_approval" 
                               value="1"
                               <?php echo e(($listingSettings['require_approval'] ?? true) ? 'checked' : ''); ?>

                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نیاز به تایید دستی آگهی‌ها</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، تمام آگهی‌های جدید و ویرایش شده باید توسط ادمین تایید شوند تا منتشر شوند. در غیر این صورت، آگهی‌ها بلافاصله پس از ثبت منتشر می‌شوند.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="default_show_before_start" 
                               value="1"
                               <?php echo e(($listingSettings['default_show_before_start'] ?? false) ? 'checked' : ''); ?>

                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div>
                            <span class="font-bold text-gray-700">نمایش پیش‌فرض حراجی‌ها قبل از شروع</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، حراجی‌های جدید به صورت پیش‌فرض در لیست‌های سایت قبل از زمان شروع نمایش داده می‌شوند (با برچسب "هنوز شروع نشده"). فروشندگان می‌توانند این تنظیم را برای هر حراجی تغییر دهند.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="mb-6">
                    <label class="block font-bold text-gray-700 mb-2">
                        گام افزایش پیشنهاد پیش‌فرض (تومان)
                    </label>
                    <input type="number" 
                           name="default_bid_increment" 
                           value="<?php echo e(old('default_bid_increment', $listingSettings['default_bid_increment'] ?? 10000)); ?>"
                           min="1000"
                           step="1000"
                           required
                           class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-sm text-gray-600 mt-1">
                        این مقدار برای تمام آگهی‌های جدید به عنوان حداقل افزایش پیشنهاد استفاده می‌شود.
                    </p>
                    <?php $__errorArgs = ['default_bid_increment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 text-xl">info</span>
                        <div class="text-sm text-yellow-800">
                            <p class="font-bold mb-1">توجه:</p>
                            <p>اگر گزینه "نیاز به تایید دستی" را غیرفعال کنید، آگهی‌های جدید بدون بررسی منتشر می‌شوند. توصیه می‌شود این گزینه را فعال نگه دارید تا کیفیت آگهی‌ها کنترل شود.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات آگهی‌ها
                </button>
            </form>
        </div>

        <!-- تنظیمات کمیسیون -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات کمیسیون سایت</h2>
            
            <form action="<?php echo e(route('admin.settings.commission.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع محاسبه کمیسیون</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="fixed" 
                                   <?php echo e($commissionSettings['type'] === 'fixed' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>مبلغ ثابت</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="percentage" 
                                   <?php echo e($commissionSettings['type'] === 'percentage' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>درصد از قیمت نهایی</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_type" value="category" 
                                   <?php echo e($commissionSettings['type'] === 'category' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <div>
                                <span>بر اساس دسته‌بندی</span>
                                <p class="text-sm text-gray-600 mt-1">
                                    کمیسیون بر اساس دسته‌بندی محصول محاسبه می‌شود. می‌توانید برای هر دسته‌بندی کمیسیون جداگانه تعریف کنید.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="commission_fixed_amount" class="block text-gray-700 font-bold mb-2">
                        مبلغ ثابت کمیسیون (تومان)
                    </label>
                    <input type="number" 
                           id="commission_fixed_amount" 
                           name="commission_fixed_amount" 
                           value="<?php echo e($commissionSettings['fixed_amount']); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0">
                </div>

                <div class="mb-6">
                    <label for="commission_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد کمیسیون (%)
                    </label>
                    <input type="number" 
                           id="commission_percentage" 
                           name="commission_percentage" 
                           value="<?php echo e($commissionSettings['percentage']); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">پرداخت کننده کمیسیون</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="buyer" 
                                   <?php echo e($commissionSettings['payer'] === 'buyer' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>خریدار</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="seller" 
                                   <?php echo e($commissionSettings['payer'] === 'seller' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>فروشنده</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="commission_payer" value="both" 
                                   <?php echo e($commissionSettings['payer'] === 'both' ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>هر دو (تقسیم بین خریدار و فروشنده)</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="commission_split_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سهم خریدار از کمیسیون (%) - فقط برای حالت "هر دو"
                    </label>
                    <input type="number" 
                           id="commission_split_percentage" 
                           name="commission_split_percentage" 
                           value="<?php echo e($commissionSettings['split_percentage']); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                    <p class="text-sm text-gray-600 mt-2">
                        مثال: اگر 60 وارد کنید، 60% از کمیسیون از خریدار و 40% از فروشنده کسر می‌شود.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات کمیسیون
                </button>
                
                <?php if($commissionSettings['type'] === 'category'): ?>
                    <a href="<?php echo e(route('admin.category-commissions.index')); ?>" class="inline-block mr-4 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                        مدیریت کمیسیون دسته‌بندی‌ها
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- تنظیمات بازندگان مزایده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات بازندگان مزایده</h2>
            
            <form action="<?php echo e(route('admin.settings.loser-fee.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="loser_fee_enabled" 
                               value="1"
                               id="loser_fee_enabled"
                               <?php echo e(($loserFeeSettings['enabled'] ?? false) ? 'checked' : ''); ?>

                               class="ml-2 w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                               onchange="toggleLoserFeeFields()">
                        <div>
                            <span class="font-bold text-gray-700">کسر کارمزد از بازندگان مزایده</span>
                            <p class="text-sm text-gray-600 mt-1">
                                اگر فعال باشد، درصدی از سپرده کاربرانی که در مزایده برنده نشدند به عنوان کارمزد کسر می‌شود.
                            </p>
                        </div>
                    </label>
                </div>

                <div id="loser-fee-fields" class="<?php echo e(($loserFeeSettings['enabled'] ?? false) ? '' : 'opacity-50 pointer-events-none'); ?>">
                    <div class="mb-6">
                        <label for="loser_fee_percentage" class="block text-gray-700 font-bold mb-2">
                            درصد کارمزد از سپرده (%)
                        </label>
                        <input type="number" 
                               id="loser_fee_percentage" 
                               name="loser_fee_percentage" 
                               value="<?php echo e($loserFeeSettings['percentage'] ?? 5); ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               min="0" 
                               max="100" 
                               step="0.01">
                        <p class="text-sm text-gray-600 mt-2">
                            مثال: اگر 5 وارد کنید، 5% از سپرده کاربران بازنده کسر می‌شود و مابقی به آنها برگردانده می‌شود.
                        </p>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات بازندگان
                </button>
            </form>
        </div>

        <!-- تنظیمات سپرده ضبط شده -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات سپرده ضبط شده</h2>
            
            <form action="<?php echo e(route('admin.settings.forfeit.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label for="forfeit_to_site_percentage" class="block text-gray-700 font-bold mb-2">
                        درصد سهم سایت از سپرده ضبط شده (%)
                    </label>
                    <input type="number" 
                           id="forfeit_to_site_percentage" 
                           name="forfeit_to_site_percentage" 
                           value="<?php echo e($forfeitSettings['to_site_percentage'] ?? 100); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0" 
                           max="100" 
                           step="0.01">
                    <p class="text-sm text-gray-600 mt-2">
                        وقتی برنده مزایده در مهلت مقرر پرداخت نکند، سپرده او ضبط می‌شود. این درصد به سایت می‌رسد و مابقی به فروشنده.
                        <br>
                        مثال: اگر 60 وارد کنید، 60% به سایت و 40% به فروشنده می‌رسد.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات سپرده ضبط شده
                </button>
            </form>
        </div>

        <!-- تنظیمات زمان‌بندی حراجی -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات زمان‌بندی حراجی</h2>
            
            <form action="<?php echo e(route('admin.settings.auction-release.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label for="auction_finalize_deadline_hours" class="block text-gray-700 font-bold mb-2">
                        مهلت تکمیل پرداخت حراجی (ساعت)
                    </label>
                    <input type="number" 
                           id="auction_finalize_deadline_hours" 
                           name="auction_finalize_deadline_hours" 
                           value="<?php echo e($auctionReleaseSettings['finalize_deadline_hours'] ?? 24); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1" 
                           max="168">
                    <p class="text-sm text-gray-600 mt-2">
                        برنده حراجی باید ظرف این مدت، مبلغ باقیمانده را پرداخت کند. در غیر این صورت، سپرده او ضبط می‌شود.
                        <br>
                        <strong>توصیه:</strong> 24 ساعت (1 روز) مناسب است.
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات زمان‌بندی
                </button>
            </form>
        </div>

        <!-- تنظیمات کیف پول -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات کیف پول</h2>
            
            <form action="<?php echo e(route('admin.settings.wallet.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-6">
                    <label for="wallet_min_deposit" class="block text-gray-700 font-bold mb-2">
                        حداقل مبلغ شارژ حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_min_deposit" 
                           name="wallet_min_deposit" 
                           value="<?php echo e($walletSettings['min_deposit'] ?? 10000); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند کمتر از این مبلغ به کیف پول خود شارژ کنند.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="wallet_max_deposit" class="block text-gray-700 font-bold mb-2">
                        حداکثر مبلغ شارژ حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_max_deposit" 
                           name="wallet_max_deposit" 
                           value="<?php echo e($walletSettings['max_deposit'] ?? 100000000); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="10000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند بیشتر از این مبلغ در هر بار به کیف پول خود شارژ کنند.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="wallet_min_withdraw" class="block text-gray-700 font-bold mb-2">
                        حداقل مبلغ برداشت از حساب (تومان)
                    </label>
                    <input type="number" 
                           id="wallet_min_withdraw" 
                           name="wallet_min_withdraw" 
                           value="<?php echo e($walletSettings['min_withdraw'] ?? 50000); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1000">
                    <p class="text-sm text-gray-600 mt-2">
                        کاربران نمی‌توانند کمتر از این مبلغ از کیف پول خود برداشت کنند.
                    </p>
                </div>

                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">مالیات شارژ کیف پول</h3>
                    
                    <div class="mb-4">
                        <label for="wallet_charge_tax" class="block text-gray-700 font-bold mb-2">
                            درصد مالیات شارژ (%)
                        </label>
                        <input type="number" 
                               id="wallet_charge_tax" 
                               name="wallet_charge_tax" 
                               value="<?php echo e($walletSettings['charge_tax'] ?? 0); ?>"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                               min="0"
                               max="100"
                               step="0.1">
                        <p class="text-sm text-gray-600 mt-2">
                            این درصد به مبلغ شارژ اضافه می‌شود. مثال: اگر 9% باشد و کاربر 100,000 تومان شارژ کند، باید 109,000 تومان پرداخت کند.
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
                            <div class="text-sm text-blue-800">
                                <p class="font-bold mb-1">نحوه محاسبه:</p>
                                <p>مبلغ نهایی = مبلغ شارژ + (مبلغ شارژ × درصد مالیات ÷ 100)</p>
                                <p class="mt-2">مثال: شارژ 100,000 تومان با مالیات 9% = 100,000 + 9,000 = 109,000 تومان</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات کیف پول
                </button>
            </form>
        </div>

        <!-- تنظیمات جریمه لغو سفارش -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات جریمه لغو سفارش</h2>
            
            <form action="<?php echo e(route('admin.settings.cancellation-penalty.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 mt-0.5">info</span>
                        <div class="text-sm text-gray-700">
                            <p class="font-bold mb-1">درباره جریمه لغو سفارش:</p>
                            <p>زمانی که خریدار یا فروشنده سفارشی را در مرحله "در حال پردازش" لغو می‌کند، این جریمه از کیف پول آن‌ها کسر شده و به حساب مدیر سایت واریز می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">نوع محاسبه جریمه</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="order_cancellation_penalty_type" value="percentage" 
                                   <?php echo e((\App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage') === 'percentage') ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>درصد از مبلغ سفارش</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="order_cancellation_penalty_type" value="fixed" 
                                   <?php echo e((\App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage') === 'fixed') ? 'checked' : ''); ?>

                                   class="ml-2">
                            <span>مبلغ ثابت</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="order_cancellation_penalty_value" class="block text-gray-700 font-bold mb-2">
                        مقدار جریمه (درصد یا مبلغ ثابت به تومان)
                    </label>
                    <input type="number" 
                           id="order_cancellation_penalty_value" 
                           name="order_cancellation_penalty_value" 
                           value="<?php echo e(\App\Models\SiteSetting::get('order_cancellation_penalty_value', 10)); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="0"
                           step="0.01">
                    <p class="text-sm text-gray-600 mt-2">
                        اگر نوع "درصد" انتخاب شده: عدد بین 0 تا 100 (مثلاً 10 یعنی 10 درصد)<br>
                        اگر نوع "مبلغ ثابت" انتخاب شده: مبلغ به تومان (مثلاً 50000 یعنی 50,000 تومان)
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-blue-600 mt-0.5">calculate</span>
                        <div class="text-sm text-blue-800">
                            <p class="font-bold mb-1">مثال محاسبه:</p>
                            <p class="mb-2"><strong>درصدی:</strong> سفارش 1,000,000 تومانی با جریمه 10% = 100,000 تومان جریمه</p>
                            <p><strong>ثابت:</strong> هر سفارش لغو شده = 50,000 تومان جریمه (صرف‌نظر از مبلغ سفارش)</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات جریمه
                </button>
            </form>
        </div>

        <!-- تنظیمات مهلت تست کالا -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">تنظیمات مهلت تست و بررسی کالا</h2>
            
            <form action="<?php echo e(route('admin.settings.test-period.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-blue-600 mt-0.5">info</span>
                        <div class="text-sm text-gray-700">
                            <p class="font-bold mb-1">درباره مهلت تست:</p>
                            <p>پس از ارسال سفارش (وقتی فروشنده کد رهگیری را ثبت کرد)، خریدار این مدت زمان را برای دریافت و تست کالا دارد. اگر مشکلی اعلام نکند، پول به فروشنده واریز می‌شود.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="order_test_period_days" class="block text-gray-700 font-bold mb-2">
                        مهلت تست و بررسی کالا (روز)
                    </label>
                    <input type="number" 
                           id="order_test_period_days" 
                           name="order_test_period_days" 
                           value="<?php echo e(\App\Models\SiteSetting::get('order_test_period_days', 7)); ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="1"
                           max="30">
                    <p class="text-sm text-gray-600 mt-2">
                        تعداد روزهایی که خریدار برای دریافت و تست کالا وقت دارد. این مهلت از زمان ثبت کد رهگیری توسط فروشنده شروع می‌شود.
                        <br>
                        <strong>پیشنهادی:</strong> 7 روز
                    </p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    ذخیره تنظیمات مهلت تست
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDurationFields() {
    const checkbox = document.getElementById('force_auction_duration');
    const fields = document.getElementById('duration-fields');
    
    if (checkbox.checked) {
        fields.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        fields.classList.add('opacity-50', 'pointer-events-none');
    }
}

function toggleLoserFeeFields() {
    const checkbox = document.getElementById('loser_fee_enabled');
    const fields = document.getElementById('loser-fee-fields');
    
    if (checkbox.checked) {
        fields.classList.remove('opacity-50', 'pointer-events-none');
    } else {
        fields.classList.add('opacity-50', 'pointer-events-none');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>