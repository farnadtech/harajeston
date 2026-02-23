# راهنمای سیستم فراموشی رمز عبور

## پیاده‌سازی کامل شده

سیستم کامل فراموشی و بازیابی رمز عبور با موارد زیر پیاده‌سازی شده:

### 1. کنترلرها
- `app/Http/Controllers/Auth/ForgotPasswordController.php` - مدیریت درخواست بازیابی
- `app/Http/Controllers/Auth/ResetPasswordController.php` - مدیریت تغییر رمز عبور

### 2. View ها
- `resources/views/auth/forgot-password.blade.php` - صفحه درخواست بازیابی
- `resources/views/auth/reset-password.blade.php` - صفحه تغییر رمز عبور

### 3. نوتیفیکیشن
- `app/Notifications/ResetPasswordNotification.php` - ایمیل فارسی بازیابی رمز عبور

### 4. روت‌ها
```php
Route::get('/password/request', ...)->name('password.request');
Route::post('/password/email', ...)->name('password.email');
Route::get('/password/reset/{token}', ...)->name('password.reset');
Route::post('/password/reset', ...)->name('password.update');
```

## نحوه استفاده

### 1. درخواست بازیابی
- کاربر روی "رمز عبور را فراموش کردید؟" کلیک می‌کند
- ایمیل خود را وارد می‌کند
- سیستم لینک بازیابی را به ایمیل ارسال می‌کند

### 2. تغییر رمز عبور
- کاربر روی لینک در ایمیل کلیک می‌کند
- رمز عبور جدید را وارد می‌کند
- رمز عبور تغییر می‌کند و به صفحه ورود هدایت می‌شود

## تنظیمات ایمیل

### برای محیط توسعه (Development)
از Mailpit استفاده می‌شود که در `.env` تنظیم شده:
```
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

برای مشاهده ایمیل‌ها:
1. Mailpit را نصب کنید: https://github.com/axllent/mailpit
2. یا از Docker استفاده کنید:
```bash
docker run -d -p 1025:1025 -p 8025:8025 axllent/mailpit
```
3. ایمیل‌ها را در `http://localhost:8025` مشاهده کنید

### برای محیط تولید (Production)
تنظیمات SMTP واقعی را در `.env` قرار دهید:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@harajeston.com
MAIL_FROM_NAME="حراج‌استون"
```

## تست سیستم

### 1. بدون سرور ایمیل (Log Driver)
برای تست سریع، در `.env` تغییر دهید:
```
MAIL_MAILER=log
```
ایمیل‌ها در `storage/logs/laravel.log` ذخیره می‌شوند.

### 2. مراحل تست
1. به `/login` بروید
2. روی "رمز عبور را فراموش کردید؟" کلیک کنید
3. ایمیل یک کاربر موجود را وارد کنید
4. اگر `MAIL_MAILER=log`:
   - فایل `storage/logs/laravel.log` را باز کنید
   - لینک بازیابی را پیدا کنید (مثل: `/password/reset/TOKEN?email=...`)
   - لینک را در مرورگر باز کنید
5. رمز عبور جدید را وارد کنید
6. با رمز عبور جدید وارد شوید

## امنیت

- توکن‌های بازیابی تا 60 دقیقه معتبر هستند
- هر توکن فقط یک بار قابل استفاده است
- ایمیل باید در سیستم ثبت شده باشد
- رمز عبور جدید باید حداقل 8 کاراکتر باشد
- تأیید رمز عبور الزامی است

## پیام‌های خطا (فارسی)

- "لطفا ایمیل خود را وارد کنید."
- "فرمت ایمیل صحیح نیست."
- "این ایمیل در سیستم ثبت نشده است."
- "رمز عبور باید حداقل 8 کاراکتر باشد."
- "تکرار رمز عبور مطابقت ندارد."
- "لینک بازیابی نامعتبر یا منقضی شده است."

## نکات مهم

1. جدول `password_reset_tokens` از قبل در دیتابیس وجود دارد
2. نوتیفیکیشن ایمیل کاملا فارسی است
3. طراحی صفحات مطابق با صفحه ورود است
4. تمام validation ها فارسی هستند
5. پس از تغییر موفق رمز عبور، کاربر به صفحه ورود هدایت می‌شود
