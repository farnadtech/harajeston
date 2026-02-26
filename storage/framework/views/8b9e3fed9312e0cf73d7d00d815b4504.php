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
     <?php $__env->slot('title', null, []); ?> جزئیات سفارش #<?php echo e($order->order_number); ?> <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> جزئیات سفارش #<?php echo e($order->order_number); ?> <?php $__env->endSlot(); ?>

    <div class="max-w-5xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="<?php echo e(route('orders.index')); ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت به لیست سفارشات
            </a>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle ml-2"></i>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">سفارش #<?php echo e($order->order_number); ?></h1>
                    <p class="text-gray-600">
                        <i class="far fa-calendar ml-1"></i>
                        <?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i')); ?>

                    </p>
                </div>
                
                <?php
                    $statusConfig = [
                        'pending' => ['text' => 'در انتظار پردازش', 'class' => 'bg-yellow-100 text-yellow-800'],
                        'processing' => ['text' => 'در حال پردازش', 'class' => 'bg-blue-100 text-blue-800'],
                        'shipped' => ['text' => 'در حال ارسال', 'class' => 'bg-purple-100 text-purple-800'],
                        'delivered' => ['text' => 'تحویل داده شده', 'class' => 'bg-green-100 text-green-800'],
                        'cancelled' => ['text' => 'لغو شده', 'class' => 'bg-red-100 text-red-800'],
                    ];
                    $status = $statusConfig[$order->status] ?? ['text' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                ?>
                <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo e($status['class']); ?>">
                    <?php echo e($status['text']); ?>

                </span>
            </div>

            <!-- Order Progress -->
            <div class="mt-6">
                <div class="flex items-center justify-between relative">
                    <?php
                        $steps = [
                            'pending' => 'در انتظار',
                            'processing' => 'پردازش',
                            'shipped' => 'ارسال',
                            'delivered' => 'تحویل'
                        ];
                        $currentStep = array_search($order->status, array_keys($steps));
                        if ($currentStep === false) $currentStep = -1;
                    ?>
                    
                    <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $stepIndex = array_search($key, array_keys($steps));
                            $isActive = $stepIndex <= $currentStep;
                            $isCancelled = $order->status === 'cancelled';
                        ?>
                        <div class="flex flex-col items-center relative z-10" style="flex: 1;">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 <?php echo e($isActive && !$isCancelled ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500'); ?>">
                                <?php if($isActive && !$isCancelled): ?>
                                    <i class="fas fa-check"></i>
                                <?php else: ?>
                                    <span class="text-sm"><?php echo e($stepIndex + 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs text-gray-600"><?php echo e($label); ?></span>
                        </div>
                        
                        <?php if(!$loop->last): ?>
                            <div class="flex-1 h-1 <?php echo e($stepIndex < $currentStep && !$isCancelled ? 'bg-blue-600' : 'bg-gray-200'); ?>" style="margin: 0 -1rem; margin-top: -2rem; z-index: 0;"></div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">اقلام سفارش</h2>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                <?php if($item->listing->images->isNotEmpty()): ?>
                                    <img src="<?php echo e(url('storage/' . $item->listing->images->first()->file_path)); ?>" 
                                         alt="<?php echo e($item->listing->title); ?>"
                                         class="w-20 h-20 object-cover rounded-lg ml-4">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg ml-4 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800 mb-1"><?php echo e($item->listing->title); ?></h3>
                                    <p class="text-sm text-gray-600 mb-2">تعداد: <?php echo e($item->quantity); ?></p>
                                    <p class="text-sm text-gray-500">قیمت واحد: <?php echo e(number_format($item->price_snapshot)); ?> تومان</p>
                                </div>
                                
                                <div class="text-left">
                                    <p class="font-semibold text-gray-800"><?php echo e(number_format($item->subtotal)); ?> تومان</p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Shipping Address -->
                <?php if($order->shipping_address): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-map-marker-alt ml-2 text-blue-600"></i>
                            آدرس ارسال
                        </h2>
                        <p class="text-gray-700 leading-relaxed"><?php echo e($order->shipping_address); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Tracking Info -->
                <?php if($order->tracking_number): ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-3">
                            <i class="fas fa-truck ml-2 text-blue-600"></i>
                            اطلاعات ارسال
                        </h2>
                        
                        <!-- Shipping Method -->
                        <?php if($order->shippingMethod): ?>
                        <div class="mb-4 pb-4 border-b border-blue-200">
                            <p class="text-sm text-gray-600 mb-1">روش ارسال</p>
                            <p class="text-lg font-semibold text-gray-900"><?php echo e($order->shippingMethod->name); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Tracking Number -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">کد رهگیری مرسوله</p>
                                <p class="text-xl font-bold text-gray-900"><?php echo e($order->tracking_number); ?></p>
                            </div>
                            <button onclick="navigator.clipboard.writeText('<?php echo e($order->tracking_number); ?>')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-copy ml-1"></i>
                                کپی
                            </button>
                        </div>

                        <?php if($order->buyer_id === auth()->id() && $order->status === 'shipped'): ?>
                            <?php
                                $testDays = (int) \App\Models\SiteSetting::get('order_test_period_days', 7);
                                $shippedDate = $order->shipped_at ? \Carbon\Carbon::parse($order->shipped_at) : $order->updated_at;
                                $deadlineDate = $shippedDate->copy()->addDays($testDays);
                                $deadlineTimestamp = $deadlineDate->timestamp;
                            ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-clock text-yellow-600 text-xl mt-1"></i>
                                    <div class="text-sm text-gray-700 w-full">
                                        <p class="font-bold mb-2">مهلت تست و بررسی کالا:</p>
                                        <p class="mb-3">
                                            از الان تا <strong><?php echo e(\App\Services\PersianNumberService::convertToPersian($testDays)); ?> روز</strong> وقت دارید کالا را دریافت و تست کنید.
                                        </p>
                                        
                                        <!-- Countdown Timer -->
                                        <div class="bg-white rounded-lg p-4 mb-3" id="countdown-timer" data-deadline="<?php echo e($deadlineTimestamp); ?>">
                                            <div class="flex items-center justify-center gap-2 text-2xl font-bold" dir="ltr">
                                                <div class="text-center">
                                                    <div class="bg-blue-600 text-white rounded-lg px-3 py-2 min-w-[60px]" id="days">00</div>
                                                    <div class="text-xs text-gray-600 mt-1">روز</div>
                                                </div>
                                                <div class="text-gray-400">:</div>
                                                <div class="text-center">
                                                    <div class="bg-blue-600 text-white rounded-lg px-3 py-2 min-w-[60px]" id="hours">00</div>
                                                    <div class="text-xs text-gray-600 mt-1">ساعت</div>
                                                </div>
                                                <div class="text-gray-400">:</div>
                                                <div class="text-center">
                                                    <div class="bg-blue-600 text-white rounded-lg px-3 py-2 min-w-[60px]" id="minutes">00</div>
                                                    <div class="text-xs text-gray-600 mt-1">دقیقه</div>
                                                </div>
                                                <div class="text-gray-400">:</div>
                                                <div class="text-center">
                                                    <div class="bg-blue-600 text-white rounded-lg px-3 py-2 min-w-[60px]" id="seconds">00</div>
                                                    <div class="text-xs text-gray-600 mt-1">ثانیه</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <p class="text-xs text-gray-600">
                                            اگر مشکلی با کالا دارید، حتماً قبل از پایان مهلت اعلام کنید. در غیر این صورت پول به فروشنده واریز می‌شود.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">خلاصه سفارش</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>جمع کل اقلام</span>
                            <span><?php echo e(number_format($order->subtotal)); ?> تومان</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>هزینه ارسال</span>
                            <span><?php echo e(number_format($order->shipping_cost)); ?> تومان</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between font-semibold text-lg text-gray-800">
                            <span>مبلغ کل</span>
                            <span><?php echo e(number_format($order->total)); ?> تومان</span>
                        </div>
                    </div>
                </div>

                <!-- Buyer/Seller Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">
                        <?php if($order->buyer_id === auth()->id()): ?>
                            اطلاعات فروشنده
                        <?php else: ?>
                            اطلاعات خریدار
                        <?php endif; ?>
                    </h2>
                    <?php
                        $otherUser = $order->buyer_id === auth()->id() ? $order->seller : $order->buyer;
                    ?>
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center ml-3">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800"><?php echo e($otherUser->name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($otherUser->email); ?></p>
                        </div>
                    </div>
                    <?php if($otherUser->phone): ?>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-phone ml-2 text-gray-400"></i>
                            <span class="text-sm"><?php echo e($otherUser->phone); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                    // Check if this is an auction order and can be released early
                    $isAuctionOrder = $order->items->first()?->listing?->required_deposit > 0;
                    $canReleaseEarly = $order->buyer_id === auth()->id() 
                        && $order->status === 'delivered' 
                        && $isAuctionOrder
                        && !$order->payment_released_at;
                ?>

                <?php if($canReleaseEarly): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">آزادسازی پول فروشنده</h2>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 text-xl ml-3 mt-1"></i>
                                <div class="text-sm text-gray-700">
                                    <p class="mb-2">
                                        اگر کالا را دریافت کرده‌اید و از کیفیت آن راضی هستید، می‌توانید پول فروشنده را زودتر آزاد کنید.
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        با آزادسازی، کمیسیون کسر شده و مابقی به فروشنده واریز می‌شود.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <form action="<?php echo e(route('orders.releasePayment', $order)); ?>" method="POST" id="releasePaymentForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('POST'); ?>
                            <button type="button" onclick="showReleasePaymentModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check-circle ml-2"></i>
                                آزادسازی پول فروشنده
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- Buyer confirm delivery -->
                <?php if($order->buyer_id === auth()->id() && $order->status === 'shipped'): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">تایید دریافت کالا</h2>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-box-open text-green-600 text-xl mt-1"></i>
                                <div class="text-sm text-gray-700">
                                    <p class="mb-2">
                                        آیا کالا را دریافت کرده‌اید؟ با تایید دریافت، پول به فروشنده واریز می‌شود.
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        لطفاً قبل از تایید، از سلامت و کیفیت کالا اطمینان حاصل کنید.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <form action="<?php echo e(route('orders.updateStatus', $order)); ?>" method="POST" id="confirmDeliveryForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="delivered">
                            <button type="button" onclick="showConfirmDeliveryModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check-circle ml-2"></i>
                                تایید دریافت کالا
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if($order->seller_id === auth()->id() && $order->status === 'processing'): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">ارسال سفارش</h2>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                                <div class="text-sm text-gray-700">
                                    <p class="mb-2">
                                        پس از تهیه کامل اقلام، کد رهگیری مرسوله را وارد کنید تا سفارش به مرحله ارسال برود.
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        در صورت لغو سفارش، جریمه لغو از کیف پول شما کسر خواهد شد.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form action="<?php echo e(route('orders.addTracking', $order)); ?>" method="POST" class="mb-3">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    کد رهگیری مرسوله
                                </label>
                                <input type="text" 
                                       id="tracking_number" 
                                       name="tracking_number" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="کد رهگیری پست یا باربری را وارد کنید"
                                       required>
                                <?php $__errorArgs = ['tracking_number'];
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
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-shipping-fast ml-2"></i>
                                ثبت کد رهگیری و ارسال سفارش
                            </button>
                        </form>

                        <button type="button" onclick="showCancelOrderModal('seller')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times-circle ml-2"></i>
                            لغو سفارش
                        </button>
                    </div>
                <?php endif; ?>

                <?php if($order->buyer_id === auth()->id() && $order->status === 'processing'): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">لغو سفارش</h2>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-1"></i>
                                <div class="text-sm text-gray-700">
                                    <p class="mb-2">
                                        در صورت لغو سفارش، جریمه لغو از کیف پول شما کسر خواهد شد.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="showCancelOrderModal('buyer')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times-circle ml-2"></i>
                            لغو سفارش
                        </button>
                    </div>
                <?php endif; ?>

                <?php if($order->seller_id === auth()->id() && $order->status === 'pending'): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">تغییر وضعیت سفارش</h2>
                        <p class="text-sm text-gray-600 mb-4">سفارش در انتظار پردازش است. می‌توانید آن را به مرحله پردازش ببرید.</p>
                        <form action="<?php echo e(route('orders.updateStatus', $order)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="processing">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-arrow-left ml-2"></i>
                                انتقال به مرحله پردازش
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delivery Modal -->
<div id="confirmDeliveryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">تایید دریافت کالا</h3>
            <p class="text-gray-600">آیا کالا را دریافت کرده‌اید و از کیفیت آن راضی هستید؟</p>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                <div class="text-sm text-yellow-800">
                    <p class="font-bold mb-1">توجه:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>با تایید، پول به فروشنده واریز می‌شود</li>
                        <li>این عملیات قابل بازگشت نیست</li>
                        <li>لطفاً از سلامت کالا اطمینان حاصل کنید</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button type="button" onclick="closeConfirmDeliveryModal()" 
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-bold">
                انصراف
            </button>
            <button type="button" onclick="submitConfirmDelivery()" 
                    class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-bold">
                تایید دریافت
            </button>
        </div>
    </div>
</div>

<!-- Release Payment Modal -->
<div id="releasePaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-money-bill-wave text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">آزادسازی پول فروشنده</h3>
            <p class="text-gray-600">آیا از آزادسازی پول فروشنده اطمینان دارید؟</p>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle text-red-600 mt-1"></i>
                <div class="text-sm text-red-800">
                    <p class="font-bold mb-2">هشدار مهم:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>این عملیات قابل بازگشت نیست</li>
                        <li>پول بلافاصله به فروشنده واریز می‌شود</li>
                        <li>کمیسیون سایت کسر خواهد شد</li>
                        <li>فقط در صورت رضایت کامل اقدام کنید</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button type="button" onclick="closeReleasePaymentModal()" 
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-bold">
                انصراف
            </button>
            <button type="button" onclick="submitReleasePayment()" 
                    class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-bold">
                تایید آزادسازی
            </button>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">لغو سفارش</h3>
            <p class="text-gray-600">آیا مطمئن هستید که می‌خواهید این سفارش را لغو کنید؟</p>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-bold mb-2">توجه:</p>
                    <p class="mb-2">با لغو سفارش، جریمه زیر از کیف پول شما کسر خواهد شد:</p>
                    <p class="text-lg font-bold text-red-600" id="penaltyAmount">محاسبه...</p>
                </div>
            </div>
        </div>
        
        <form id="cancelOrderForm" action="<?php echo e(route('orders.cancelWithPenalty', $order)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelOrderModal()" 
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-bold">
                    انصراف
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-bold">
                    تایید لغو
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showConfirmDeliveryModal() {
    document.getElementById('confirmDeliveryModal').classList.remove('hidden');
}

function closeConfirmDeliveryModal() {
    document.getElementById('confirmDeliveryModal').classList.add('hidden');
}

function submitConfirmDelivery() {
    document.getElementById('confirmDeliveryForm').submit();
}

function showReleasePaymentModal() {
    document.getElementById('releasePaymentModal').classList.remove('hidden');
}

function closeReleasePaymentModal() {
    document.getElementById('releasePaymentModal').classList.add('hidden');
}

function submitReleasePayment() {
    document.getElementById('releasePaymentForm').submit();
}

// Close modals on outside click
document.getElementById('confirmDeliveryModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmDeliveryModal();
    }
});

document.getElementById('releasePaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReleasePaymentModal();
    }
});

document.getElementById('cancelOrderModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelOrderModal();
    }
});

function showCancelOrderModal(userType) {
    // Calculate penalty
    const orderTotal = <?php echo e($order->total); ?>;
    const penaltyType = '<?php echo e(\App\Models\SiteSetting::get("order_cancellation_penalty_type", "percentage")); ?>';
    const penaltyValue = <?php echo e(\App\Models\SiteSetting::get('order_cancellation_penalty_value', 10)); ?>;
    
    let penalty = 0;
    if (penaltyType === 'percentage') {
        penalty = (orderTotal * penaltyValue) / 100;
    } else {
        penalty = penaltyValue;
    }
    
    // Format penalty with Persian numbers
    const penaltyFormatted = new Intl.NumberFormat('fa-IR').format(penalty) + ' تومان';
    document.getElementById('penaltyAmount').textContent = penaltyFormatted;
    
    document.getElementById('cancelOrderModal').classList.remove('hidden');
}

function closeCancelOrderModal() {
    document.getElementById('cancelOrderModal').classList.add('hidden');
}

// Countdown Timer for Test Period
const countdownElement = document.getElementById('countdown-timer');
if (countdownElement) {
    const deadline = parseInt(countdownElement.dataset.deadline) * 1000; // Convert to milliseconds
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = deadline - now;
        
        if (distance < 0) {
            document.getElementById('days').textContent = '۰۰';
            document.getElementById('hours').textContent = '۰۰';
            document.getElementById('minutes').textContent = '۰۰';
            document.getElementById('seconds').textContent = '۰۰';
            
            // Change colors to red when expired
            document.querySelectorAll('#countdown-timer .bg-blue-600').forEach(el => {
                el.classList.remove('bg-blue-600');
                el.classList.add('bg-red-600');
            });
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Convert to Persian numbers
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        function toPersian(num) {
            return num.toString().padStart(2, '0').split('').map(d => persianNumbers[parseInt(d)]).join('');
        }
        
        document.getElementById('days').textContent = toPersian(days);
        document.getElementById('hours').textContent = toPersian(hours);
        document.getElementById('minutes').textContent = toPersian(minutes);
        document.getElementById('seconds').textContent = toPersian(seconds);
        
        // Change color to orange when less than 24 hours
        if (distance < 24 * 60 * 60 * 1000) {
            document.querySelectorAll('#countdown-timer .bg-blue-600').forEach(el => {
                el.classList.remove('bg-blue-600');
                el.classList.add('bg-orange-500');
            });
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
}
</script>
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
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/orders/show.blade.php ENDPATH**/ ?>