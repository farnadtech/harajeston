<?php if(auth()->guard()->check()): ?>
<?php if(!auth()->user()->isVerified() && auth()->user()->role !== 'admin'): ?>
<div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 mb-6"
     x-data="{
        showEmailForm: <?php echo e(session('email_otp_sent') ? 'true' : 'false'); ?>,
        showPhoneForm: false,
        phoneStep: 'send',
        phone: '<?php echo e(auth()->user()->phone); ?>',
        phoneCode: '',
        sending: false,
        verifying: false,
        phoneMsg: '',
        phoneMsgType: '',
        async sendPhoneOtp() {
            this.sending = true;
            this.phoneMsg = '';
            try {
                const res = await fetch('<?php echo e(route('phone.verify.send')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: this.phone })
                });
                const data = await res.json();
                if (data.success) {
                    this.phoneStep = 'verify';
                    this.phoneMsg = data.message;
                    this.phoneMsgType = 'success';
                } else {
                    this.phoneMsg = data.message;
                    this.phoneMsgType = 'error';
                }
            } catch(e) {
                this.phoneMsg = 'خطا در ارسال کد';
                this.phoneMsgType = 'error';
            }
            this.sending = false;
        },
        async verifyPhoneOtp() {
            this.verifying = true;
            this.phoneMsg = '';
            try {
                const res = await fetch('<?php echo e(route('phone.verify')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone: this.phone, code: this.phoneCode })
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    this.phoneMsg = data.message;
                    this.phoneMsgType = 'error';
                }
            } catch(e) {
                this.phoneMsg = 'خطا در تایید کد';
                this.phoneMsgType = 'error';
            }
            this.verifying = false;
        }
     }">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-amber-600">verified_user</span>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-amber-900 mb-1">احراز هویت الزامی است</h3>
            <p class="text-sm text-amber-700 mb-3">برای شرکت در حراجی‌ها و ایجاد آگهی، باید شماره تلفن یا ایمیل خود را تایید کنید.</p>

            <?php if(session('success') && !session('email_otp_sent')): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm mb-3"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm mb-3"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            
            <div x-show="!showEmailForm && !showPhoneForm" style="display:flex" class="flex flex-wrap gap-3">
                <?php if(!auth()->user()->phone_verified_at && auth()->user()->phone): ?>
                    <button @click="showPhoneForm = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-lg">phone_iphone</span>
                        تایید شماره تلفن
                    </button>
                <?php endif; ?>
                <?php if(!auth()->user()->email_verified_at): ?>
                    <form action="<?php echo e(route('email.verify.send')); ?>" method="POST" class="inline" @submit.prevent="showEmailForm = true; $el.submit()">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-amber-400 text-amber-700 rounded-xl text-sm font-medium hover:bg-amber-50 transition-colors">
                            <span class="material-symbols-outlined text-lg">email</span>
                            تایید ایمیل
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div x-show="showPhoneForm" style="display:none" class="bg-white border border-gray-200 rounded-xl p-4 mt-2 max-w-sm">
                <p class="text-sm font-medium text-gray-700 mb-3">تایید شماره: <span class="font-bold text-primary" dir="ltr"><?php echo e(auth()->user()->phone); ?></span></p>

                <div x-show="phoneStep === 'send'">
                    <button @click="sendPhoneOtp()" :disabled="sending"
                        class="w-full px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-50">
                        <span x-text="sending ? 'در حال ارسال...' : 'ارسال کد تایید'"></span>
                    </button>
                </div>

                <div x-show="phoneStep === 'verify'" class="space-y-3">
                    <input type="text" x-model="phoneCode" maxlength="6" placeholder="کد ۶ رقمی"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary text-center tracking-widest"
                        dir="ltr" inputmode="numeric">
                    <button @click="verifyPhoneOtp()" :disabled="verifying || phoneCode.length < 6"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50">
                        <span x-text="verifying ? 'در حال تایید...' : 'تایید کد'"></span>
                    </button>
                    <button @click="phoneStep = 'send'; sendPhoneOtp()" class="text-xs text-primary hover:underline block text-center">ارسال مجدد کد</button>
                </div>

                <div x-show="phoneMsg" class="mt-2 text-sm px-3 py-2 rounded-lg"
                    :class="phoneMsgType === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'"
                    x-text="phoneMsg"></div>

                <button @click="showPhoneForm = false; phoneStep = 'send'; phoneMsg = ''" class="mt-2 text-xs text-gray-500 hover:text-gray-700">انصراف</button>
            </div>

            
            <div x-show="showEmailForm" style="display:none" class="bg-white border border-gray-200 rounded-xl p-4 mt-2 max-w-sm">
                <?php if(session('success') && session('email_otp_sent')): ?>
                    <p class="text-sm text-green-700 mb-3"><?php echo e(session('success')); ?></p>
                <?php else: ?>
                    <p class="text-sm text-gray-600 mb-3">کد تایید به ایمیل شما ارسال شد.</p>
                <?php endif; ?>
                <?php if($errors->has('email_otp_code')): ?>
                    <p class="text-sm text-red-600 mb-2"><?php echo e($errors->first('email_otp_code')); ?></p>
                <?php endif; ?>
                <form action="<?php echo e(route('email.verify')); ?>" method="POST" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="email_otp_code" maxlength="6" placeholder="کد ۶ رقمی"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary text-center tracking-widest"
                        dir="ltr" inputmode="numeric" value="<?php echo e(old('email_otp_code')); ?>">
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary/90 transition-colors">
                        تایید کد
                    </button>
                </form>
                <form action="<?php echo e(route('email.verify.send')); ?>" method="POST" class="mt-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-xs text-primary hover:underline">ارسال مجدد کد</button>
                </form>
                <button @click="showEmailForm = false" class="mt-1 text-xs text-gray-500 hover:text-gray-700 block">انصراف</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\components\verification-banner.blade.php ENDPATH**/ ?>