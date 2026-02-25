

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">edit</span>
                        </div>
                        ویرایش درگاه: <?php echo e($gateway->display_name); ?>

                    </h1>
                    <p class="mt-2 text-gray-600">تنظیم اطلاعات احراز هویت و پیکربندی درگاه پرداخت</p>
                </div>
                <a href="<?php echo e(route('admin.payment-gateways.index')); ?>" 
                   class="bg-white text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-50 transition-all duration-300 font-medium flex items-center gap-2 shadow-md border border-gray-200">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span>بازگشت</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-md p-8 border border-gray-100">
                    <form action="<?php echo e(route('admin.payment-gateways.update', $gateway)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- Status Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600">toggle_on</span>
                                وضعیت درگاه
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                                <!-- فعال‌سازی درگاه -->
                                <label class="flex items-center justify-between cursor-pointer group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                            <span class="material-symbols-outlined text-2xl <?php echo e($gateway->is_active ? 'text-green-600' : 'text-gray-400'); ?>">
                                                <?php echo e($gateway->is_active ? 'check_circle' : 'cancel'); ?>

                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">فعال‌سازی درگاه</p>
                                            <p class="text-sm text-gray-600">درگاه برای کاربران قابل استفاده باشد</p>
                                        </div>
                                    </div>
                                    <div class="relative inline-block">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               id="is_active"
                                               value="1"
                                               <?php echo e($gateway->is_active ? 'checked' : ''); ?>

                                               class="sr-only peer">
                                        <div class="w-14 h-8 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-all duration-300"></div>
                                        <div class="absolute top-1 right-1 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300 peer-checked:right-7"></div>
                                    </div>
                                </label>

                                <?php if($gateway->name === 'zarinpal'): ?>
                                <!-- حالت Sandbox (فقط برای زرین‌پال) -->
                                <label class="flex items-center justify-between cursor-pointer group border-t border-gray-200 pt-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                            <span class="material-symbols-outlined text-2xl <?php echo e($gateway->sandbox_mode ? 'text-orange-600' : 'text-gray-400'); ?>">
                                                <?php echo e($gateway->sandbox_mode ? 'science' : 'public'); ?>

                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">حالت Sandbox</p>
                                            <p class="text-sm text-gray-600">برای تست پرداخت (بدون انتقال پول واقعی)</p>
                                        </div>
                                    </div>
                                    <div class="relative inline-block">
                                        <input type="checkbox" 
                                               name="sandbox_mode" 
                                               id="sandbox_mode"
                                               value="1"
                                               <?php echo e($gateway->sandbox_mode ? 'checked' : ''); ?>

                                               class="sr-only peer">
                                        <div class="w-14 h-8 bg-gray-300 rounded-full peer peer-checked:bg-orange-500 transition-all duration-300"></div>
                                        <div class="absolute top-1 right-1 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300 peer-checked:right-7"></div>
                                    </div>
                                </label>
                                
                                <?php if($gateway->sandbox_mode): ?>
                                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-start gap-3">
                                    <span class="material-symbols-outlined text-orange-600 text-xl flex-shrink-0">warning</span>
                                    <div class="text-sm text-orange-800">
                                        <p class="font-semibold mb-1">حالت تست فعال است</p>
                                        <p>در این حالت، پرداخت‌ها واقعی نیستند و فقط برای تست استفاده می‌شوند. برای محیط واقعی، این گزینه را غیرفعال کنید.</p>
                                        <p class="mt-2 text-xs">💡 نکته: در حالت Sandbox نیازی به وارد کردن Merchant ID واقعی نیست. سیستم به صورت خودکار از یک UUID تستی استفاده می‌کند.</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600">sort</span>
                                ترتیب نمایش
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-6">
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    اولویت نمایش در لیست درگاه‌ها
                                </label>
                                <input type="number" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="<?php echo e(old('sort_order', $gateway->sort_order)); ?>"
                                       min="0">
                                <p class="text-xs text-gray-500 mt-2">عدد کوچکتر = اولویت بالاتر</p>
                            </div>
                        </div>

                        <!-- Credentials Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600">key</span>
                                اطلاعات احراز هویت
                            </h3>

                            <div class="space-y-4">
                                <?php switch($gateway->name):
                                    case ('zarinpal'): ?>
                                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
                                            <label for="merchant_id" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-yellow-600">badge</span>
                                                Merchant ID
                                            </label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border border-yellow-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent bg-white" 
                                                   id="merchant_id" 
                                                   name="credentials[merchant_id]" 
                                                   value="<?php echo e(old('credentials.merchant_id', $gateway->getCredential('merchant_id'))); ?>"
                                                   placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                                   dir="ltr">
                                            <p class="text-xs text-yellow-800 mt-2 flex items-start gap-1">
                                                <span class="material-symbols-outlined text-sm">info</span>
                                                کد پذیرنده را از پنل زرین‌پال دریافت کنید
                                            </p>
                                        </div>
                                        <?php break; ?>

                                    <?php case ('zibal'): ?>
                                        <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl p-6 border border-teal-200">
                                            <label for="merchant_id" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-teal-600">badge</span>
                                                Merchant ID
                                            </label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border border-teal-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white" 
                                                   id="merchant_id" 
                                                   name="credentials[merchant_id]" 
                                                   value="<?php echo e(old('credentials.merchant_id', $gateway->getCredential('merchant_id'))); ?>"
                                                   placeholder="zibal"
                                                   dir="ltr">
                                            <p class="text-xs text-teal-800 mt-2 flex items-start gap-1">
                                                <span class="material-symbols-outlined text-sm">info</span>
                                                کد پذیرنده را از پنل زیبال دریافت کنید
                                            </p>
                                        </div>
                                        <?php break; ?>

                                    <?php case ('vandar'): ?>
                                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-200">
                                            <label for="api_key" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-indigo-600">vpn_key</span>
                                                API Key
                                            </label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border border-indigo-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" 
                                                   id="api_key" 
                                                   name="credentials[api_key]" 
                                                   value="<?php echo e(old('credentials.api_key', $gateway->getCredential('api_key'))); ?>"
                                                   placeholder="your-api-key"
                                                   dir="ltr">
                                            <p class="text-xs text-indigo-800 mt-2 flex items-start gap-1">
                                                <span class="material-symbols-outlined text-sm">info</span>
                                                کلید API را از پنل وندار دریافت کنید
                                            </p>
                                        </div>
                                        <?php break; ?>

                                    <?php case ('payping'): ?>
                                        <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 border border-red-200">
                                            <label for="api_key" class="block text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-red-600">vpn_key</span>
                                                API Key
                                            </label>
                                            <input type="text" 
                                                   class="w-full px-4 py-3 border border-red-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white" 
                                                   id="api_key" 
                                                   name="credentials[api_key]" 
                                                   value="<?php echo e(old('credentials.api_key', $gateway->getCredential('api_key'))); ?>"
                                                   placeholder="your-api-key"
                                                   dir="ltr">
                                            <p class="text-xs text-red-800 mt-2 flex items-start gap-1">
                                                <span class="material-symbols-outlined text-sm">info</span>
                                                کلید API را از پنل پی‌پینگ دریافت کنید
                                            </p>
                                        </div>
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-bold text-lg flex items-center justify-center gap-3 shadow-lg hover:shadow-xl">
                                <span class="material-symbols-outlined text-2xl">save</span>
                                <span>ذخیره تغییرات</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Gateway Info -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">info</span>
                        اطلاعات درگاه
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">نام فنی:</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($gateway->name); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">نام نمایشی:</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($gateway->display_name); ?></span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">وضعیت:</span>
                            <?php if($gateway->is_active): ?>
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-lg text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    فعال
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full"></span>
                                    غیرفعال
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-md p-6 border border-blue-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">help</span>
                        راهنما
                    </h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <?php switch($gateway->name):
                            case ('zarinpal'): ?>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-yellow-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>وارد <a href="https://www.zarinpal.com" target="_blank" class="text-blue-600 hover:underline">پنل زرین‌پال</a> شوید</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-yellow-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>به بخش "درگاه پرداخت" بروید</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-yellow-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>کد پذیرنده (Merchant ID) را کپی کنید</span>
                                </p>
                                <div class="mt-3 pt-3 border-t border-yellow-200">
                                    <p class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-orange-600">science</span>
                                        حالت Sandbox:
                                    </p>
                                    <p class="text-xs mb-2">برای تست، حالت Sandbox را فعال کنید. در این حالت می‌توانید با کارت‌های تست پرداخت کنید بدون اینکه پول واقعی منتقل شود.</p>
                                    <div class="bg-white rounded-lg p-3 mt-2 space-y-2">
                                        <p class="text-xs font-semibold text-gray-900">کارت‌های تست:</p>
                                        <div class="text-xs text-gray-700 space-y-1">
                                            <p>• شماره کارت: <code class="bg-gray-100 px-2 py-0.5 rounded">6037-9971-0000-0001</code></p>
                                            <p>• CVV2: <code class="bg-gray-100 px-2 py-0.5 rounded">000</code></p>
                                            <p>• تاریخ انقضا: هر تاریخی در آینده</p>
                                            <p>• رمز دوم: <code class="bg-gray-100 px-2 py-0.5 rounded">123456</code></p>
                                        </div>
                                    </div>
                                    <p class="text-xs mt-2">
                                        <a href="https://www.zarinpal.com/docs/paymentGateway/sandBox.html" target="_blank" class="text-blue-600 hover:underline">
                                            📖 مستندات Sandbox زرین‌پال
                                        </a>
                                    </p>
                                </div>
                                <?php break; ?>
                            <?php case ('zibal'): ?>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-teal-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>وارد <a href="https://zibal.ir" target="_blank" class="text-blue-600 hover:underline">پنل زیبال</a> شوید</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-teal-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>کد پذیرنده خود را دریافت کنید</span>
                                </p>
                                <?php break; ?>
                            <?php case ('vandar'): ?>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-indigo-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>وارد <a href="https://vandar.io" target="_blank" class="text-blue-600 hover:underline">پنل وندار</a> شوید</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-indigo-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>کلید API خود را ایجاد کنید</span>
                                </p>
                                <?php break; ?>
                            <?php case ('payping'): ?>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-red-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>وارد <a href="https://payping.ir" target="_blank" class="text-blue-600 hover:underline">پنل پی‌پینگ</a> شوید</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-red-600 text-lg flex-shrink-0">arrow_back</span>
                                    <span>کلید API خود را دریافت کنید</span>
                                </p>
                                <?php break; ?>
                        <?php endswitch; ?>
                    </div>
                </div>

                <!-- Warning Card -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl shadow-md p-6 border border-orange-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-600">warning</span>
                        نکات امنیتی
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="text-orange-600 mt-0.5">•</span>
                            <span>اطلاعات احراز هویت را به صورت امن نگهداری کنید</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-600 mt-0.5">•</span>
                            <span>هرگز این اطلاعات را با دیگران به اشتراک نگذارید</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-orange-600 mt-0.5">•</span>
                            <span>قبل از فعال‌سازی، درگاه را تست کنید</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/payment-gateways/edit.blade.php ENDPATH**/ ?>