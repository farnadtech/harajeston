<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>فراموشی رمز عبور - حراج‌استون</title>
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>body { font-family: 'Vazirmatn', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 sm:p-12">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
            </div>
            <h1 class="text-xl font-black tracking-tight">حراج<span class="text-primary">استون</span></h1>
        </div>
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">فراموشی رمز عبور</h2>
            <p class="text-sm text-gray-500">شماره موبایل یا ایمیل خود را وارد کنید تا کد تایید ارسال شود</p>
        </div>
        <?php if(session('status')): ?>
            <div class="bg-green-50 border-r-4 border-green-500 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-700 font-medium"><?php echo e(session('status')); ?></p>
            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-sm text-red-700 font-medium"><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo e(route('password.otp.send')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره موبایل یا ایمیل</label>
                <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                       name="identifier" value="<?php echo e(old('identifier')); ?>"
                       placeholder="09123456789 یا example@email.com" type="text" required/>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                <span>ارسال کد تایید</span>
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="<?php echo e(route('login')); ?>" class="text-sm font-bold text-primary hover:text-primary-hover">بازگشت به صفحه ورود</a>
        </div>
        <div class="mt-8 text-center"><p class="text-xs text-gray-400">  تمامی حقوق محفوظ است</p></div>
    </div>
</body>
</html><?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\auth\forgot-password.blade.php ENDPATH**/ ?>