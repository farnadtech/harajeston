

<?php $__env->startSection('title', 'جزئیات سفارش #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
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
                        'shipped' => ['text' => 'ارسال شده', 'class' => 'bg-purple-100 text-purple-800'],
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
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">کد رهگیری مرسوله</p>
                                <p class="text-lg font-mono font-semibold text-gray-800"><?php echo e($order->tracking_number); ?></p>
                            </div>
                            <button onclick="navigator.clipboard.writeText('<?php echo e($order->tracking_number); ?>')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-copy ml-1"></i>
                                کپی
                            </button>
                        </div>
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

                <!-- Actions -->
                <?php if($order->buyer_id === auth()->id() && $order->canBeCancelled()): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">عملیات</h2>
                        <form action="<?php echo e(route('orders.cancel', $order)); ?>" method="POST" 
                              onsubmit="return confirm('آیا از لغو این سفارش اطمینان دارید؟')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('POST'); ?>
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-times-circle ml-2"></i>
                                لغو سفارش
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2 text-center">
                            امکان لغو تا 1 ساعت پس از ثبت سفارش
                        </p>
                    </div>
                <?php endif; ?>

                <?php if($order->seller_id === auth()->id() && in_array($order->status, ['pending', 'processing'])): ?>
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">به‌روزرسانی وضعیت</h2>
                        <form action="<?php echo e(route('orders.updateStatus', $order)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending" <?php echo e($order->status === 'pending' ? 'selected' : ''); ?>>در انتظار پردازش</option>
                                <option value="processing" <?php echo e($order->status === 'processing' ? 'selected' : ''); ?>>در حال پردازش</option>
                                <option value="shipped" <?php echo e($order->status === 'shipped' ? 'selected' : ''); ?>>ارسال شده</option>
                                <option value="delivered" <?php echo e($order->status === 'delivered' ? 'selected' : ''); ?>>تحویل داده شده</option>
                            </select>
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-save ml-2"></i>
                                ذخیره تغییرات
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/orders/show.blade.php ENDPATH**/ ?>