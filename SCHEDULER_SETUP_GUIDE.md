# راهنمای راه‌اندازی Laravel Scheduler (زمان‌بندی وظایف خودکار)

این سیستم از Laravel Scheduler برای اجرای وظایف خودکار استفاده می‌کند که شامل:
- شروع خودکار حراجی‌ها در زمان مقرر
- پایان خودکار حراجی‌ها
- بررسی timeout نهایی‌سازی سفارشات

## 📋 فهرست مطالب
1. [وظایف خودکار سیستم](#وظایف-خودکار-سیستم)
2. [راه‌اندازی در Windows (محیط توسعه)](#راه‌اندازی-در-windows)
3. [راه‌اندازی در Linux (سرور Production)](#راه‌اندازی-در-linux)
4. [تست و عیب‌یابی](#تست-و-عیب‌یابی)

---

## وظایف خودکار سیستم

### 1. ProcessAuctionStarting (هر دقیقه)
**وظیفه:** فعال‌سازی حراجی‌های pending که زمان شروعشان رسیده است.

**فایل:** `app/Jobs/ProcessAuctionStarting.php`

**منطق:**
```php
// حراجی‌هایی که status=pending و starts_at <= الان
Listing::where('status', 'pending')
    ->where('starts_at', '<=', now())
    ->get();
```

### 2. ProcessAuctionEnding (هر دقیقه)
**وظیفه:** پایان دادن به حراجی‌های فعال که زمان پایانشان رسیده است.

**فایل:** `app/Jobs/ProcessAuctionEnding.php`

**منطق:**
```php
// حراجی‌هایی که status=active و ends_at <= الان
Listing::where('status', 'active')
    ->where('ends_at', '<=', now())
    ->get();
```

### 3. ProcessFinalizationTimeout (هر ساعت)
**وظیفه:** بررسی سفارشاتی که مهلت نهایی‌سازی آنها تمام شده است.

**فایل:** `app/Jobs/ProcessFinalizationTimeout.php`

---

## راه‌اندازی در Windows

### روش 1: استفاده از دستور `schedule:work` (توصیه شده برای Development)

این روش ساده‌ترین راه برای تست و توسعه است:

```bash
# در ترمینال پروژه اجرا کنید
php artisan schedule:work
```

این دستور یک فرآیند دائمی ایجاد می‌کند که هر دقیقه وظایف را اجرا می‌کند.

**نکات مهم:**
- این دستور باید همیشه در حال اجرا باشد
- اگر ترمینال را ببندید، متوقف می‌شود
- برای production مناسب نیست

---

### روش 2: استفاده از Windows Task Scheduler (توصیه شده برای Production)

#### مرحله 1: باز کردن Task Scheduler
1. کلید `Windows + R` را فشار دهید
2. `taskschd.msc` را تایپ کنید و Enter بزنید

#### مرحله 2: ایجاد Task جدید
1. از منوی سمت راست، روی `Create Basic Task` کلیک کنید
2. نام: `Laravel Scheduler - Auction Platform`
3. توضیحات: `اجرای خودکار وظایف Laravel هر دقیقه`

#### مرحله 3: تنظیم Trigger
1. انتخاب کنید: `Daily`
2. زمان شروع: `00:00:00` (نیمه شب)
3. تکرار هر: `1 روز`

#### مرحله 4: تنظیم Action
1. انتخاب کنید: `Start a program`
2. Program/script:
   ```
   D:\xamp8.1\php\php.exe
   ```
3. Add arguments:
   ```
   D:\xamp8.1\htdocs\haraj\artisan schedule:run
   ```
4. Start in:
   ```
   D:\xamp8.1\htdocs\haraj
   ```

#### مرحله 5: تنظیمات پیشرفته
1. روی Task ایجاد شده راست کلیک کنید و `Properties` را انتخاب کنید
2. در تب `Triggers`، روی trigger ایجاد شده دوبار کلیک کنید
3. تیک `Repeat task every` را بزنید
4. انتخاب کنید: `1 minute`
5. Duration: `Indefinitely`
6. تیک `Enabled` را بزنید

#### مرحله 6: تنظیمات امنیتی
1. در تب `General`:
   - تیک `Run whether user is logged on or not` را بزنید
   - تیک `Run with highest privileges` را بزنید
2. در تب `Settings`:
   - تیک `Allow task to be run on demand` را بزنید
   - تیک `Run task as soon as possible after a scheduled start is missed` را بزنید
   - تیک `If the task fails, restart every: 1 minute` را بزنید

---

## راه‌اندازی در Linux

### مرحله 1: ویرایش Crontab

```bash
crontab -e
```

### مرحله 2: اضافه کردن خط زیر

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**مثال واقعی:**
```bash
* * * * * cd /var/www/auction-platform && php artisan schedule:run >> /dev/null 2>&1
```

### مرحله 3: ذخیره و خروج

- در nano: `Ctrl + X` سپس `Y` سپس `Enter`
- در vim: `ESC` سپس `:wq` سپس `Enter`

### مرحله 4: بررسی Crontab

```bash
crontab -l
```

---

## تست و عیب‌یابی

### تست دستی

برای تست اینکه آیا scheduler به درستی کار می‌کند:

```bash
php artisan schedule:run
```

خروجی باید شبیه این باشد:
```
2026-02-21 20:05:34 Running [App\Jobs\ProcessAuctionStarting] .... DONE
2026-02-21 20:05:38 Running [App\Jobs\ProcessAuctionEnding] ...... DONE
2026-02-21 20:05:40 Running [App\Jobs\ProcessFinalizationTimeout] . DONE
```

### بررسی لاگ‌ها

لاگ‌های مربوط به Jobs در فایل زیر ذخیره می‌شوند:

```
storage/logs/laravel.log
```

برای مشاهده لاگ‌های اخیر:

```bash
# Windows (PowerShell)
Get-Content storage/logs/laravel.log -Tail 50

# Linux
tail -f storage/logs/laravel.log
```

### مشکلات رایج و راه‌حل

#### 1. خطای "Class not found"
**راه‌حل:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

#### 2. خطای "Permission denied" (Linux)
**راه‌حل:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 3. Job اجرا نمی‌شود
**بررسی کنید:**
- آیا cron job یا Task Scheduler به درستی تنظیم شده؟
- آیا مسیر PHP و پروژه صحیح است؟
- آیا دسترسی‌های لازم وجود دارد؟

#### 4. خطای Database Connection
**راه‌حل:**
- فایل `.env` را بررسی کنید
- اطمینان حاصل کنید که دیتابیس در دسترس است
- تست کنید: `php artisan tinker` سپس `DB::connection()->getPdo();`

---

## بررسی وضعیت حراجی‌ها

### حراجی‌های Pending که باید فعال شوند:

```bash
php artisan tinker
```

```php
// تعداد حراجی‌های pending که زمان شروعشان رسیده
\App\Models\Listing::where('status', 'pending')
    ->where('starts_at', '<=', now())
    ->count();

// لیست حراجی‌ها
\App\Models\Listing::where('status', 'pending')
    ->where('starts_at', '<=', now())
    ->get(['id', 'title', 'starts_at', 'status']);
```

### حراجی‌های Active که باید پایان یابند:

```php
// تعداد حراجی‌های active که زمان پایانشان رسیده
\App\Models\Listing::where('status', 'active')
    ->where('ends_at', '<=', now())
    ->count();

// لیست حراجی‌ها
\App\Models\Listing::where('status', 'active')
    ->where('ends_at', '<=', now())
    ->get(['id', 'title', 'ends_at', 'status']);
```

---

## نکات مهم برای خریداران اسکریپت

### 1. پیش‌نیازها
- PHP 8.1 یا بالاتر
- Composer
- دسترسی به Crontab (Linux) یا Task Scheduler (Windows)
- دسترسی به دیتابیس

### 2. نصب اولیه
```bash
# نصب dependencies
composer install

# تنظیم فایل .env
cp .env.example .env
php artisan key:generate

# اجرای migrations
php artisan migrate

# راه‌اندازی scheduler
# Linux: اضافه کردن به crontab
# Windows: ایجاد Task در Task Scheduler
```

### 3. تست عملکرد
```bash
# تست دستی
php artisan schedule:run

# مشاهده لاگ‌ها
tail -f storage/logs/laravel.log
```

### 4. مانیتورینگ
- بررسی منظم لاگ‌ها
- استفاده از ابزارهای مانیتورینگ مثل Laravel Horizon (اختیاری)
- تنظیم اعلان‌های خطا (اختیاری)

---

## پشتیبانی

در صورت بروز مشکل:
1. لاگ‌های `storage/logs/laravel.log` را بررسی کنید
2. دستور `php artisan schedule:run` را به صورت دستی اجرا کنید
3. تنظیمات Cron/Task Scheduler را بررسی کنید
4. دسترسی‌های فایل‌ها و پوشه‌ها را چک کنید

---

**تاریخ آخرین بروزرسانی:** 1404/12/02
**نسخه:** 1.0.0
