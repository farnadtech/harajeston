<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-vazirmatn antialiased">
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-500 to-purple-600">
        <div class="text-center">
            <h1 class="text-5xl font-bold text-white mb-4">
                سامانه مزایده ایرانی
            </h1>
            <p class="text-xl text-white/90 mb-8">
                پلتفرم حرفه‌ای مزایده آنلاین
            </p>
            <div class="flex gap-4 justify-center">
                <button class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition">
                    ورود
                </button>
                <button class="px-6 py-3 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition">
                    ثبت‌نام
                </button>
            </div>
        </div>
    </div>
</body>
</html>
