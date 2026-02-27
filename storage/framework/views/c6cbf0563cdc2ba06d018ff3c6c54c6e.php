

<?php $__env->startSection('title', 'مدیریت مزایده - ' . $listing->title); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(url('css/persian-datepicker-package.css')); ?>?v=<?php echo e(now()->timestamp); ?>">
<style>
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    .image-preview-hover:hover .image-overlay {
        opacity: 1;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                <?php echo e($listing->title); ?>

                <?php if($listing->status === 'active'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        فعال
                    </span>
                <?php elseif($listing->status === 'pending'): ?>
                    <?php
                        $startsAt = \Carbon\Carbon::parse($listing->starts_at);
                        $isApproved = !is_null($listing->approved_at);
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <?php if(!$isApproved): ?>
                            منتظر تایید ادمین
                        <?php elseif($startsAt->isFuture()): ?>
                            در انتظار شروع
                        <?php else: ?>
                            منتظر تایید
                        <?php endif; ?>
                    </span>
                <?php elseif($listing->status === 'completed' || $listing->status === 'ended'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        تمام شده
                    </span>
                <?php elseif($listing->status === 'suspended'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        معلق شده
                    </span>
                <?php elseif($listing->status === 'failed'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        ناموفق
                    </span>
                <?php endif; ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                شناسه مزایده: <span class="font-mono text-gray-700">#<?php echo e($listing->id); ?>-A</span> • 
                تاریخ ایجاد: <span dir="ltr"><?php echo e(\App\Services\JalaliDateService::toJalali($listing->created_at)); ?></span>
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            <button onclick="openEditModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                ویرایش جزئیات
            </button>
            
            <?php if($listing->status === 'pending'): ?>
            <?php if(is_null($listing->approved_at)): ?>
            <button onclick="confirmApprove()" class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg hover:bg-green-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                تایید و انتشار
            </button>
            
            <button onclick="confirmReject()" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                رد کردن
            </button>
            <?php else: ?>
            <span class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg flex items-center gap-2 text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">schedule</span>
                تایید شده - در انتظار شروع
            </span>
            <?php endif; ?>
            <?php elseif($listing->status === 'active'): ?>
            <button onclick="confirmEndEarly()" class="px-4 py-2 bg-orange-50 border border-orange-200 text-orange-700 rounded-lg hover:bg-orange-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">timer_off</span>
                پایان زودتر
            </button>
            
            <button onclick="confirmSuspend()" class="px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg hover:bg-red-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">block</span>
                توقیف مزایده
            </button>
            <?php elseif($listing->status === 'suspended'): ?>
            <button onclick="confirmActivate()" class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg hover:bg-green-100 flex items-center gap-2 text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                فعال‌سازی مجدد
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if($listing->pendingChanges && $listing->pendingChanges->count() > 0): ?>
    <!-- Pending Changes Alert -->
    <div class="bg-orange-50 border-r-4 border-orange-500 rounded-2xl p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-orange-600 text-2xl">pending_actions</span>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-orange-900 mb-2">تغییرات در انتظار تایید</h3>
                <p class="text-sm text-orange-700 mb-4">
                    فروشنده تغییراتی در این آگهی ایجاد کرده که نیاز به بررسی و تایید شما دارد.
                </p>
                
                <?php $__currentLoopData = $listing->pendingChanges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl border border-orange-200 p-4 mb-3">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-600">edit_note</span>
                            <span class="font-bold text-gray-900">تغییرات ثبت شده در <?php echo e(\App\Services\JalaliDateService::toJalali($change->created_at)); ?></span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="approvePendingChange(<?php echo e($change->id); ?>)" 
                                    class="px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-1 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                                تایید
                            </button>
                            <button onclick="rejectPendingChange(<?php echo e($change->id); ?>)" 
                                    class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center gap-1 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                رد
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <?php
                            $changes = $change->changes;
                            
                            // تعریف نام فارسی فیلدها
                            $fieldLabels = [
                                'title' => 'عنوان',
                                'description' => 'توضیحات',
                                'category_id' => 'دسته‌بندی',
                                'condition' => 'وضعیت کالا',
                                'tags' => 'برچسب‌ها',
                                'starting_price' => 'قیمت شروع',
                                'buy_now_price' => 'قیمت خرید فوری',
                                'starts_at' => 'زمان شروع',
                                'ends_at' => 'زمان پایان',
                                'auto_extend' => 'تمدید خودکار',
                                'main_image_id' => 'تصویر اصلی',
                            ];
                        ?>
                        
                        <?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!in_array($field, ['attributes', 'shipping_methods', 'shipping_costs', 'deleted_images', 'images'])): ?>
                                <?php
                                    $oldValue = $listing->$field ?? null;
                                    
                                    // ذخیره مقادیر اصلی برای استفاده در فرمت کردن
                                    $oldValueOriginal = $oldValue;
                                    $newValueOriginal = $newValue;
                                    
                                    // مقایسه دقیق برای تاریخ‌ها
                                    if (in_array($field, ['starts_at', 'ends_at'])) {
                                        $oldValueTimestamp = $oldValue ? \Carbon\Carbon::parse($oldValue)->timestamp : null;
                                        $newValueTimestamp = $newValue ? \Carbon\Carbon::parse($newValue)->timestamp : null;
                                        $hasChanged = $oldValueTimestamp != $newValueTimestamp;
                                    } elseif (is_array($oldValue) || is_array($newValue)) {
                                        // مقایسه آرایه‌ها
                                        $hasChanged = json_encode($oldValue) != json_encode($newValue);
                                    } else {
                                        $hasChanged = $oldValue != $newValue;
                                    }
                                    
                                    if (!$hasChanged) continue;
                                    
                                    // فرمت کردن مقادیر برای نمایش
                                    $oldValueFormatted = $oldValue;
                                    $newValueFormatted = $newValue;
                                    
                                    // فرمت کردن تاریخ‌ها - استفاده از مقادیر اصلی
                                    if (in_array($field, ['starts_at', 'ends_at'])) {
                                        if ($oldValueOriginal) {
                                            $oldValueFormatted = \App\Services\JalaliDateService::toJalali($oldValueOriginal);
                                        }
                                        if ($newValueOriginal) {
                                            $newValueFormatted = \App\Services\JalaliDateService::toJalali($newValueOriginal);
                                        }
                                    }
                                    // فرمت کردن قیمت‌ها
                                    elseif (in_array($field, ['starting_price', 'buy_now_price'])) {
                                        $oldValueFormatted = number_format($oldValue) . ' تومان';
                                        $newValueFormatted = number_format($newValue) . ' تومان';
                                    }
                                    // فرمت کردن boolean
                                    elseif ($field === 'auto_extend') {
                                        $oldValueFormatted = $oldValue ? 'فعال' : 'غیرفعال';
                                        $newValueFormatted = $newValue ? 'فعال' : 'غیرفعال';
                                    }
                                    // فرمت کردن آرایه images
                                    elseif ($field === 'images') {
                                        $oldValueFormatted = is_array($oldValue) ? count($oldValue) . ' تصویر' : '-';
                                        $newValueFormatted = is_array($newValue) ? count($newValue) . ' تصویر' : '-';
                                    }
                                    // تبدیل آرایه‌های باقیمونده به رشته
                                    else {
                                        if (is_array($oldValue)) {
                                            $oldValueFormatted = empty($oldValue) ? '-' : implode(', ', array_map(function($v) {
                                                return is_scalar($v) ? $v : json_encode($v);
                                            }, $oldValue));
                                        } else {
                                            $oldValueFormatted = $oldValue ?? '-';
                                        }
                                        
                                        if (is_array($newValue)) {
                                            $newValueFormatted = empty($newValue) ? '-' : implode(', ', array_map(function($v) {
                                                return is_scalar($v) ? $v : json_encode($v);
                                            }, $newValue));
                                        } else {
                                            $newValueFormatted = $newValue ?? '-';
                                        }
                                    }
                                ?>
                                
                                <?php
                                    // تبدیل نهایی به رشته برای اطمینان
                                    $oldValueFormatted = is_string($oldValueFormatted) ? $oldValueFormatted : (string)$oldValueFormatted;
                                    $newValueFormatted = is_string($newValueFormatted) ? $newValueFormatted : (string)$newValueFormatted;
                                ?>
                                
                                <div class="flex gap-4 py-2 border-b border-gray-100 last:border-0">
                                    <div class="w-32 font-medium text-gray-600">
                                        <?php echo e($fieldLabels[$field] ?? $field); ?>:
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-red-600 line-through mb-1">
                                            <?php if($field === 'category_id'): ?>
                                                <?php echo e(\App\Models\Category::find($oldValue)->name ?? 'بدون دسته'); ?>

                                            <?php elseif($field === 'condition'): ?>
                                                <?php echo e(condition_label($oldValue)); ?>

                                            <?php elseif($field === 'description'): ?>
                                                <?php echo e(\Str::limit($oldValueFormatted, 100)); ?>

                                            <?php elseif($field === 'main_image_id'): ?>
                                                <?php
                                                    $oldImage = \App\Models\ListingImage::find($oldValue);
                                                ?>
                                                <?php if($oldImage): ?>
                                                    <div class="flex items-center gap-2">
                                                        <img src="<?php echo e(url('storage/' . $oldImage->file_path)); ?>" class="w-16 h-16 object-cover rounded-lg border">
                                                        <span class="text-xs text-gray-500">تصویر قبلی</span>
                                                    </div>
                                                <?php else: ?>
                                                    تصویر #<?php echo e($oldValue); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo e($oldValueFormatted ?? '-'); ?>

                                            <?php endif; ?>
                                        </div>
                                        <div class="text-green-600 font-medium">
                                            <?php if($field === 'category_id'): ?>
                                                <?php echo e(\App\Models\Category::find($newValue)->name ?? 'بدون دسته'); ?>

                                            <?php elseif($field === 'condition'): ?>
                                                <?php echo e(condition_label($newValue)); ?>

                                            <?php elseif($field === 'description'): ?>
                                                <?php echo e(\Str::limit($newValueFormatted, 100)); ?>

                                            <?php elseif($field === 'main_image_id'): ?>
                                                <?php
                                                    $newImage = \App\Models\ListingImage::find($newValue);
                                                ?>
                                                <?php if($newImage): ?>
                                                    <div class="flex items-center gap-2">
                                                        <img src="<?php echo e(url('storage/' . $newImage->file_path)); ?>" class="w-16 h-16 object-cover rounded-lg border border-green-500">
                                                        <span class="text-xs text-gray-500">تصویر جدید</span>
                                                    </div>
                                                <?php else: ?>
                                                    تصویر #<?php echo e($newValue); ?>

                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo e($newValueFormatted ?? '-'); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(isset($changes['shipping_methods'])): ?>
                        <div class="flex gap-4 py-2 border-b border-gray-100 last:border-0">
                            <div class="w-32 font-medium text-gray-600">روش‌های ارسال:</div>
                            <div class="flex-1">
                                <?php
                                    $oldMethods = $listing->shippingMethods->pluck('id')->toArray();
                                    $newMethods = is_array($changes['shipping_methods']) ? array_keys($changes['shipping_methods']) : [];
                                    sort($oldMethods);
                                    sort($newMethods);
                                    $methodsChanged = $oldMethods != $newMethods;
                                ?>
                                
                                <?php if($methodsChanged): ?>
                                    <div class="text-red-600 line-through mb-1">
                                        <?php $__currentLoopData = $listing->shippingMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs ml-1 mb-1"><?php echo e($method->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    <div class="text-green-600 font-medium">
                                        <?php $__currentLoopData = $newMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $method = \App\Models\ShippingMethod::find($methodId);
                                            ?>
                                            <?php if($method): ?>
                                                <span class="inline-block px-2 py-1 bg-green-100 rounded text-xs ml-1 mb-1"><?php echo e($method->name); ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($changes['attributes'])): ?>
                        <div class="flex gap-4 py-2 border-b border-gray-100">
                            <div class="w-32 font-medium text-gray-600">مشخصات فنی:</div>
                            <div class="flex-1">
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $changes['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attrId => $newAttrValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $attribute = \App\Models\CategoryAttribute::find($attrId);
                                            $oldAttrValue = $listing->attributeValues()->where('category_attribute_id', $attrId)->first();
                                        ?>
                                        <?php if($attribute): ?>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-700"><?php echo e($attribute->name); ?>:</span>
                                                <?php if($oldAttrValue): ?>
                                                    <span class="text-red-600 line-through mx-2"><?php echo e($oldAttrValue->value); ?></span>
                                                <?php endif; ?>
                                                <span class="text-green-600 font-medium"><?php echo e($newAttrValue); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(isset($changes['images'])): ?>
                        <div class="flex gap-4 py-2 border-b border-gray-100">
                            <div class="w-32 font-medium text-gray-600">تصاویر:</div>
                            <div class="flex-1 space-y-3">
                                <?php if($listing->images->count() > 0): ?>
                                <div>
                                    <div class="text-xs text-gray-500 mb-2">تصاویر قبلی:</div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <?php $__currentLoopData = $listing->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oldImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="relative group">
                                                <img src="<?php echo e(url('storage/' . $oldImage->file_path)); ?>" 
                                                     class="w-full h-24 object-cover rounded-lg border opacity-50">
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <div class="text-xs text-green-600 font-medium mb-2">تصاویر جدید (<?php echo e(count($changes['images'])); ?> تصویر):</div>
                                    <div class="grid grid-cols-4 gap-2">
                                        <?php $__currentLoopData = $changes['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imageData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $image = \App\Models\ListingImage::find($imageData['id'] ?? null);
                                            ?>
                                            <?php if($image): ?>
                                                <div class="relative group">
                                                    <img src="<?php echo e(url('storage/' . $image->file_path)); ?>" 
                                                         class="w-full h-24 object-cover rounded-lg border-2 border-green-500">
                                                    <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-2 py-0.5 rounded">
                                                        جدید
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Product Details Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Images Section -->
                    <div class="space-y-4">
                        <div class="aspect-square rounded-xl bg-gray-100 overflow-hidden border border-gray-200 relative group image-preview-hover">
                            <?php
                                $mainImage = $listing->images->sortBy('display_order')->first();
                            ?>
                            <?php if($mainImage): ?>
                                <img src="<?php echo e(url('storage/' . $mainImage->file_path)); ?>" 
                                     alt="<?php echo e($listing->title); ?>" 
                                     class="w-full h-full object-cover"
                                     id="mainImage">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-6xl">image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="image-overlay absolute inset-0 bg-black/40 opacity-0 transition-opacity flex items-center justify-center gap-2">
                                <button onclick="viewImage()" class="p-2 bg-white rounded-full text-gray-700 hover:text-primary">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                                <button onclick="deleteMainImage()" class="p-2 bg-white rounded-full text-gray-700 hover:text-red-500">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 max-h-[400px] overflow-y-auto">
                            <?php $__currentLoopData = $listing->images->sortBy('display_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="aspect-square rounded-lg bg-gray-100 overflow-hidden border <?php echo e($image->display_order === 1 ? 'border-primary ring-2 ring-primary ring-offset-2' : 'border-gray-200'); ?> cursor-pointer hover:border-primary transition-colors relative group"
                                 data-image-id="<?php echo e($image->id); ?>"
                                 onclick="changeMainImage('<?php echo e(url('storage/' . $image->file_path)); ?>', <?php echo e($image->id); ?>, this)">
                                <img src="<?php echo e(url('storage/' . $image->file_path)); ?>" 
                                     class="w-full h-full object-cover <?php echo e($image->display_order === 1 ? '' : 'grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all'); ?>">
                                
                                <?php if($image->display_order === 1): ?>
                                <div class="absolute top-1 right-1 bg-primary text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                                    عکس اصلی
                                </div>
                                <?php else: ?>
                                <button onclick="event.stopPropagation(); setAsMainImage(<?php echo e($image->id); ?>)" 
                                        class="absolute top-1 right-1 p-1.5 bg-blue-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-blue-600"
                                        title="تنظیم به عنوان عکس اصلی">
                                    <span class="material-symbols-outlined text-sm">star</span>
                                </button>
                                <?php endif; ?>
                                
                                <button onclick="event.stopPropagation(); deleteThumbnailImage(<?php echo e($image->id); ?>)" 
                                        class="absolute top-1 left-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                                        title="حذف تصویر">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if($listing->images->count() < 8): ?>
                            <div class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 border-dashed text-gray-400 hover:text-primary hover:border-primary cursor-pointer transition-colors"
                                 onclick="document.getElementById('newImageInput').click()">
                                <span class="material-symbols-outlined">add_photo_alternate</span>
                            </div>
                            <?php endif; ?>
                            <input type="file" id="newImageInput" class="hidden" accept="image/*" onchange="uploadNewImage(this)">
                        </div>
                    </div>
                    
                    <!-- Product Info Section -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">توضیحات محصول</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mt-2 text-justify">
                                <?php echo e($listing->description); ?>

                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">دسته‌بندی</span>
                                <span class="font-bold text-gray-900 text-sm"><?php echo e($listing->category ? $listing->category->name : 'بدون دسته‌بندی'); ?></span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">وضعیت کالا</span>
                                <span class="font-bold text-gray-900 text-sm"><?php echo e(condition_label($listing->condition)); ?></span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">نوع فروش</span>
                                <span class="font-bold text-gray-900 text-sm">
                                    <?php if($listing->type === 'auction'): ?>
                                        مزایده
                                    <?php elseif($listing->type === 'direct_sale'): ?>
                                        فروش مستقیم
                                    <?php else: ?>
                                        مزایده + خرید فوری
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <span class="block text-xs text-gray-500 mb-1">محل کالا</span>
                                <span class="font-bold text-gray-900 text-sm"><?php echo e($listing->store->city ?? 'تهران'); ?></span>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-gray-900 mb-3 text-sm">برچسب‌ها</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                    $tags = is_array($listing->tags) ? $listing->tags : [];
                                ?>
                                <?php if(count($tags) > 0): ?>
                                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium">
                                        #<?php echo e(trim($tag)); ?>

                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">برچسبی تعریف نشده است</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">برای ویرایش برچسب‌ها از دکمه "ویرایش جزئیات" استفاده کنید.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Auction Settings Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">tune</span>
                        تنظیمات مزایده
                    </h3>
                    <button onclick="saveAuctionSettings()" class="text-sm text-primary font-bold hover:underline">
                        ذخیره تغییرات
                    </button>
                </div>
                
                <form id="auctionSettingsForm">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- زمان شروع - فقط برای حراجی‌های pending یا آینده -->
                        <?php if($listing->isPending()): ?>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">زمان شروع</label>
                            <input type="text" 
                                   name="starts_at" 
                                   id="manage_starts_at"
                                   value="<?php echo e(\App\Services\JalaliDateService::toDatepickerFormat($listing->starts_at)); ?>"
                                   class="persian-datepicker-input w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-primary focus:border-primary"
                                   placeholder="انتخاب تاریخ و زمان"
                                   autocomplete="off">
                            <p class="text-[10px] text-blue-600 mt-1">
                                <span class="material-symbols-outlined text-[12px] align-middle">info</span>
                                قابل تغییر تا زمان شروع حراجی
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت پایه (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="starting_price" 
                                       id="starting_price"
                                       value="<?php echo e(number_format($listing->starting_price)); ?>"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت رزرو (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="reserve_price" 
                                       id="reserve_price"
                                       value="<?php echo e($listing->reserve_price ? number_format($listing->reserve_price) : ''); ?>"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">گام افزایش (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="bid_increment" 
                                       id="bid_increment"
                                       value="<?php echo e(number_format($listing->bid_increment)); ?>"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">زمان پایان</label>
                            <input type="text" 
                                   name="ends_at" 
                                   id="manage_ends_at"
                                   value="<?php echo e(\App\Services\JalaliDateService::toDatepickerFormat($listing->ends_at)); ?>"
                                   class="persian-datepicker-input w-full bg-gray-50 border-gray-200 rounded-xl text-sm focus:ring-primary focus:border-primary"
                                   placeholder="انتخاب تاریخ و زمان"
                                   autocomplete="off">
                        </div>
                        
                        <?php if($listing->buy_now_price): ?>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">قیمت خرید فوری (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="buy_now_price" 
                                       id="buy_now_price"
                                       value="<?php echo e(number_format($listing->buy_now_price)); ?>"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-700">مبلغ سپرده (تومان)</label>
                            <div class="relative">
                                <input type="text" 
                                       name="deposit_amount" 
                                       id="deposit_amount"
                                       value="<?php echo e(number_format($listing->deposit_amount)); ?>"
                                       class="w-full bg-gray-50 border-gray-200 rounded-xl text-sm font-bold text-left pl-10 focus:ring-primary focus:border-primary">
                                <span class="absolute left-3 top-2.5 text-gray-400 text-xs">IRT</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="auto_extend" 
                                       id="auto_extend"
                                       <?php echo e($listing->auto_extend ? 'checked' : ''); ?>

                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="auto_extend" class="font-medium text-gray-900">تمدید خودکار</label>
                                <p class="text-gray-500 text-xs">اگر پیشنهادی در ۵ دقیقه آخر ثبت شود، زمان مزایده ۵ دقیقه تمدید می‌شود.</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Activity Log Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">history</span>
                    تاریخچه فعالیت‌ها
                </h3>
                
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $activityLogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 text-[18px]"><?php echo e($log->icon ?? 'info'); ?></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($log->action); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?php echo e($log->description); ?></p>
                            <p class="text-xs text-gray-400 mt-1" dir="ltr">
                                <?php echo e(\App\Services\JalaliDateService::toJalali($log->created_at)); ?>

                            </p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2">history</span>
                        <p class="text-sm">هیچ فعالیتی ثبت نشده است</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Bids History Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm flex flex-col h-[500px]">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">gavel</span>
                        تاریخچه پیشنهادات
                    </h3>
                    
                    <div class="mt-3 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500">
                                <?php if($listing->bids->count() > 0): ?>
                                    بالاترین پیشنهاد فعلی
                                <?php else: ?>
                                    قیمت پایه
                                <?php endif; ?>
                            </p>
                            <p class="text-xl font-black text-primary mt-1">
                                <?php
                                    $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
                                    $currentPrice = $highestBid ? $highestBid->amount : $listing->starting_price;
                                ?>
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($currentPrice))); ?>

                                <span class="text-xs font-normal text-gray-400">تومان</span>
                            </p>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-gray-500">تعداد پیشنهادها</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->bids->count())); ?> پیشنهاد
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-2 space-y-2" id="bids-container">
                    <?php $__empty_1 = true; $__currentLoopData = $listing->bids->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-3 <?php echo e($index === 0 ? 'bg-blue-50 border border-blue-100' : 'bg-white border border-gray-100'); ?> rounded-xl transition-all hover:shadow-md group <?php echo e($index > 0 ? 'relative' : ''); ?>">
                        <?php if($index > 0): ?>
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-200 rounded-l-xl group-hover:bg-gray-300"></div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-start mb-2 <?php echo e($index > 0 ? 'pl-2' : ''); ?>">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full <?php echo e($index === 0 ? 'bg-blue-200' : 'bg-gray-100'); ?> flex items-center justify-center <?php echo e($index === 0 ? 'text-blue-700' : 'text-gray-600'); ?> text-xs font-bold">
                                    <?php echo e(mb_substr($bid->user->name, 0, 2)); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-bold <?php echo e($index === 0 ? 'text-gray-900' : 'text-gray-700'); ?>">
                                        <?php echo e($bid->user->name); ?>

                                    </p>
                                    <p class="text-[10px] <?php echo e($index === 0 ? 'text-gray-500' : 'text-gray-400'); ?>">
                                        User ID: #<?php echo e($bid->user->id); ?>

                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] <?php echo e($index === 0 ? 'bg-white px-2 py-1 rounded-full border border-gray-100' : ''); ?> text-gray-500">
                                <?php echo e($bid->created_at->diffForHumans()); ?>

                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center <?php echo e($index > 0 ? 'pl-2' : ''); ?>">
                            <span class="font-bold <?php echo e($index === 0 ? 'text-primary' : 'text-gray-600'); ?> text-sm">
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($bid->amount))); ?> تومان
                            </span>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="cancelBid(<?php echo e($bid->id); ?>)" 
                                        class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg" 
                                        title="ابطال پیشنهاد">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                                <button onclick="contactUser(<?php echo e($bid->user->id); ?>)" 
                                        class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg" 
                                        title="تماس با کاربر">
                                    <span class="material-symbols-outlined text-[16px]">chat</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-symbols-outlined text-4xl mb-2">gavel</span>
                        <p class="text-sm">هنوز پیشنهادی ثبت نشده است</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Seller Info Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">storefront</span>
                    اطلاعات فروشنده
                </h3>
                
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 text-xl font-bold border-2 border-white shadow-sm">
                        <?php echo e(mb_substr($listing->seller->name, 0, 2)); ?>

                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900"><?php echo e($listing->seller->name); ?></h4>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-yellow-400 text-[16px] fill-current">star</span>
                            <span class="text-xs font-bold text-gray-700">
                                <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($listing->seller->seller_rating ?? 0, 1))); ?>

                            </span>
                            <span class="text-xs text-gray-400">
                                (<?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->seller->seller_rating_count ?? 0)); ?> نظر)
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm py-2 border-b border-gray-50">
                        <span class="text-gray-500">وضعیت حساب</span>
                        <span class="<?php echo e($listing->seller->seller_status === 'active' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50'); ?> font-bold px-2 py-0.5 rounded text-xs">
                            <?php if($listing->seller->seller_status === 'active'): ?>
                                تایید شده
                            <?php elseif($listing->seller->seller_status === 'pending'): ?>
                                در انتظار تایید
                            <?php elseif($listing->seller->seller_status === 'suspended'): ?>
                                تعلیق شده
                            <?php else: ?>
                                غیرفعال
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm py-2 border-b border-gray-50">
                        <span class="text-gray-500">شماره تماس</span>
                        <span class="text-gray-900 font-mono"><?php echo e($listing->seller->phone ?? 'ثبت نشده'); ?></span>
                    </div>
                    
                    <div class="flex justify-between items-center text-sm py-2">
                        <span class="text-gray-500">عضویت</span>
                        <span class="text-gray-900" dir="ltr">
                            <?php echo e(\App\Services\JalaliDateService::toJalali($listing->seller->created_at, 'Y/m/d')); ?>

                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mt-5">
                    <a href="<?php echo e(route('admin.users.show', $listing->seller)); ?>" 
                       class="flex items-center justify-center gap-2 py-2 px-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        پروفایل کاربر
                    </a>
                    <button onclick="contactSeller()" 
                            class="flex items-center justify-center gap-2 py-2 px-3 bg-primary/10 hover:bg-primary/20 text-primary rounded-xl text-sm font-medium transition-colors">
                        <span class="material-symbols-outlined text-[18px]">mail</span>
                        ارسال پیام
                    </button>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-gray-500">analytics</span>
                    آمار سریع
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-gray-700">بازدیدها</span>
                        <span class="text-lg font-bold text-blue-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->views ?? 0)); ?>

                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-gray-700">شرکت‌کنندگان</span>
                        <span class="text-lg font-bold text-green-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->participations->count())); ?>

                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <span class="text-sm text-gray-700">علاقه‌مندی‌ها</span>
                        <span class="text-lg font-bold text-purple-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->favorites ?? 0)); ?>

                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                        <span class="text-sm text-gray-700">اشتراک‌گذاری</span>
                        <span class="text-lg font-bold text-orange-600">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listing->shares ?? 0)); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-xl font-bold text-gray-900">ویرایش جزئیات مزایده</h3>
            <button onclick="closeEditModal()" class="p-2 hover:bg-gray-100 rounded-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="editForm" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">عنوان محصول</label>
                <input type="text" 
                       name="title" 
                       value="<?php echo e($listing->title); ?>"
                       class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات</label>
                <textarea name="description" 
                          rows="4"
                          class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"><?php echo e($listing->description); ?></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <?php if (isset($component)) { $__componentOriginalea7437c52847eb24b028938c1b3c6b93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalea7437c52847eb24b028938c1b3c6b93 = $attributes; } ?>
<?php $component = App\View\Components\CategorySelector::resolve(['selected' => $listing->category_id,'name' => 'category_id','label' => 'دسته‌بندی'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('category-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\CategorySelector::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalea7437c52847eb24b028938c1b3c6b93)): ?>
<?php $attributes = $__attributesOriginalea7437c52847eb24b028938c1b3c6b93; ?>
<?php unset($__attributesOriginalea7437c52847eb24b028938c1b3c6b93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalea7437c52847eb24b028938c1b3c6b93)): ?>
<?php $component = $__componentOriginalea7437c52847eb24b028938c1b3c6b93; ?>
<?php unset($__componentOriginalea7437c52847eb24b028938c1b3c6b93); ?>
<?php endif; ?>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">وضعیت کالا</label>
                    <select name="condition" class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <option value="new" <?php echo e($listing->condition === 'new' ? 'selected' : ''); ?>>نو</option>
                        <option value="like_new" <?php echo e($listing->condition === 'like_new' ? 'selected' : ''); ?>>در حد نو</option>
                        <option value="used" <?php echo e($listing->condition === 'used' ? 'selected' : ''); ?>>دست دوم</option>
                    </select>
                </div>
            </div>

            <!-- Attributes Section -->
            <div id="editAttributesSection" style="display: none;" class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-bold text-gray-800 mb-3">ویژگی‌های محصول</h4>
                <div id="editAttributesContainer" class="space-y-3">
                    <!-- ویژگی‌ها به صورت داینامیک اضافه می‌شوند -->
                </div>
            </div>
            
            <!-- Shipping Methods -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-3">روش‌های ارسال * <span class="text-xs text-gray-500">(حداقل یک روش را انتخاب کنید)</span></label>
                <?php
                    $allShippingMethods = \App\Models\ShippingMethod::where('is_active', true)->get();
                    $selectedMethods = $listing->shippingMethods->keyBy('id');
                ?>
                
                <div class="space-y-3" id="editShippingMethodsContainer">
                    <?php $__currentLoopData = $allShippingMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isSelected = $selectedMethods->has($method->id);
                        $currentCost = $isSelected ? ($method->base_cost + $selectedMethods[$method->id]->pivot->custom_cost_adjustment) : $method->base_cost;
                    ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="shipping_methods[]" 
                                   value="<?php echo e($method->id); ?>"
                                   <?php echo e($isSelected ? 'checked' : ''); ?>

                                   class="w-4 h-4 text-primary rounded focus:ring-primary mt-1 edit-shipping-method-checkbox"
                                   onchange="toggleEditPriceInput(this, <?php echo e($method->id); ?>)">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-medium text-gray-900"><?php echo e($method->name); ?></span>
                                        <?php if($method->estimated_days): ?>
                                            <span class="text-xs text-gray-500 mr-2">(<?php echo e(\App\Services\PersianNumberService::convertToPersian($method->estimated_days)); ?> روز)</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        قیمت پایه: <?php echo e(\App\Services\PersianNumberService::convertToPersian(number_format($method->base_cost))); ?> تومان
                                    </span>
                                </div>
                                
                                <div class="price-adjustment-container <?php echo e($isSelected ? '' : 'hidden'); ?>" id="edit-price-container-<?php echo e($method->id); ?>">
                                    <label class="block text-xs text-gray-600 mb-1">قیمت سفارشی برای این محصول (تومان)</label>
                                    <input type="number" 
                                           name="shipping_costs[<?php echo e($method->id); ?>]" 
                                           id="edit-price-input-<?php echo e($method->id); ?>"
                                           value="<?php echo e($currentCost); ?>"
                                           min="0"
                                           step="1000"
                                           <?php echo e($isSelected ? '' : 'disabled'); ?>

                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                           placeholder="قیمت ارسال برای این محصول">
                                    <p class="text-xs text-gray-500 mt-1">می‌توانید قیمت ارسال را برای این محصول تغییر دهید</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">برچسب‌ها (حداکثر 5 تگ، با کاما جدا کنید)</label>
                <input type="text" 
                       name="tags" 
                       id="tagsInput"
                       value="<?php echo e(is_array($listing->tags) ? implode(', ', $listing->tags) : ''); ?>"
                       placeholder="مثال: لپتاپ, گیمینگ, ارزان"
                       class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">برچسب‌ها را با کاما (,) از هم جدا کنید. حداکثر 5 برچسب مجاز است.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        onclick="closeEditModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    انصراف
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(url('js/persian-datepicker-package.js')); ?>?v=<?php echo e(now()->timestamp); ?>"></script>
<script>
// Initialize datepickers
document.addEventListener('DOMContentLoaded', function() {
    // Starts at - only for pending auctions, can't be in the past
    const startsAtInput = document.getElementById('manage_starts_at');
    if (startsAtInput && !startsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(startsAtInput, {
            minDate: 'today'
        });
    }
    
    // Ends at
    const endsAtInput = document.getElementById('manage_ends_at');
    if (endsAtInput && !endsAtInput.dataset.pickerInitialized) {
        new PersianDatePicker(endsAtInput);
    }
});

// Base URL for API calls
const baseUrl = '<?php echo e(url("/")); ?>';

// Image Management
let currentImageId = <?php echo e($listing->images->sortBy('display_order')->first()->id ?? 0); ?>;

function changeMainImage(src, imageId, element) {
    document.getElementById('mainImage').src = src;
    currentImageId = imageId;
    
    // Remove active state from all thumbnails
    document.querySelectorAll('.grid.grid-cols-4 > div').forEach(div => {
        div.classList.remove('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2');
        div.classList.add('border-gray-200');
        const img = div.querySelector('img');
        if (img) {
            img.classList.add('grayscale', 'opacity-70');
            img.classList.remove('grayscale-0', 'opacity-100');
        }
    });
    
    // Add active state to clicked thumbnail
    element.classList.add('border-primary', 'ring-2', 'ring-primary', 'ring-offset-2');
    element.classList.remove('border-gray-200');
    const img = element.querySelector('img');
    if (img) {
        img.classList.remove('grayscale', 'opacity-70');
        img.classList.add('grayscale-0', 'opacity-100');
    }
}


function viewImage() {
    const mainImage = document.getElementById('mainImage');
    window.open(mainImage.src, '_blank');
}

function deleteMainImage() {
    if (!currentImageId) {
        showNotification('لطفاً یک تصویر انتخاب کنید', 'error');
        return;
    }
    
    showConfirmModal(
        'حذف تصویر',
        'آیا از حذف این تصویر اطمینان دارید؟',
        'حذف',
        'انصراف',
        () => {
            // Send delete request
            const deleteUrl = `<?php echo e(url('/admin/listings')); ?>/<?php echo e($listing->slug); ?>/images/${currentImageId}`;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تصویر با موفقیت حذف شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در حذف تصویر', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در حذف تصویر'));
        }
    );
}

function deleteThumbnailImage(imageId) {
    showConfirmModal(
        'حذف تصویر',
        'آیا از حذف این تصویر اطمینان دارید؟',
        'حذف',
        'انصراف',
        () => {
            const deleteUrl = `<?php echo e(url('/admin/listings')); ?>/<?php echo e($listing->slug); ?>/images/${imageId}`;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تصویر با موفقیت حذف شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در حذف تصویر', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در حذف تصویر'));
        }
    );
}

function setAsMainImage(imageId) {
    const setMainUrl = `<?php echo e(url('/admin/listings')); ?>/<?php echo e($listing->slug); ?>/images/${imageId}/set-main`;
    fetch(setMainUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('عکس اصلی با موفقیت تغییر کرد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در تنظیم عکس اصلی', 'error');
        }
    })
    .catch(error => handleFetchError(error, 'خطا در تنظیم عکس اصلی'));
}

function uploadNewImage(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('image', input.files[0]);
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        
        fetch(`<?php echo e(route('admin.listings.images.upload', $listing)); ?>`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'خطا در آپلود تصویر');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('تصویر با موفقیت آپلود شد', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'خطا در آپلود تصویر', 'error');
            }
        })
        .catch(error => {
            showNotification(error.message || 'خطا در آپلود تصویر', 'error');
        });
        
        // Reset input
        input.value = '';
    }
}

// Auction Settings
function saveAuctionSettings() {
    const form = document.getElementById('auctionSettingsForm');
    const formData = new FormData(form);
    
    // Add method spoofing for PUT request
    formData.append('_method', 'PUT');
    
    // Handle checkbox - if not checked, add false value
    if (!formData.has('auto_extend')) {
        formData.append('auto_extend', '0');
    } else {
        formData.set('auto_extend', '1');
    }
    
    // Remove commas from numbers
    ['starting_price', 'reserve_price', 'bid_increment', 'buy_now_price', 'deposit_amount'].forEach(field => {
        const value = formData.get(field);
        if (value) {
            formData.set(field, value.replace(/,/g, ''));
        }
    });
    
    fetch(`<?php echo e(route('admin.listings.settings', $listing)); ?>`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تنظیمات با موفقیت ذخیره شد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در ذخیره تنظیمات', 'error');
        }
    })
    .catch(error => {
        handleFetchError(error, 'خطا در ارتباط با سرور');
    });
}

// Format numbers with commas on input
document.querySelectorAll('input[type="text"][name*="price"], input[type="text"][name*="amount"]').forEach(input => {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            e.target.value = parseInt(value).toLocaleString('en-US');
        }
    });
});

// Bid Management
function cancelBid(bidId) {
    showConfirmModal(
        'ابطال پیشنهاد',
        'آیا از ابطال این پیشنهاد اطمینان دارید؟ این عمل قابل بازگشت نیست.',
        'ابطال',
        'انصراف',
        () => {
            fetch(`<?php echo e(url('/admin/bids')); ?>/${bidId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('پیشنهاد با موفقیت ابطال شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در ابطال پیشنهاد', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در ابطال پیشنهاد'));
        }
    );
}


function contactUser(userId) {
    window.location.href = `/admin/users/${userId}/message`;
}

// Auction Actions
function confirmEndEarly() {
    showConfirmModal(
        'پایان زودتر مزایده',
        'آیا از پایان زودتر این مزایده اطمینان دارید؟ برنده فعلی به عنوان برنده نهایی انتخاب خواهد شد.',
        'پایان مزایده',
        'انصراف',
        () => {
            fetch(`<?php echo e(route('admin.listings.end-early', $listing)); ?>`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('مزایده با موفقیت پایان یافت', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'خطا در پایان مزایده', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در پایان مزایده'));
        }
    );
}

function confirmSuspend() {
    const reason = prompt('لطفاً دلیل توقیف مزایده را وارد کنید:');
    if (reason && reason.trim()) {
        fetch(`<?php echo e(route('admin.listings.suspend', $listing)); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('مزایده با موفقیت توقیف شد', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('خطا در توقیف مزایده', 'error');
            }
        })
        .catch(error => handleFetchError(error, 'خطا در توقیف مزایده'));
    }
}

function confirmActivate() {
    showConfirmModal(
        'فعال‌سازی مزایده',
        'آیا از فعال‌سازی مجدد این مزایده اطمینان دارید؟',
        'فعال‌سازی',
        'انصراف',
        () => {
            fetch(`<?php echo e(route('admin.listings.activate', $listing)); ?>`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('مزایده با موفقیت فعال شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification('خطا در فعال‌سازی مزایده', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در فعال‌سازی مزایده'));
        }
    );
}

function confirmApprove() {
    showConfirmModal(
        'تایید آگهی',
        'آیا از تایید و انتشار این آگهی اطمینان دارید؟',
        'تایید و انتشار',
        'انصراف',
        () => {
            fetch(`<?php echo e(route('admin.listings.approve', $listing)); ?>`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('آگهی با موفقیت تایید شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'خطا در تایید آگهی', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در تایید آگهی'));
        }
    );
}

function confirmReject() {
    // Create a custom modal for rejection reason
    const modalHtml = `
        <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: flex;">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">رد کردن آگهی</h3>
                <p class="text-sm text-gray-600 mb-4">لطفاً دلیل رد کردن را وارد کنید:</p>
                <textarea id="rejectReason" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="4" placeholder="دلیل رد..."></textarea>
                <div class="flex gap-3 mt-6">
                    <button onclick="submitReject()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                        رد کردن
                    </button>
                    <button onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        انصراف
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    document.getElementById('rejectReason').focus();
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    if (modal) modal.remove();
}

function submitReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    
    if (!reason) {
        showNotification('لطفاً دلیل رد کردن را وارد کنید', 'error');
        return;
    }
    
    closeRejectModal();
    
    fetch(`<?php echo e(route('admin.listings.reject', $listing)); ?>`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('آگهی رد شد', 'success');
            setTimeout(() => window.location.href = '<?php echo e(route("admin.listings.index")); ?>', 1500);
        } else {
            showNotification(data.message || 'خطا در رد کردن آگهی', 'error');
        }
    })
    .catch(error => handleFetchError(error, 'خطا در رد کردن آگهی'));
}

// Modal Management
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}


// Edit Form Submission
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Check if category is selected
    const categoryId = document.getElementById('categorySelect').value;
    if (!categoryId) {
        showNotification('لطفاً دسته‌بندی را انتخاب کنید', 'error');
        return;
    }
    
    // Check if at least one shipping method is selected
    const checkedMethods = document.querySelectorAll('.edit-shipping-method-checkbox:checked');
    if (checkedMethods.length === 0) {
        showNotification('لطفاً حداقل یک روش ارسال را انتخاب کنید', 'error');
        document.getElementById('editShippingMethodsContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    formData.append('_method', 'PUT');
    
    fetch(`<?php echo e(route('admin.listings.update', $listing)); ?>`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تغییرات با موفقیت ذخیره شد', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('خطا در ذخیره تغییرات', 'error');
        }
    })
    .catch(error => handleFetchError(error, 'خطا در ذخیره تغییرات'));
});

// Toggle price input for edit form
function toggleEditPriceInput(checkbox, methodId) {
    const container = document.getElementById('edit-price-container-' + methodId);
    const input = document.getElementById('edit-price-input-' + methodId);
    
    if (checkbox.checked) {
        container.classList.remove('hidden');
        input.disabled = false;
    } else {
        container.classList.add('hidden');
        input.disabled = true;
    }
}

// Seller Contact
function contactSeller() {
    window.location.href = `/admin/stores/<?php echo e($listing->store->id); ?>/message`;
}

// Close modal on outside click
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Auto-refresh bids every 30 seconds
setInterval(() => {
    fetch(`<?php echo e(route('admin.listings.bids', $listing)); ?>`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.bids && data.bids.length > 0) {
            updateBidsContainer(data.bids);
        }
    });
}, 30000);

// Pending Changes Functions
function approvePendingChange(changeId) {
    showConfirmModal(
        'تایید تغییرات',
        'آیا از تایید و اعمال این تغییرات اطمینان دارید؟',
        'تایید و اعمال',
        'انصراف',
        () => {
            fetch(`<?php echo e(route('admin.listings.pending-changes.approve', ['listing' => $listing->id, 'change' => '__CHANGE_ID__'])); ?>`.replace('__CHANGE_ID__', changeId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تغییرات با موفقیت تایید و اعمال شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'خطا در تایید تغییرات', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در تایید تغییرات'));
        }
    );
}

function rejectPendingChange(changeId) {
    showPromptModal(
        'رد تغییرات',
        'لطفاً دلیل رد تغییرات را وارد کنید:',
        'رد تغییرات',
        'انصراف',
        (reason) => {
            if (!reason || reason.trim() === '') {
                showNotification('لطفاً دلیل رد را وارد کنید', 'error');
                return;
            }
            
            fetch(`<?php echo e(route('admin.listings.pending-changes.reject', ['listing' => $listing->id, 'change' => '__CHANGE_ID__'])); ?>`.replace('__CHANGE_ID__', changeId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('تغییرات رد شد', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'خطا در رد تغییرات', 'error');
                }
            })
            .catch(error => handleFetchError(error, 'خطا در رد تغییرات'));
        }
    );
}

function updateBidsContainer(bids) {
    // Update bids container with new data
    // This is a simplified version - adjust based on your needs
    const container = document.getElementById('bids-container');
    // Update logic here
}

const editAttributesSection = document.getElementById('editAttributesSection');
const editAttributesContainer = document.getElementById('editAttributesContainer');

// بارگذاری ویژگی‌های موجود
const currentAttributes = <?php echo json_encode($listing->attributeValues->mapWithKeys(function($av) {
    return [$av->category_attribute_id => $av->value];
}), 15, 512) ?>;

// گوش دادن به تغییرات category_id (hidden input از component)
const categoryInput = document.getElementById('categorySelect');
if (categoryInput) {
    // بارگذاری اولیه
    if (categoryInput.value) {
        loadEditAttributes(categoryInput.value);
    }
    
    // گوش دادن به تغییرات با MutationObserver
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                const newValue = categoryInput.value;
                if (newValue) {
                    loadEditAttributes(newValue);
                }
            }
        });
    });
    
    observer.observe(categoryInput, {
        attributes: true,
        attributeFilter: ['value']
    });
    
    // همچنین با Alpine.js
    document.addEventListener('alpine:initialized', () => {
        Alpine.effect(() => {
            const val = categoryInput.value;
            if (val) {
                loadEditAttributes(val);
            }
        });
    });
}

function loadEditAttributes(categoryId) {
    if (!categoryId) {
        editAttributesSection.style.display = 'none';
        editAttributesContainer.innerHTML = '';
        return;
    }
    
    fetch(`<?php echo e(url('/api/categories')); ?>/${categoryId}/attributes`)
        .then(response => response.json())
        .then(data => {
            if (data.attributes && data.attributes.length > 0) {
                editAttributesContainer.innerHTML = '';
                
                data.attributes.forEach(attr => {
                    const div = document.createElement('div');
                    const fieldName = `attributes[${attr.id}]`;
                    const required = attr.is_required ? 'required' : '';
                    const requiredLabel = attr.is_required ? '<span class="text-red-500">*</span>' : '';
                    const currentValue = currentAttributes[attr.id] || '';
                    
                    let inputHtml = '';
                    
                    if (attr.type === 'select' && attr.options) {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <select name="${fieldName}" ${required}
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">انتخاب کنید</option>
                                ${attr.options.map(opt => `<option value="${opt}" ${currentValue === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                            </select>
                        `;
                    } else if (attr.type === 'number') {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <input type="number" name="${fieldName}" value="${currentValue}" ${required}
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="${attr.name}">
                        `;
                    } else {
                        inputHtml = `
                            <label class="block text-xs font-medium text-gray-700 mb-1">
                                ${attr.name} ${requiredLabel}
                            </label>
                            <input type="text" name="${fieldName}" value="${currentValue}" ${required}
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                   placeholder="${attr.name}">
                        `;
                    }
                    
                    div.innerHTML = inputHtml;
                    editAttributesContainer.appendChild(div);
                });
                
                editAttributesSection.style.display = 'block';
            } else {
                editAttributesSection.style.display = 'none';
                editAttributesContainer.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error fetching attributes:', error);
            editAttributesSection.style.display = 'none';
        });
}
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/admin/listings/manage.blade.php ENDPATH**/ ?>