# راهنمای رفع مشکل دکمه‌های تایید و تعلیق آگهی

## مشکل
دکمه‌های تایید، تعلیق، و فعال‌سازی آگهی در پنل ادمین خطای 404 می‌دهند.

## علت احتمالی
1. مشکل در URL rewriting
2. مشکل در authentication/session
3. مشکل در CSRF token

## راه حل‌های پیشنهادی

### راه حل 1: بررسی لاگین ادمین
مطمئن شوید که با اکانت admin لاگین کرده‌اید:
```bash
D:\xamp8.1\php\php.exe artisan tinker
>>> $admin = \App\Models\User::where('role', 'admin')->first();
>>> $admin->email
>>> $admin->role
```

### راه حل 2: تست مستقیم روت
فایل `/public/test-direct-route.php` را در مرورگر باز کنید:
```
http://localhost/haraj/public/test-direct-route.php
```

### راه حل 3: بررسی Apache mod_rewrite
مطمئن شوید که `mod_rewrite` در Apache فعال است:
1. فایل `httpd.conf` را باز کنید
2. خط `LoadModule rewrite_module modules/mod_rewrite.so` را uncomment کنید
3. Apache را restart کنید

### راه حل 4: استفاده از form به جای fetch
اگر مشکل ادامه داشت، می‌توانیم از form submission استفاده کنیم:

```javascript
function approveListing(listingId) {
    Swal.fire({
        // ... سایر تنظیمات
    }).then((result) => {
        if (result.isConfirmed) {
            // ایجاد form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/listings/' + listingId + '/approve';
            
            // اضافه کردن CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // ارسال form
            document.body.appendChild(form);
            form.submit();
        }
    });
}
```

### راه حل 5: بررسی session
```bash
D:\xamp8.1\php\php.exe artisan session:table
D:\xamp8.1\php\php.exe artisan migrate
```

## تست
بعد از هر تغییر:
1. Cache را پاک کنید: `php artisan view:clear`
2. مرورگر را refresh کنید (Ctrl+F5)
3. Console مرورگر را باز کنید (F12)
4. روی دکمه کلیک کنید و خطا را بررسی کنید

## اطلاعات تکمیلی
- روت‌ها درست تعریف شده‌اند (تایید شده با `artisan route:list`)
- کنترلرها و متدها وجود دارند
- middleware ها درست هستند
- مشکل احتمالاً از URL rewriting یا session است
