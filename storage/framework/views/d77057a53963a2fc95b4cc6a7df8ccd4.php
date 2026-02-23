

<?php $__env->startSection('title', 'نتایج جستجو'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    فیلترها
                </h2>

                <?php if(!empty($availableAttributes) && $availableAttributes->count() > 0): ?>
                <form method="GET" action="<?php echo e(route('listings.index')); ?>" class="space-y-4">
                    <!-- حفظ پارامترهای موجود -->
                    <?php if(request('category')): ?>
                        <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                    <?php endif; ?>
                    <?php if(request('search')): ?>
                        <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                    <?php endif; ?>
                    <?php if(request('tag')): ?>
                        <input type="hidden" name="tag" value="<?php echo e(request('tag')); ?>">
                    <?php endif; ?>

                    <?php $__currentLoopData = $availableAttributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e($attribute->name); ?></label>
                        
                        <?php if($attribute->type === 'select' && $attribute->options): ?>
                            <select name="attr[<?php echo e($attribute->id); ?>]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <option value="">همه</option>
                                <?php $__currentLoopData = $attribute->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option); ?>" <?php echo e(request("attr.{$attribute->id}") === $option ? 'selected' : ''); ?>>
                                        <?php echo e($option); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif($attribute->type === 'number'): ?>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="attr[<?php echo e($attribute->id); ?>][min]" 
                                       value="<?php echo e(request("attr.{$attribute->id}.min")); ?>"
                                       placeholder="از" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <input type="number" name="attr[<?php echo e($attribute->id); ?>][max]" 
                                       value="<?php echo e(request("attr.{$attribute->id}.max")); ?>"
                                       placeholder="تا" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                            </div>
                        <?php else: ?>
                            <input type="text" name="attr[<?php echo e($attribute->id); ?>]" 
                                   value="<?php echo e(request("attr.{$attribute->id}")); ?>"
                                   placeholder="<?php echo e($attribute->name); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                        اعمال فیلترها
                    </button>
                </form>
                <?php endif; ?>

                <?php if(request()->hasAny(['category', 'tag', 'search', 'attr'])): ?>
                <a href="<?php echo e(route('listings.index')); ?>" class="block w-full mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center font-medium">
                    حذف همه فیلترها
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Header -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 mb-2">
                            <?php if(request('tag')): ?>
                                نتایج برچسب: <span class="text-primary">#<?php echo e(request('tag')); ?></span>
                            <?php elseif(request('search')): ?>
                                نتایج جستجو: <span class="text-primary"><?php echo e(request('search')); ?></span>
                            <?php elseif(request('category')): ?>
                                <?php
                                    $categoryObj = \App\Models\Category::where('slug', request('category'))->first();
                                ?>
                                دسته‌بندی: <span class="text-primary"><?php echo e($categoryObj ? $categoryObj->name : request('category')); ?></span>
                            <?php else: ?>
                                همه مزایده‌ها
                            <?php endif; ?>
                        </h1>
                        <p class="text-sm text-gray-500">
                            <?php echo e(\App\Services\PersianNumberService::convertToPersian($listings->total())); ?> مزایده یافت شد
                        </p>
                    </div>
                    
                    <!-- Sort Options -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600 font-medium">مرتب‌سازی:</span>
                        <select onchange="window.location.href=this.value" class="border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                            <option value="<?php echo e(request()->fullUrlWithQuery(['sort' => 'starting_soon'])); ?>" <?php echo e(request('sort') == 'starting_soon' ? 'selected' : ''); ?>>
                                زودتر شروع می‌شود
                            </option>
                            <option value="<?php echo e(request()->fullUrlWithQuery(['sort' => 'ending_soon'])); ?>" <?php echo e(request('sort') == 'ending_soon' || !request('sort') ? 'selected' : ''); ?>>
                                زودتر به پایان می‌رسد
                            </option>
                            <option value="<?php echo e(request()->fullUrlWithQuery(['sort' => 'newest'])); ?>" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>
                                جدیدترین
                            </option>
                            <option value="<?php echo e(request()->fullUrlWithQuery(['sort' => 'price_low'])); ?>" <?php echo e(request('sort') == 'price_low' ? 'selected' : ''); ?>>
                                ارزان‌ترین
                            </option>
                            <option value="<?php echo e(request()->fullUrlWithQuery(['sort' => 'price_high'])); ?>" <?php echo e(request('sort') == 'price_high' ? 'selected' : ''); ?>>
                                گران‌ترین
                            </option>
                        </select>
                    </div>
                </div>
                
                <!-- Active Filters -->
        <?php if(request('tag') || request('search') || request('category') || request('buy_now')): ?>
        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-gray-100">
            <span class="text-sm text-gray-600 font-medium">فیلترهای فعال:</span>
            
            <?php if(request('tag')): ?>
            <a href="<?php echo e(request()->fullUrlWithQuery(['tag' => null])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                <span class="material-symbols-outlined text-sm">tag</span>
                <?php echo e(request('tag')); ?>

                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            <?php endif; ?>
            
            <?php if(request('search')): ?>
            <a href="<?php echo e(request()->fullUrlWithQuery(['search' => null])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                <span class="material-symbols-outlined text-sm">search</span>
                <?php echo e(request('search')); ?>

                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            <?php endif; ?>
            
            <?php if(request('category')): ?>
            <?php
                $activeCategoryObj = \App\Models\Category::where('slug', request('category'))->first();
            ?>
            <a href="<?php echo e(request()->fullUrlWithQuery(['category' => null])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors">
                <span class="material-symbols-outlined text-sm">category</span>
                <?php echo e($activeCategoryObj ? $activeCategoryObj->name : request('category')); ?>

                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            <?php endif; ?>
            
            <?php if(request('buy_now')): ?>
            <a href="<?php echo e(request()->fullUrlWithQuery(['buy_now' => null])); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg text-sm font-medium hover:bg-orange-100 transition-colors">
                <span class="material-symbols-outlined text-sm">bolt</span>
                خرید فوری
                <span class="material-symbols-outlined text-sm">close</span>
            </a>
            <?php endif; ?>
            
            <a href="<?php echo e(route('listings.index')); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                <span class="material-symbols-outlined text-sm">clear_all</span>
                حذف همه فیلترها
            </a>
        </div>
        <?php endif; ?>
            </div>

            <!-- Results Grid -->
            <?php if($listings->isNotEmpty()): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php $__currentLoopData = $listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $listing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.listing-card','data' => ['listing' => $listing]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('listing-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['listing' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($listing)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce)): ?>
<?php $attributes = $__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce; ?>
<?php unset($__attributesOriginal31ec1dc5dadb4835ef50de3d88e519ce); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce)): ?>
<?php $component = $__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce; ?>
<?php unset($__componentOriginal31ec1dc5dadb4835ef50de3d88e519ce); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
    
    <!-- Pagination -->
    <div class="flex justify-center">
        <?php echo e($listings->links('vendor.pagination.custom')); ?>

    </div>
    
    <?php else: ?>
    <!-- Empty State -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-6xl text-gray-400">search_off</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">نتیجه‌ای یافت نشد</h3>
        <p class="text-gray-500 mb-6">متأسفانه مزایده‌ای با این مشخصات پیدا نشد. لطفاً فیلترهای دیگری را امتحان کنید.</p>
        <a href="<?php echo e(route('listings.index')); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-bold">
            <span class="material-symbols-outlined">home</span>
            بازگشت به صفحه اصلی
        </a>
    </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views/listings/search.blade.php ENDPATH**/ ?>