

<?php $__env->startSection('title', 'ایجاد تیکت جدید'); ?>
<?php $__env->startSection('page-title', 'تیکت جدید'); ?>
<?php $__env->startSection('header-title', 'ایجاد تیکت توسط ادمین'); ?>
<?php $__env->startSection('header-subtitle', 'ارسال پیام مستقیم به هر کاربر'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo e(route('admin.tickets.index')); ?>" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
        <h1 class="text-xl font-bold text-gray-900">تیکت جدید</h1>
    </div>

    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-200 p-6"
         x-data="adminTicketForm('<?php echo e(route('admin.tickets.listings-for-user')); ?>')">
        <form action="<?php echo e(route('admin.tickets.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    گیرنده تیکت <span class="text-red-500">*</span>
                </label>
                <select name="recipient_id" x-model="recipientId" @change="loadListings()" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- انتخاب کاربر --</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>" <?php echo e(old('recipient_id') == $user->id ? 'selected' : ''); ?>>
                            <?php echo e($user->name); ?> (<?php echo e($user->isSeller() ? 'فروشنده' : 'خریدار'); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    حراجی مرتبط <span class="text-red-500">*</span>
                </label>
                <div x-show="!recipientId" class="bg-gray-50 border border-gray-200 text-gray-500 px-4 py-3 rounded-xl text-sm">
                    ابتدا کاربر را انتخاب کنید
                </div>
                <div x-show="recipientId && loading" class="bg-gray-50 border border-gray-200 text-gray-500 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                    در حال بارگذاری...
                </div>
                <select name="listing_id" x-model="listingId" required
                    x-show="recipientId && !loading"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">-- انتخاب حراجی --</option>
                    <template x-for="listing in listings" :key="listing.id">
                        <option :value="listing.id" x-text="listing.title"></option>
                    </template>
                </select>
                <p x-show="recipientId && !loading && listings.length === 0" class="text-sm text-amber-600 mt-2">
                    هیچ حراجی مرتبطی برای این کاربر یافت نشد.
                </p>
            </div>

            
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">موضوع <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" required maxlength="255"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="موضوع تیکت...">
            </div>

            
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">اولویت</label>
                <select name="priority" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="low">کم</option>
                    <option value="normal" selected>معمولی</option>
                    <option value="high">زیاد</option>
                </select>
            </div>

            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">متن پیام <span class="text-red-500">*</span></label>
                <textarea name="message" rows="6" required maxlength="5000"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary resize-none"
                    placeholder="متن پیام ادمین..."><?php echo e(old('message')); ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    :disabled="!recipientId || !listingId"
                    class="flex-1 bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    ایجاد تیکت
                </button>
                <a href="<?php echo e(route('admin.tickets.index')); ?>" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                    انصراف
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function adminTicketForm(apiUrl) {
    return {
        recipientId: '<?php echo e(old('recipient_id', '')); ?>',
        listingId: '<?php echo e(old('listing_id', '')); ?>',
        listings: [],
        loading: false,

        loadListings() {
            if (!this.recipientId) {
                this.listings = [];
                this.listingId = '';
                return;
            }
            this.loading = true;
            this.listingId = '';
            fetch(apiUrl + '?user_id=' + this.recipientId, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                this.listings = data;
                this.loading = false;
            })
            .catch(() => { this.loading = false; });
        },

        init() {
            if (this.recipientId) this.loadListings();
        }
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\admin\tickets\create.blade.php ENDPATH**/ ?>