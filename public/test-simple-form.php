<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <title>تست فرم ساده</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6">تست فرم ورود</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                خطا در ورود
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/haraj/public/login" class="space-y-4">
            <div>
                <label class="block mb-2">ایمیل:</label>
                <input type="email" name="email" value="test@test.com" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <div>
                <label class="block mb-2">رمز عبور:</label>
                <input type="password" name="password" value="12345678" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded">
                ورود
            </button>
        </form>
        
        <hr class="my-6">
        
        <h2 class="text-xl font-bold mb-4">تست فرم ثبت‌نام</h2>
        
        <form method="POST" action="/haraj/public/register" class="space-y-4">
            <div>
                <label class="block mb-2">نام:</label>
                <input type="text" name="name" value="Test User" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <div>
                <label class="block mb-2">ایمیل:</label>
                <input type="email" name="email" value="newuser@test.com" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <div>
                <label class="block mb-2">رمز عبور:</label>
                <input type="password" name="password" value="12345678" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <div>
                <label class="block mb-2">تکرار رمز عبور:</label>
                <input type="password" name="password_confirmation" value="12345678" 
                       class="w-full border border-gray-300 rounded px-3 py-2" required/>
            </div>
            
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded">
                ثبت‌نام
            </button>
        </form>
    </div>
    
    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                console.log('Form submitting to:', this.action);
                console.log('Method:', this.method);
                console.log('Data:', new FormData(this));
            });
        });
    </script>
</body>
</html>
