<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>تایید کد - حراج استون</title>
    <link href="/haraj/public/css/app.css" rel="stylesheet"/>
    <link href="/haraj/public/css/vazirmatn-local.css" rel="stylesheet"/>
    <style>body{font-family:Vazirmatn,sans-serif}.otp-input{letter-spacing:.5em;text-align:center;font-size:1.5rem;font-weight:bold}</style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 sm:p-12">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5z"/></svg>
        </div>
        <h1 class="text-xl font-black tracking-tight">حراج<span class="text-primary">استون</span></h1>
    </div>
    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4 mb-6">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
            @endforeach
        </div>
    @endif
    @if(isset($verified) && $verified)
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">رمز عبور جدید</h2>
        <p class="text-sm text-gray-500">رمز عبور جدید خود را وارد کنید</p>
    </div>
    <form action="{{ route('password.otp.reset') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="identifier" value="{{ $identifier }}">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور جدید</label>
            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" name="password" id="pw1" type="password" placeholder="حداقل 8 کاراکتر" required minlength="8" oninput="checkMatch()"/>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">تکرار رمز عبور</label>
            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" name="password_confirmation" id="pw2" type="password" placeholder="تکرار رمز عبور" required oninput="checkMatch()"/>
            <p id="pw-error" class="text-xs text-red-600 mt-1 hidden">رمز عبور و تکرار آن یکسان نیستند.</p>
        </div>
        <button type="submit" id="submit-btn" disabled class="w-full bg-gray-400 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2"><span>تغییر رمز عبور</span></button>
    </form>
    <script>
    function checkMatch() {
        var p1 = document.getElementById("pw1").value;
        var p2 = document.getElementById("pw2").value;
        var btn = document.getElementById("submit-btn");
        var err = document.getElementById("pw-error");
        var ok = p1.length >= 8 && p1 === p2;
        btn.disabled = !ok;
        btn.className = ok
            ? "w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2"
            : "w-full bg-gray-400 text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2";
        if (p2.length > 0 && p1 !== p2) { err.classList.remove("hidden"); } else { err.classList.add("hidden"); }
    }
    </script>
    @else
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">کد تایید را وارد کنید</h2>
        <p class="text-sm text-gray-500">کد 6 رقمی ارسال شده به <span class="font-bold text-gray-700 mx-1" dir="ltr">{{ $identifier }}</span> را وارد کنید</p>
    </div>
    <form action="{{ route('password.otp.verify') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="identifier" value="{{ $identifier }}">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2 text-center">کد تایید</label>
            <input class="otp-input w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" name="code" type="text" inputmode="numeric" maxlength="6" placeholder="     " autocomplete="one-time-code" autofocus required/>
        </div>
        <div class="text-center text-sm text-gray-500" id="timer-wrap">ارسال مجدد تا <span id="countdown" class="font-bold text-primary">02:00</span></div>
        <div class="text-center hidden" id="resend-wrap"><button type="button" onclick="document.getElementById('resend-form').submit()" class="text-sm font-bold text-primary underline">ارسال مجدد کد</button></div>
        <button type="submit" class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2"><span>تایید کد</span></button>
    </form>
    <form id="resend-form" action="{{ route('password.otp.send') }}" method="POST" class="hidden">@csrf<input type="hidden" name="identifier" value="{{ $identifier }}"></form>
    <div class="mt-4 text-center"><a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-gray-700">تغییر شماره/ایمیل</a></div>
    <script>
    let s=120;const cd=document.getElementById("countdown"),tw=document.getElementById("timer-wrap"),rw=document.getElementById("resend-wrap");
    function tick(){const m=Math.floor(s/60),sec=s%60;cd.textContent=String(m).padStart(2,"0")+":"+String(sec).padStart(2,"0");if(s--<=0){tw.classList.add("hidden");rw.classList.remove("hidden");return;}setTimeout(tick,1000);}
    tick();
    document.querySelector("input[name=code]").addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").slice(0,6);});
    </script>
    @endif
    <div class="mt-8 text-center"><p class="text-xs text-gray-400">تمامی حقوق محفوظ است</p></div>
</div>
</body>
</html>
