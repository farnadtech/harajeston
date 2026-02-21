# راهنمای نصب سریع - پلتفرم حراجی آنلاین

## 🚀 نصب در 5 دقیقه

### مرحله 1: آپلود فایل‌ها
```bash
# آپلود تمام فایل‌ها به سرور
# مسیر پیشنهادی: /var/www/auction-platform
```

### مرحله 2: نصب Dependencies
```bash
cd /var/www/auction-platform
composer install --optimize-autoloader --no-dev
```

### مرحله 3: تنظیمات محیطی
```bash
# کپی فایل .env
cp .env.example .env

# ویرایش .env و تنظیم:
# - DB_DATABASE
# - DB_USERNAME  
# - DB_PASSWORD
# - APP_URL

# تولید کلید برنامه
php artisan key:generate
```

### مرحله 4: دیتابیس
```bash
# اجرای migrations
php artisan migrate

# (اختیاری) اضافه کردن داده‌های نمونه
php artisan db:seed --class=QuickSeeder
```

### مرحله 5: دسترسی‌ها
```bash
# Linux
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# ایجاد symbolic link برای storage
php artisan storage:link
```

### مرحله 6: راه‌اندازی Scheduler (مهم!)

#### Linux (Production):
```bash
crontab -e
```
اضافه کنید:
```
* * * * * cd /var/www/auction-platform && php artisan schedule:run >> /dev/null 2>&1
```

#### Windows (Development):
```bash
php artisan schedule:work
```

### مرحله 7: تنظیمات وب‌سرور

#### Apache (.htaccess موجود است)
```apache
DocumentRoot /var/www/auction-platform/public
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/auction-platform/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### مرحله 8: بهینه‌سازی (Production)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ تست نصب

### 1. بررسی سایت
مرور کنید: `http://your-domain.com`

### 2. ورود به پنل ادمین
```
URL: http://your-domain.com/admin
Email: admin@example.com
Password: password
```

### 3. تست Scheduler
```bash
php artisan schedule:run
```

باید خروجی شبیه این ببینید:
```
Running [App\Jobs\ProcessAuctionStarting] .... DONE
Running [App\Jobs\ProcessAuctionEnding] ...... DONE
```

---

## 🔧 تنظیمات اولیه (از پنل ادمین)

1. **تنظیمات سپرده** (`/admin/settings`)
   - نوع محاسبه: ثابت یا درصدی
   - مبلغ یا درصد سپرده

2. **تنظیمات کمیسیون** (`/admin/settings`)
   - نوع محاسبه: ثابت یا درصدی
   - پرداخت‌کننده: خریدار، فروشنده، یا هر دو

3. **تنظیمات مدت زمان حراجی** (`/admin/settings`)
   - اجبار مدت زمان ثابت (اختیاری)
   - تعداد روز

4. **روش‌های ارسال** (`/admin/shipping-methods`)
   - اضافه کردن روش‌های ارسال

5. **دسته‌بندی‌ها** (`/admin/categories`)
   - ایجاد دسته‌بندی‌های محصولات

---

## 📞 پشتیبانی

### مشکلات رایج:

**خطای 500:**
```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

**Scheduler کار نمی‌کند:**
- بررسی crontab: `crontab -l`
- تست دستی: `php artisan schedule:run`
- بررسی لاگ: `tail -f storage/logs/laravel.log`

**خطای دیتابیس:**
- بررسی `.env`
- تست اتصال: `php artisan tinker` → `DB::connection()->getPdo();`

---

## 📚 مستندات کامل

برای اطلاعات بیشتر:
- `SCHEDULER_SETUP_GUIDE.md` - راهنمای کامل Scheduler
- `DEPLOYMENT.md` - راهنمای استقرار
- `README.md` - مستندات کامل پروژه

---

**نسخه:** 1.0.0  
**تاریخ:** 1404/12/02
