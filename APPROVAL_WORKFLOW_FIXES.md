# تغییرات سیستم تایید آگهی‌ها

## مشکلات برطرف شده

### 1. خطای SQL در رد آگهی
- **مشکل**: `SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1`
- **علت**: وضعیت‌های `rejected`, `suspended`, `cancelled` در enum جدول listings وجود نداشتند
- **راه‌حل**: 
  - ایجاد migration برای اضافه کردن این وضعیت‌ها به enum
  - فایل: `database/migrations/2026_02_26_120000_add_rejected_suspended_to_listings_status.php`

### 2. فعال‌سازی خودکار آگهی‌های تایید نشده
- **مشکل**: آگهی‌های pending که هنوز تایید نشده بودند، بعد از 1 دقیقه خودکار فعال می‌شدند
- **علت**: Job `ProcessAuctionStarting` فقط `status = pending` و `starts_at <= now` را چک می‌کرد
- **راه‌حل**: 
  - اضافه کردن شرط `whereNotNull('approved_at')` به query
  - فایل: `app/Jobs/ProcessAuctionStarting.php`

### 3. برچسب نادرست در لیست آگهی‌ها
- **مشکل**: آگهی‌های منتظر تایید، برچسب "در انتظار شروع" نشان می‌دادند
- **راه‌حل**: 
  - اضافه کردن چک `!$listing->approved_at` برای نمایش برچسب "منتظر تایید ادمین"
  - فایل‌ها:
    - `resources/views/admin/listings/index.blade.php`
    - `resources/views/components/listing-card.blade.php`

### 4. فیلتر "نیاز به تایید" کار نمی‌کرد
- **مشکل**: فیلتر هیچ نتیجه‌ای نشان نمی‌داد
- **راه‌حل**: 
  - تغییر فیلتر از `status = 'draft'` به `status = 'pending' AND approved_at IS NULL`
  - فایل: `app/Http/Controllers/Admin/ListingController.php`

### 5. فیلدهای approved_at و approved_by ذخیره نمی‌شدند
- **مشکل**: مقادیر این فیلدها در دیتابیس ذخیره نمی‌شدند
- **علت**: این فیلدها در آرایه `$fillable` مدل Listing نبودند
- **راه‌حل**: 
  - اضافه کردن `approved_at` و `approved_by` به `$fillable`
  - فایل: `app/Models/Listing.php`

### 6. پاپ‌آپ رد آگهی با prompt ساده
- **مشکل**: استفاده از `prompt()` برای دریافت دلیل رد
- **راه‌حل**: 
  - ایجاد modal سفارشی با textarea برای دریافت دلیل رد
  - فایل: `resources/views/admin/listings/manage.blade.php`

## ورک فلو نهایی

### ایجاد آگهی جدید
1. کاربر آگهی جدید ایجاد می‌کند
2. اگر `require_listing_approval` فعال باشد:
   - وضعیت: `pending`
   - `approved_at`: `NULL`
   - برچسب: "منتظر تایید ادمین"

### تایید آگهی توسط ادمین
1. ادمین دکمه "تایید و انتشار" را می‌زند
2. سیستم:
   - `approved_at` = زمان فعلی
   - `approved_by` = ID ادمین
   - اگر `starts_at` در آینده باشد: وضعیت `pending` می‌ماند
   - اگر `starts_at` در گذشته باشد: وضعیت به `active` تغییر می‌کند

### فعال‌سازی خودکار
- Job `ProcessAuctionStarting` هر دقیقه اجرا می‌شود
- شرایط فعال‌سازی:
  - `status = 'pending'`
  - `approved_at IS NOT NULL`
  - `starts_at <= NOW()`
- وضعیت به `active` تغییر می‌کند

### رد آگهی
1. ادمین دکمه "رد کردن" را می‌زند
2. modal برای دریافت دلیل رد نمایش داده می‌شود
3. سیستم:
   - `status` = `rejected`
   - `rejection_reason` = دلیل وارد شده
   - نوتیفیکیشن به فروشنده ارسال می‌شود

## فایل‌های تغییر یافته

1. `app/Jobs/ProcessAuctionStarting.php` - اضافه کردن چک approved_at
2. `app/Models/Listing.php` - اضافه کردن approved_at و approved_by به fillable
3. `app/Http/Controllers/Admin/ListingController.php` - فیلتر نیاز به تایید
4. `resources/views/admin/listings/index.blade.php` - برچسب‌ها و فیلتر
5. `resources/views/admin/listings/manage.blade.php` - modal رد آگهی
6. `resources/views/components/listing-card.blade.php` - برچسب منتظر تایید
7. `database/migrations/2026_02_26_120000_add_rejected_suspended_to_listings_status.php` - اضافه کردن وضعیت‌های جدید

## تست‌ها

### تست خودکار
```bash
php public/test-approval-fixes.php
php public/test-complete-approval-workflow.php
```

### تست دستی
1. فعال کردن تنظیم "نیاز به تایید دستی آگهی‌ها" در پنل ادمین
2. ایجاد آگهی جدید با زمان شروع در آینده
3. بررسی نمایش برچسب "منتظر تایید ادمین"
4. تایید آگهی توسط ادمین
5. بررسی تغییر برچسب به "در انتظار شروع"
6. صبر تا زمان شروع برسد (یا تغییر دستی در دیتابیس)
7. بررسی فعال‌سازی خودکار توسط scheduler

## نکات مهم

- آگهی‌های قدیمی که قبل از این تغییرات ایجاد شده‌اند، `approved_at = NULL` دارند
- برای فعال‌سازی خودکار آن‌ها، باید `approved_at` را به‌روزرسانی کنید
- scheduler باید فعال باشد: `php artisan schedule:work`
- وضعیت‌های مجاز: `pending`, `active`, `ended`, `completed`, `failed`, `out_of_stock`, `rejected`, `suspended`, `cancelled`
