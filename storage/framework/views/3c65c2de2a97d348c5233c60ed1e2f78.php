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
     <?php $__env->slot('title', null, []); ?> کیف پول <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> کیف پول من <?php $__env->endSlot(); ?>
    
     <?php $__env->slot('styles', null, []); ?> 
        <link rel="stylesheet" href="<?php echo e(url('css/persian-datepicker-package.css')); ?>?v=<?php echo e(now()->timestamp); ?>">
     <?php $__env->endSlot(); ?>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl mb-6">
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined">error</span>
                <span class="font-bold">خطاهای اعتبارسنجی:</span>
            </div>
            <ul class="list-disc list-inside mr-8">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Available Balance -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">موجودی قابل استفاده</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">account_balance_wallet</span>
            </div>
            <p class="text-4xl font-bold">
                <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($wallet->balance, true); ?>
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>

        <!-- Frozen Balance -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">موجودی مسدود شده</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">lock</span>
            </div>
            <p class="text-4xl font-bold">
                <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($wallet->frozen, true); ?>
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>

        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium opacity-90">مجموع موجودی</h3>
                <span class="material-symbols-outlined text-3xl opacity-80">savings</span>
            </div>
            <p class="text-4xl font-bold">
                <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($wallet->balance + $wallet->frozen, true); ?>
                <span class="text-lg mr-2 font-normal">تومان</span>
            </p>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Add Funds -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600 text-2xl">add_circle</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">افزایش موجودی</h2>
            </div>
            <form method="POST" action="<?php echo e(route('wallet.add-funds')); ?>" class="space-y-4" id="addFundsFormBuyer">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ شارژ (تومان)</label>
                    <?php
                        $minDeposit = \App\Models\SiteSetting::get('wallet_min_deposit', 10000);
                        $maxDeposit = \App\Models\SiteSetting::get('wallet_max_deposit', 100000000);
                        $taxPercentage = \App\Models\SiteSetting::get('wallet_charge_tax', 0);
                    ?>
                    <input type="number" name="amount" id="chargeAmountBuyer" placeholder="مثال: 100000" required 
                           min="<?php echo e($minDeposit); ?>" max="<?php echo e($maxDeposit); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           oninput="calculateChargeTaxBuyer()">
                    <p class="text-xs text-gray-500 mt-1">حداقل: <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($minDeposit, true); ?> - حداکثر: <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($maxDeposit, true); ?> تومان</p>
                </div>

                <?php if($taxPercentage > 0): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4" id="taxInfoBuyer" style="display: none;">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">مبلغ شارژ:</span>
                            <span class="font-semibold text-gray-900" id="baseAmountBuyer">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">مالیات (<?php echo e(\App\Services\PersianNumberService::convertToPersian($taxPercentage)); ?>%):</span>
                            <span class="font-semibold text-blue-600" id="taxAmountBuyer">0</span>
                        </div>
                        <div class="border-t border-blue-300 pt-2 flex justify-between">
                            <span class="font-bold text-gray-900">مبلغ قابل پرداخت:</span>
                            <span class="font-bold text-lg text-blue-700" id="totalAmountBuyer">0</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">payments</span>
                    <span>افزایش موجودی</span>
                </button>
            </form>
        </div>

        <!-- Withdraw Funds -->
        <div class="bg-white rounded-2xl shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-2xl">remove_circle</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900">برداشت از حساب</h2>
            </div>
            <form method="POST" action="<?php echo e(route('wallet.withdraw')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ (تومان)</label>
                    <?php
                        $minWithdraw = \App\Models\SiteSetting::get('wallet_min_withdraw', 50000);
                    ?>
                    <input type="number" name="amount" placeholder="مثال: 50000" required 
                           min="<?php echo e($minWithdraw); ?>" max="<?php echo e($wallet->balance); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">حداقل: <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($minWithdraw, true); ?> - حداکثر: <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($wallet->balance, true); ?> تومان</p>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">account_balance</span>
                    <span>درخواست برداشت</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-2xl">receipt_long</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">تاریخچه تراکنش‌ها</h2>
                </div>
                <a href="<?php echo e(route('wallet.export')); ?>" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                    <span class="material-symbols-outlined text-xl">download</span>
                    <span>دانلود CSV</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">از تاریخ</label>
                    <input type="text" id="from_date" name="from_date" value="<?php echo e(request('from_date')); ?>" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="انتخاب تاریخ" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تا تاریخ</label>
                    <input type="text" id="to_date" name="to_date" value="<?php echo e(request('to_date')); ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="انتخاب تاریخ" readonly>
                </div>
                <div class="sm:col-span-2 flex items-end gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">filter_alt</span>
                        <span>اعمال فیلتر</span>
                    </button>
                    <a href="<?php echo e(route('wallet.show')); ?>" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                        حذف فیلتر
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">تاریخ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">نوع</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">مبلغ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">موجودی بعد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php
                                    $jalaliDate = \Morilog\Jalali\Jalalian::fromDateTime($transaction->created_at)->format('Y/m/d H:i');
                                ?>
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian($jalaliDate)); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $typeLabels = [
                                        'deposit' => 'واریز',
                                        'withdrawal' => 'برداشت از حساب',
                                        'freeze_deposit' => 'مسدود سازی سپرده',
                                        'release_deposit' => 'آزادسازی سپرده',
                                        'deduct_frozen' => 'کسر از موجودی مسدود',
                                        'transfer_in' => 'انتقال به حساب',
                                        'transfer_out' => 'انتقال از حساب',
                                        'forfeit' => 'ضبط سپرده',
                                        'purchase' => 'خرید',
                                        'refund' => 'بازگشت وجه',
                                    ];
                                    $typeColors = [
                                        'deposit' => 'bg-green-100 text-green-800',
                                        'withdrawal' => 'bg-red-100 text-red-800',
                                        'freeze_deposit' => 'bg-orange-100 text-orange-800',
                                        'release_deposit' => 'bg-blue-100 text-blue-800',
                                        'deduct_frozen' => 'bg-red-100 text-red-800',
                                        'transfer_in' => 'bg-green-100 text-green-800',
                                        'transfer_out' => 'bg-red-100 text-red-800',
                                        'forfeit' => 'bg-red-100 text-red-800',
                                        'purchase' => 'bg-purple-100 text-purple-800',
                                        'refund' => 'bg-green-100 text-green-800',
                                    ];
                                    
                                    $label = $typeLabels[$transaction->type] ?? $transaction->type;
                                    $color = $typeColors[$transaction->type] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo e($color); ?>">
                                    <?php echo e($label); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold <?php echo e(in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in']) ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e(in_array($transaction->type, ['deposit', 'release_deposit', 'refund', 'transfer_in']) ? '+' : '-'); ?>

                                    <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($transaction->amount, true); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <?php echo app(\App\Services\PersianNumberService::class)->formatNumber($transaction->balance_after, true); ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-gray-300 text-6xl mb-3 block">receipt_long</span>
                                <p class="text-gray-500 font-medium">هیچ تراکنشی یافت نشد</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($transactions) && $transactions->hasPages()): ?>
            <div class="p-6 border-t border-gray-200">
                <?php echo e($transactions->links()); ?>

            </div>
        <?php endif; ?>
    </div>

     <?php $__env->slot('scripts', null, []); ?> 
        <script src="<?php echo e(url('js/persian-datepicker-package.js')); ?>?v=<?php echo e(now()->timestamp); ?>"></script>
        <script>
        const TAX_PERCENTAGE_BUYER = <?php echo e($taxPercentage ?? 0); ?>;
        
        function calculateChargeTaxBuyer() {
            const amount = parseFloat(document.getElementById('chargeAmountBuyer').value) || 0;
            
            if (amount > 0 && TAX_PERCENTAGE_BUYER > 0) {
                const tax = (amount * TAX_PERCENTAGE_BUYER) / 100;
                const total = amount + tax;
                
                document.getElementById('baseAmountBuyer').textContent = amount.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('taxAmountBuyer').textContent = tax.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('totalAmountBuyer').textContent = total.toLocaleString('fa-IR') + ' تومان';
                document.getElementById('taxInfoBuyer').style.display = 'block';
            } else {
                document.getElementById('taxInfoBuyer').style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');
            
            if (fromDateInput && typeof PersianDatePicker !== 'undefined') {
                new PersianDatePicker(fromDateInput);
            }
            
            if (toDateInput && typeof PersianDatePicker !== 'undefined') {
                new PersianDatePicker(toDateInput);
            }
        });
        </script>
     <?php $__env->endSlot(); ?>
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
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/wallet/show.blade.php ENDPATH**/ ?>