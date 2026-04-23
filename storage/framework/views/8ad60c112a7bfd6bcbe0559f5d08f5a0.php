<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تایید کد - حراج‌استون</title>
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
    body { font-family: 'Vazirmatn', sans-serif; }

    /* ورودی کد OTP */
    .otp-input {
        letter-spacing: 0.5em;
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
    }

    /* تایمر countdown */
    #resend-timer { transition: opacity 0.3s; }
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
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mx-auto mb-4">
                <span class="material-symbols-outlined text-4xl">mark_email_read</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">کد تایید را وارد کنید</h2>
            <p class="text-sm text-gray-500 text-center">
                کد ۶ رقمی به شماره
                <span class="font-bold text-gray-700 mx-1" dir="ltr"><?php echo e($phone); ?></span>
                ارسال شد
            </p>
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

        <form action="<?php echo e(route('otp.login.verify')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="phone" value="<?php echo e($phone); ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 text-center">کد تایید</label>
                <input class="otp-input w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                       name="code"
                       type="text"
                       inputmode="numeric"
                       maxlength="6"
                       placeholder="● ● ● ● ● ●"
                       autocomplete="one-time-code"
                       autofocus
                       required/>
            </div>

            <!-- تایمر -->
            <div class="text-center text-sm text-gray-500" id="resend-timer">
                ارسال مجدد کد تا
                <span id="countdown" class="font-bold text-primary">۰۲:۰۰</span>
            </div>

            <!-- دکمه ارسال مجدد (مخفی تا پایان تایمر) -->
            <div class="text-center hidden" id="resend-btn-wrap">
                <button type="button" onclick="document.getElementById('resend-form').submit()"
                        class="text-sm font-bold text-primary hover:text-primary-hover underline">
                    ارسال مجدد کد
                </button>
            </div>

            <button type="submit"
                    class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                <span>تایید و ورود</span>
                <span class="material-symbols-outlined text-lg">verified</span>
            </button>
        </form>

        
        <form id="resend-form" action="<?php echo e(route('otp.resend')); ?>" method="POST" class="hidden">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="phone" value="<?php echo e($phone); ?>">
            <input type="hidden" name="purpose" value="<?php echo e($purpose ?? 'login'); ?>">
        </form>

        <div class="mt-6 text-center">
            <a href="<?php echo e($purpose === 'login' ? route('otp.login') : route('register')); ?>"
               class="text-sm text-gray-500 hover:text-gray-700 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                تغییر شماره موبایل
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">© ۱۴۰۳ تمامی حقوق محفوظ است</p>
        </div>
    </div>

    <script>
    // Countdown timer - 2 minutes
    let seconds = 120;
    const countdownEl = document.getElementById('countdown');
    const timerWrap   = document.getElementById('resend-timer');
    const resendWrap  = document.getElementById('resend-btn-wrap');

    function toPersianDigits(n) {
        return String(n).replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
    }

    function updateTimer() {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        const display = toPersianDigits(String(m).padStart(2, '0')) + ':' + toPersianDigits(String(s).padStart(2, '0'));
        countdownEl.textContent = display;

        if (seconds <= 0) {
            timerWrap.classList.add('hidden');
            resendWrap.classList.remove('hidden');
            return;
        }
        seconds--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();

    // فقط عدد قبول کن
    document.querySelector('input[name="code"]').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
    </script>
</body>
</html>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\auth\otp-verify.blade.php ENDPATH**/ ?>