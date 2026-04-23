<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ثبت‌نام - حراج‌استون</title>
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>
    @font-face {
        font-family: 'Material Symbols Outlined';
        font-style: normal;
        font-weight: 100 700;
        font-display: block;
        src: url('/haraj/public/fonts/MaterialSymbolsOutlined[FILL,GRAD,opsz,wght].woff2') format('woff2');
    }
    .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-weight: normal;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
    </style>
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-md p-8 sm:p-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h1 class="text-xl font-black tracking-tight">
                حراج<span class="text-primary">استون</span>
            </h1>
        </div>
        
        <div class="mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl">person_add</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">ساخت حساب جدید</h2>
            <p class="text-sm text-gray-500 text-center">به بزرگترین پلتفرم حراجی آنلاین ایران بپیوندید</p>
        </div>
        
        <?php if($errors->any()): ?>
            <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <span class="material-symbols-outlined text-red-500 ml-3">error</span>
                    <div class="flex-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p class="text-sm text-red-700 font-medium"><?php echo e($error); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- OTP Modal -->
        <div id="otpModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm mx-4">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mx-auto mb-4">
                        <span class="material-symbols-outlined text-4xl">sms</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">تایید شماره موبایل</h3>
                    <p class="text-sm text-gray-500">کد ۶ رقمی ارسال شده به <span id="otpPhoneDisplay" class="font-bold text-gray-700 dir-ltr"></span> را وارد کنید</p>
                </div>

                <div id="otpModalError" class="hidden bg-red-50 border-r-4 border-red-500 rounded-lg p-3 mb-4">
                    <p class="text-sm text-red-700 font-medium" id="otpModalErrorText"></p>
                </div>

                <div class="relative mb-4">
                    <input id="otpCodeInput"
                           class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-center text-2xl font-bold tracking-widest"
                           placeholder="● ● ● ● ● ●"
                           type="text"
                           inputmode="numeric"
                           maxlength="6"
                           autocomplete="one-time-code"/>
                </div>

                <button type="button" id="confirmOtpBtn"
                        class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2 mb-3">
                    <span>تایید و ثبت‌نام</span>
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                </button>

                <div class="flex items-center justify-between text-sm">
                    <button type="button" id="resendOtpBtn" class="text-primary hover:underline font-medium disabled:text-gray-400 disabled:no-underline" disabled>
                        ارسال مجدد کد
                    </button>
                    <span id="otpTimer" class="text-gray-500 font-medium"></span>
                </div>

                <button type="button" id="closeOtpModal" class="mt-4 w-full text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    بازگشت به فرم
                </button>
            </div>
        </div>

        <form action="<?php echo e(route('register')); ?>" method="POST" class="space-y-4" id="registerForm">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="otp_code" id="otpCodeHidden"/>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نام و نام خانوادگی <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="name" 
                           value="<?php echo e(old('name')); ?>"
                           placeholder="نام کامل خود را وارد کنید" 
                           type="text"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">badge</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره تلفن <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           id="phone"
                           name="phone" 
                           value="<?php echo e(old('phone')); ?>"
                           placeholder="09123456789" 
                           type="text"
                           pattern="09[0-9]{9}"
                           maxlength="11"
                           required
                           oninput="validatePhone(this)"/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">phone</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">شماره موبایل باید 11 رقمی و با 09 شروع شود</p>
                <p id="phoneError" class="text-xs text-red-600 mt-1 hidden">شماره تلفن باید 11 رقمی و با 09 شروع شود</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="email" 
                           value="<?php echo e(old('email')); ?>"
                           placeholder="example@email.com" 
                           type="email"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">mail</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="password" 
                           placeholder="حداقل ۸ کاراکتر" 
                           type="password"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">lock</span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تکرار رمز عبور <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                           name="password_confirmation" 
                           placeholder="تکرار رمز عبور" 
                           type="password"
                           required/>
                    <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">lock</span>
                </div>
                <p id="passwordMatchError" class="text-xs text-red-600 mt-1 hidden">رمز عبور و تکرار آن یکسان نیستند</p>
            </div>
            
            <div class="flex items-start pt-2">
                <input id="terms" name="terms" type="checkbox" required
                       class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
                <label for="terms" class="mr-2 block text-sm text-gray-700">
                    با <a href="#" class="text-primary hover:text-primary-hover font-medium">قوانین و مقررات</a> موافقم <span class="text-red-500">*</span>
                </label>
            </div>
            
            <button class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                <span>ایجاد حساب کاربری</span>
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <span class="text-sm text-gray-600">قبلاً ثبت‌نام کرده‌اید؟</span>
            <a class="text-sm font-bold text-primary hover:text-primary-hover mr-1" href="<?php echo e(route('login')); ?>">
                وارد شوید
            </a>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© ۱۴۰۳ تمامی حقوق محفوظ است</p>
        </div>
    </div>

    <script>
    var otpEnabled = <?php echo e(\App\Models\SiteSetting::get('otp_enabled', true) ? 'true' : 'false'); ?>;
    var otpVerified = false;
    var timerInterval = null;

    function validatePhone(input) {
        var value = input.value.replace(/\D/g, '').substring(0, 11);
        input.value = value;
        var err = document.getElementById('phoneError');
        if (value.length === 11 && value.startsWith('09')) {
            input.classList.remove('border-red-500');
            err.classList.add('hidden');
        } else if (value.length > 0) {
            input.classList.add('border-red-500');
            err.classList.remove('hidden');
        } else {
            input.classList.remove('border-red-500');
            err.classList.add('hidden');
        }
    }

    function validatePasswordMatch() {
        var pw = document.querySelector('input[name="password"]').value;
        var pwc = document.querySelector('input[name="password_confirmation"]').value;
        var errEl = document.getElementById('passwordMatchError');
        if (pwc.length > 0 && pw !== pwc) {
            errEl.classList.remove('hidden');
            return false;
        }
        errEl.classList.add('hidden');
        return true;
    }

    function openOtpModal(phone) {
        document.getElementById('otpPhoneDisplay').textContent = phone;
        document.getElementById('otpCodeInput').value = '';
        document.getElementById('otpModalError').classList.add('hidden');
        document.getElementById('otpModal').classList.remove('hidden');
        document.getElementById('otpModal').classList.add('flex');
        document.getElementById('otpCodeInput').focus();
        startTimer(120);
    }

    function closeOtpModal() {
        document.getElementById('otpModal').classList.add('hidden');
        document.getElementById('otpModal').classList.remove('flex');
        clearInterval(timerInterval);
    }

    function startTimer(seconds) {
        clearInterval(timerInterval);
        var timerEl = document.getElementById('otpTimer');
        var resendBtn = document.getElementById('resendOtpBtn');
        resendBtn.disabled = true;
        var remaining = seconds;
        timerEl.textContent = formatTime(remaining);
        timerInterval = setInterval(function() {
            remaining--;
            timerEl.textContent = formatTime(remaining);
            if (remaining <= 0) {
                clearInterval(timerInterval);
                timerEl.textContent = '';
                resendBtn.disabled = false;
            }
        }, 1000);
    }

    function formatTime(s) {
        var m = Math.floor(s / 60);
        var sec = s % 60;
        return (m < 10 ? '0' + m : m) + ':' + (sec < 10 ? '0' + sec : sec);
    }

    function sendOtp(phone) {
        return fetch('<?php echo e(route("otp.register.send")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            body: JSON.stringify({ phone: phone })
        }).then(function(res) {
            var status = res.status;
            return res.text().then(function(text) {
                try {
                    var data = JSON.parse(text);
                    data._status = status;
                    return data;
                } catch(e) {
                    // response is not JSON (PHP error page)
                    return { success: false, message: 'خطای سرور. لطفاً دوباره تلاش کنید.', _status: status };
                }
            });
        });
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var phone = document.getElementById('phone').value;
        if (phone.length !== 11 || !phone.startsWith('09')) {
            document.getElementById('phone').classList.add('border-red-500');
            document.getElementById('phoneError').classList.remove('hidden');
            document.getElementById('phone').focus();
            return;
        }

        if (!validatePasswordMatch()) {
            document.querySelector('input[name="password_confirmation"]').focus();
            return;
        }

        if (!otpEnabled) {
            this.submit();
            return;
        }

        if (otpVerified) {
            this.submit();
            return;
        }

        var submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>در حال ارسال کد...</span>';

        sendOtp(phone).then(function(data) {
            if (data.success) {
                openOtpModal(phone);
            } else {
                var msg = data.message;
                if (!msg && data.errors) {
                    var firstKey = Object.keys(data.errors)[0];
                    msg = data.errors[firstKey][0];
                }
                alert(msg || 'خطا در ارسال کد تایید');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>ایجاد حساب کاربری</span><span class="material-symbols-outlined text-lg">arrow_forward</span>';
        }).catch(function() {
            alert('خطا در اتصال به سرور');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>ایجاد حساب کاربری</span><span class="material-symbols-outlined text-lg">arrow_forward</span>';
        });
    });

    document.getElementById('confirmOtpBtn').addEventListener('click', function() {
        var code = document.getElementById('otpCodeInput').value.trim();
        if (code.length !== 6) {
            document.getElementById('otpModalErrorText').textContent = 'کد تایید باید 6 رقم باشد';
            document.getElementById('otpModalError').classList.remove('hidden');
            return;
        }
        document.getElementById('otpCodeHidden').value = code;
        otpVerified = true;
        closeOtpModal();
        document.getElementById('registerForm').submit();
    });

    document.getElementById('resendOtpBtn').addEventListener('click', function() {
        var phone = document.getElementById('phone').value;
        var btn = this;
        btn.disabled = true;
        sendOtp(phone).then(function(data) {
            if (data.success) {
                document.getElementById('otpModalError').classList.add('hidden');
                startTimer(120);
            } else {
                document.getElementById('otpModalErrorText').textContent = data.message || 'خطا در ارسال مجدد';
                document.getElementById('otpModalError').classList.remove('hidden');
                btn.disabled = false;
            }
        }).catch(function() { btn.disabled = false; });
    });

    document.getElementById('closeOtpModal').addEventListener('click', closeOtpModal);

    document.getElementById('otpCodeInput').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    document.getElementById('otpCodeInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') document.getElementById('confirmOtpBtn').click();
    });

    document.querySelector('input[name="password_confirmation"]').addEventListener('input', validatePasswordMatch);
    document.querySelector('input[name="password"]').addEventListener('input', validatePasswordMatch);
    </script>
</body>
</html><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\auth\register.blade.php ENDPATH**/ ?>