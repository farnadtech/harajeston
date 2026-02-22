# راهنمای فعال‌سازی mod_rewrite در XAMPP

## مشکل
دکمه‌های تایید، تعلیق و فعال‌سازی آگهی خطای 404 می‌دهند چون Apache نمی‌تواند URL های بدون `index.php` را پیدا کند.

## راه حل: فعال‌سازی mod_rewrite

### مرحله 1: ویرایش httpd.conf

1. فایل زیر را باز کنید:
```
D:\xamp8.1\apache\conf\httpd.conf
```

2. این خط را پیدا کنید (حدود خط 180):
```
#LoadModule rewrite_module modules/mod_rewrite.so
```

3. علامت `#` را از ابتدای خط حذف کنید:
```
LoadModule rewrite_module modules/mod_rewrite.so
```

### مرحله 2: تنظیم AllowOverride

در همان فایل `httpd.conf`، این بخش را پیدا کنید:

```apache
<Directory "D:/xamp8.1/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride None
    Require all granted
</Directory>
```

و `AllowOverride None` را به `AllowOverride All` تغییر دهید:

```apache
<Directory "D:/xamp8.1/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
</Directory>
```

### مرحله 3: Restart Apache

1. XAMPP Control Panel را باز کنید
2. دکمه "Stop" کنار Apache را بزنید
3. چند ثانیه صبر کنید
4. دکمه "Start" را بزنید

### مرحله 4: تست

بعد از restart، صفحه مدیریت آگهی‌ها را refresh کنید (Ctrl+F5) و دوباره روی دکمه‌ها کلیک کنید.

## اگر هنوز کار نکرد

اگر بعد از این کارها هنوز مشکل داشتید، این دستور را اجرا کنید:

```bash
D:\xamp8.1\php\php.exe artisan route:cache
D:\xamp8.1\php\php.exe artisan config:cache
```

سپس Apache را دوباره restart کنید.

## نکته مهم

بعد از فعال شدن mod_rewrite، دیگر نیازی به `index.php` در URL ها نیست و همه چیز باید کار کند.
