
<?php $__env->startSection('title', 'مدیریت خبرنامه'); ?>

<?php $__env->startSection('content'); ?>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مدیریت خبرنامه</h1>
            <p class="text-sm text-gray-500 mt-1">مشترکین و ارسال ایمیل گروهی</p>
        </div>
        <button onclick="document.getElementById('send-modal').style.display='flex'"
                class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors">
            <span class="material-symbols-outlined text-base">send</span>
            ارسال ایمیل گروهی
        </button>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-base">check_circle</span>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-primary"><?php echo e($totalAll); ?></div>
            <div class="text-sm text-gray-500 mt-1">کل مشترکین</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-green-600"><?php echo e($totalActive); ?></div>
            <div class="text-sm text-gray-500 mt-1">مشترکین فعال</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 text-center">
            <div class="text-3xl font-black text-amber-500"><?php echo e($totalAll - $totalActive); ?></div>
            <div class="text-sm text-gray-500 mt-1">لغو اشتراک</div>
        </div>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="جستجو در ایمیل یا نام..."
                   class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                <option value="">همه</option>
                <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>فعال</option>
                <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>غیرفعال</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">فیلتر</button>
            <?php if(request('search') || request('status')): ?>
            <a href="<?php echo e(route('admin.newsletter.index')); ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">پاک کردن</a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">ایمیل</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">نام</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">وضعیت</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">تاریخ عضویت</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-700">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $subscribers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-400"><?php echo e($sub->id); ?></td>
                    <td class="px-4 py-3 font-medium text-gray-900"><?php echo e($sub->email); ?></td>
                    <td class="px-4 py-3 text-gray-500"><?php echo e($sub->name ?: '-'); ?></td>
                    <td class="px-4 py-3">
                        <?php if($sub->is_active): ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                فعال
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                <span class="material-symbols-outlined text-xs">cancel</span>
                                غیرفعال
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        <?php echo e($sub->created_at ? $sub->created_at->format('Y/m/d') : '-'); ?>

                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <form method="POST" action="<?php echo e(route('admin.newsletter.toggle', $sub)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" title="<?php echo e($sub->is_active ? 'غیرفعال کردن' : 'فعال کردن'); ?>"
                                        class="p-1.5 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors <?php echo e($sub->is_active ? 'text-amber-500' : 'text-green-600'); ?>">
                                    <span class="material-symbols-outlined text-sm"><?php echo e($sub->is_active ? 'toggle_off' : 'toggle_on'); ?></span>
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('admin.newsletter.destroy', $sub)); ?>" onsubmit="return confirm('حذف شود؟')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="p-1.5 border border-red-200 rounded-lg bg-red-50 hover:bg-red-100 transition-colors text-red-500">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <span class="material-symbols-outlined text-5xl block mb-2">mail_off</span>
                        هیچ مشترکی یافت نشد
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($subscribers->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($subscribers->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<div id="send-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4" dir="rtl">
    <div class="absolute inset-0 bg-black/60" onclick="this.parentElement.style.display='none'"></div>
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg text-primary">send</span>
                ارسال ایمیل گروهی
            </h3>
            <button onclick="document.getElementById('send-modal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('admin.newsletter.send')); ?>" class="p-5 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">ارسال به</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="target" value="active" checked class="accent-primary">
                        مشترکین فعال (<?php echo e($totalActive); ?> نفر)
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="radio" name="target" value="all" class="accent-primary">
                        همه (<?php echo e($totalAll); ?> نفر)
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">موضوع ایمیل</label>
                <input type="text" name="subject" required placeholder="موضوع ایمیل را وارد کنید"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">متن ایمیل</label>
                <textarea name="body" required rows="7" placeholder="متن ایمیل... (HTML پشتیبانی می‌شود)"
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary resize-y font-mono"></textarea>
                <p class="text-xs text-gray-400 mt-1">لینک لغو اشتراک به صورت خودکار اضافه می‌شود.</p>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="document.getElementById('send-modal').style.display='none'"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    انصراف
                </button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">send</span>
                    ارسال ایمیل
                </button>
            </div>
        </form>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\newsletter\index.blade.php ENDPATH**/ ?>