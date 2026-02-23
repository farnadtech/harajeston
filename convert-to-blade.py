import re

# Read the HTML file
with open('resources/views/auth/auth-temp.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace login form
login_form_pattern = r'<form class="space-y-5" onsubmit="event\.preventDefault\(\);">.*?</form>'
login_form_replacement = '''<form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">شماره موبایل یا ایمیل</label>
                        <div class="relative">
                            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pr-10" 
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="مثلا: 09123456789" 
                                   type="text"
                                   required/>
                            <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400">person</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">رمز عبور</label>
                            <a class="text-xs font-bold text-primary hover:text-primary-hover" href="{{ route('password.request') }}">رمز عبور را فراموش کردید؟</a>
                        </div>
                        <div class="relative">
                            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pr-10" 
                                   name="password"
                                   placeholder="••••••••" 
                                   type="password"
                                   required/>
                            <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400 cursor-pointer hover:text-gray-600">visibility</span>
                        </div>
                    </div>
                    <button class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                        <span>ورود به حساب کاربری</span>
                        <span class="material-symbols-outlined text-lg">login</span>
                    </button>
                </form>'''

content = re.sub(login_form_pattern, login_form_replacement, content, flags=re.DOTALL)

# Add error display for login
login_content_start = '<div class="tab-content active space-y-6" id="login-content"><div><h2'
login_error_block = '''<div class="tab-content active space-y-6" id="login-content">
                @if($errors->any() && !old('name') && !old('password_confirmation'))
                    <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-red-500 ml-3">error</span>
                            <div class="flex-1">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div><h2'''

content = content.replace(login_content_start, login_error_block)

# Replace register form
register_form_pattern = r'<div class="tab-content space-y-6" id="register-content">.*?<form class="space-y-4" onsubmit="event\.preventDefault\(\);">.*?</form>'
register_form_replacement = '''<div class="tab-content space-y-6" id="register-content">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">ساخت حساب جدید 🚀</h2>
                    <p class="text-sm text-gray-500">به بزرگترین جامعه مزایده آنلاین بپیوندید</p>
                </div>
                
                @if($errors->any() && (old('name') || old('password_confirmation')))
                    <div class="bg-red-50 border-r-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <span class="material-symbols-outlined text-red-500 ml-3">error</span>
                            <div class="flex-1">
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-700 font-medium">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نام و نام خانوادگی</label>
                        <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" 
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="نام کامل خود را وارد کنید" 
                               type="text"
                               required/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل</label>
                        <div class="relative">
                            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors pl-10" 
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="example@email.com" 
                                   type="email"
                                   required/>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="material-symbols-outlined text-gray-400">smartphone</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور</label>
                        <div class="relative">
                            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" 
                                   name="password"
                                   placeholder="حداقل ۸ کاراکتر" 
                                   type="password"
                                   required/>
                            <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400 cursor-pointer hover:text-gray-600">visibility_off</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تکرار رمز عبور</label>
                        <div class="relative">
                            <input class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" 
                                   name="password_confirmation"
                                   placeholder="حداقل ۸ کاراکتر" 
                                   type="password"
                                   required/>
                            <span class="material-symbols-outlined absolute left-3 top-3.5 text-gray-400 cursor-pointer hover:text-gray-600">visibility_off</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2 pt-2">
                        <input class="w-4 h-4 mt-1 text-primary border-gray-300 rounded focus:ring-primary" id="terms" type="checkbox" required/>
                        <label class="text-xs text-gray-500 leading-5" for="terms">
                            با ثبت نام در سایت، <a class="text-primary hover:underline" href="#">قوانین و مقررات</a> و <a class="text-primary hover:underline" href="#">حریم خصوصی</a> را می‌پذیرم.
                        </label>
                    </div>
                    <button class="w-full bg-primary hover:bg-primary-hover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
                        <span>ایجاد حساب کاربری</span>
                        <span class="material-symbols-outlined text-lg">person_add</span>
                    </button>
                </form>'''

content = re.sub(register_form_pattern, register_form_replacement, content, flags=re.DOTALL)

# Add Laravel script for auto-switching to register tab on error
script_end = '</script></body></html>'
script_replacement = '''
        
        @if($errors->any() && (old('name') || old('password_confirmation')))
            document.addEventListener('DOMContentLoaded', function() {
                switchTab('register');
            });
        @endif
    </script>
</body>
</html>'''

content = content.replace(script_end, script_replacement)

# Write the blade file
with open('resources/views/auth/auth.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Conversion complete!")
