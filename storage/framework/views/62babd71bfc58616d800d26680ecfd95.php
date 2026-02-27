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
     <?php $__env->slot('title', null, []); ?> سفارشات من <?php $__env->endSlot(); ?>
     <?php $__env->slot('pageTitle', null, []); ?> سفارشات من <?php $__env->endSlot(); ?>

    <div class="space-y-6">
            <!-- Tabs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <a href="<?php echo e(route('orders.index', ['role' => 'buyer'])); ?>" 
                           class="py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors flex items-center gap-2 <?php echo e($role === 'buyer' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            <span class="material-symbols-outlined text-xl">shopping_bag</span>
                            <span>خریدهای من</span>
                            <?php if($role === 'buyer'): ?>
                                <span class="mr-2 bg-blue-100 text-blue-600 py-1 px-2 rounded-full text-xs font-bold"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($orders->count()); ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?php echo e(route('orders.index', ['role' => 'seller'])); ?>" 
                           class="py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors flex items-center gap-2 <?php echo e($role === 'seller' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                            <span class="material-symbols-outlined text-xl">store</span>
                            <span>فروش‌های من</span>
                            <?php if($role === 'seller'): ?>
                                <span class="mr-2 bg-blue-100 text-blue-600 py-1 px-2 rounded-full text-xs font-bold"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($orders->count()); ?></span>
                            <?php endif; ?>
                        </a>
                    </nav>
                </div>
            </div>

            <?php if($orders->isEmpty()): ?>
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-gray-400" style="font-size: 60px;">receipt_long</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">
                            <?php if($role === 'buyer'): ?>
                                هنوز سفارشی ثبت نکرده‌اید
                            <?php else: ?>
                                هنوز سفارشی دریافت نکرده‌اید
                            <?php endif; ?>
                        </h3>
                        <p class="text-gray-600 mb-6">
                            <?php if($role === 'buyer'): ?>
                                برای خرید محصولات، به صفحه اصلی بروید و مزایده‌ها را مشاهده کنید.
                            <?php else: ?>
                                وقتی کاربران از شما خرید کنند، سفارشات اینجا نمایش داده می‌شود.
                            <?php endif; ?>
                        </p>
                        <?php if($role === 'buyer'): ?>
                            <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                                <span class="material-symbols-outlined">home</span>
                                رفتن به صفحه اصلی
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="space-y-4">
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- Order Header -->
                                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-4 space-x-reverse">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800">
                                                سفارش #<?php echo e($order->order_number); ?>

                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                <?php echo e(\Morilog\Jalali\Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i')); ?>

                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <!-- Status Badge -->
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
                                        <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo e($status['class']); ?>">
                                            <?php echo e($status['text']); ?>

                                        </span>
                                    </div>
                                </div>

                                <!-- Order Info -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-blue-600">person</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                <?php if($role === 'buyer'): ?>
                                                    فروشنده
                                                <?php else: ?>
                                                    خریدار
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-sm font-medium text-gray-800">
                                                <?php if($role === 'buyer'): ?>
                                                    <?php echo e($order->seller->name); ?>

                                                <?php else: ?>
                                                    <?php echo e($order->buyer->name); ?>

                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-green-600">inventory_2</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">تعداد اقلام</p>
                                            <p class="text-sm font-medium text-gray-800"><?php echo app(\App\Services\PersianNumberService::class)->toPersian($order->items->count()); ?> مورد</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center ml-3">
                                            <span class="material-symbols-outlined text-purple-600">payments</span>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">مبلغ کل</p>
                                            <p class="text-sm font-bold text-gray-800"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($order->total)); ?> تومان</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items Preview -->
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="space-y-2">
                                        <?php $__currentLoopData = $order->items->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <?php if($item->listing->images->isNotEmpty()): ?>
                                                        <img src="<?php echo e(url('storage/' . $item->listing->images->first()->file_path)); ?>" 
                                                             alt="<?php echo e($item->listing->title); ?>"
                                                             class="w-12 h-12 object-cover rounded ml-3">
                                                    <?php else: ?>
                                                        <div class="w-12 h-12 bg-gray-200 rounded ml-3 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400">image</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800"><?php echo e($item->listing->title); ?></p>
                                                        <p class="text-xs text-gray-500">تعداد: <?php echo app(\App\Services\PersianNumberService::class)->toPersian($item->quantity); ?></p>
                                                    </div>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800"><?php echo app(\App\Services\PersianNumberService::class)->toPersian(number_format($item->subtotal)); ?> تومان</p>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <?php if($order->items->count() > 2): ?>
                                            <p class="text-xs text-gray-500 text-center pt-2">
                                                و <?php echo app(\App\Services\PersianNumberService::class)->toPersian($order->items->count() - 2); ?> مورد دیگر...
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <?php if($order->tracking_number): ?>
                                            <span class="text-sm text-gray-600 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">local_shipping</span>
                                                کد رهگیری: <span class="font-medium"><?php echo e($order->tracking_number); ?></span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" 
                                           class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium flex items-center gap-1 shadow-lg shadow-blue-500/20">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                            مشاهده جزئیات
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal895f6ef515592ffd4805667c75b9d7a7)): ?>
<?php $attributes = $__attributesOriginal895f6ef515592ffd4805667c75b9d7a7; ?>
<?php unset($__attributesOriginal895f6ef515592ffd4805667c75b9d7a7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal895f6ef515592ffd4805667c75b9d7a7)): ?>
<?php $component = $__componentOriginal895f6ef515592ffd4805667c75b9d7a7; ?>
<?php unset($__componentOriginal895f6ef515592ffd4805667c75b9d7a7); ?>
<?php endif; ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/orders/index.blade.php ENDPATH**/ ?>