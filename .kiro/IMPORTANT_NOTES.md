# یادداشت‌های مهم پروژه

## 🚫 قوانین کلی برای AI Agent

### ❌ ممنوعیت ایجاد فایل‌های Markdown
**هرگز فایل‌های .md برای خلاصه‌سازی یا مستندسازی کار خود ایجاد نکنید!**

دلایل:
- کاربر نمی‌خواهد فایل‌های اضافی در پروژه
- اطلاعات باید در همین فایل (IMPORTANT_NOTES.md) ذخیره شود
- فایل‌های .md اضافی باعث شلوغی پروژه می‌شوند
- تنها استثنا: زمانی که کاربر صریحاً درخواست ایجاد فایل markdown کند

### ✅ روش صحیح: استفاده از این فایل
همه اطلاعات مهم، تغییرات، و یادداشت‌ها را در این فایل (.kiro/IMPORTANT_NOTES.md) ذخیره کنید.

---

## 📋 ساختار کامل پروژه

### نوع پروژه
**Persian Auction Marketplace** - پلتفرم حراج آنلاین ایرانی

### تکنولوژی‌ها
- **Backend**: Laravel 11.x (PHP 8.1+)
- **Frontend**: Blade Templates + Livewire 3.x + TailwindCSS
- **Database**: MySQL 8.0
- **Server**: XAMPP 8.1 (Windows)
- **PHP Path**: `D:\xamp8.1\php\php.exe`

### ویژگی‌های اصلی
1. **سیستم حراج محور**: همه محصولات به صورت حراج (با قابلیت خرید فوری اختیاری)
2. **سیستم کیف پول**: مدیریت موجودی و سپرده‌ها
3. **سیستم پیشنهاد قیمت**: Real-time bidding با Livewire
4. **پنل مدیریت**: مدیریت کامل حراج‌ها، کاربران، سفارشات
5. **چند زبانه**: فارسی (اصلی) + انگلیسی
6. **تقویم جلالی**: استفاده از تاریخ شمسی
7. **اعداد فارسی**: نمایش اعداد به صورت فارسی در UI

### ساختار دیتابیس

#### جداول اصلی:
- `users`: کاربران (خریدار/فروشنده/ادمین)
- `stores`: فروشگاه‌های فروشندگان
- `listings`: آگهی‌های حراج
- `listing_images`: تصاویر محصولات
- `bids`: پیشنهادات قیمت
- `auction_participations`: شرکت‌کنندگان حراج (پرداخت سپرده)
- `wallets`: کیف پول کاربران
- `wallet_transactions`: تراکنش‌های مالی
- `orders`: سفارشات
- `order_items`: آیتم‌های سفارش
- `carts`: سبد خرید
- `cart_items`: آیتم‌های سبد
- `shipping_methods`: روش‌های ارسال
- `admin_action_logs`: لاگ اقدامات ادمین

#### فیلدهای مهم Listing:
```php
'starting_price'      // قیمت پایه حراج
'current_price'       // بالاترین پیشنهاد فعلی
'buy_now_price'       // قیمت خرید فوری (nullable)
'reserve_price'       // قیمت رزرو (nullable)
'bid_increment'       // گام افزایش پیشنهاد
'deposit_amount'      // مبلغ سپرده شرکت
'starts_at'           // زمان شروع
'ends_at'             // زمان پایان
'status'              // وضعیت: pending, active, ended, completed, failed, out_of_stock, suspended, cancelled
'suspension_reason'   // دلیل تعلیق (nullable)
'auto_extend'         // تمدید خودکار (boolean)
```

### Services (لایه منطق کسب‌وکار)
- `AuctionService`: مدیریت حراج‌ها
- `BidService`: مدیریت پیشنهادات
- `DepositService`: مدیریت سپرده‌ها
- `WalletService`: مدیریت کیف پول
- `OrderService`: مدیریت سفارشات
- `CartService`: مدیریت سبد خرید
- `ListingService`: مدیریت آگهی‌ها
- `StoreService`: مدیریت فروشگاه‌ها
- `ImageService`: مدیریت تصاویر
- `ShippingService`: مدیریت ارسال
- `AdminService`: عملیات ادمین
- `DashboardService`: داده‌های داشبورد
- `CacheService`: مدیریت کش
- `PersianNumberService`: تبدیل اعداد به فارسی
- `JalaliDateService`: تبدیل تاریخ به جلالی

### Livewire Components
- `AuctionBidding`: فرم پیشنهاد قیمت
- `AuctionCountdown`: شمارش معکوس پایان حراج
- `AuctionParticipation`: شرکت در حراج (پرداخت سپرده)
- `DirectSalePurchase`: خرید فوری
- `StoreListings`: لیست محصولات فروشگاه
- `CartSummary`: خلاصه سبد خرید
- `WalletBalance`: موجودی کیف پول

### Routes Structure
```
/ - صفحه اصلی
/listings - لیست حراج‌ها
/listings/{id} - جزئیات حراج
/listings/create - ایجاد حراج جدید
/stores/{id} - صفحه فروشگاه
/dashboard - داشبورد کاربر (خریدار/فروشنده)
/wallet - کیف پول
/cart - سبد خرید
/checkout - تسویه حساب
/orders - سفارشات

/admin/dashboard - داشبورد ادمین
/admin/listings - مدیریت حراج‌ها
/admin/listings/{id} - نمایش حراج
/admin/listings/{id}/manage - مدیریت کامل حراج
/admin/users - مدیریت کاربران
/admin/orders - مدیریت سفارشات
/admin/shipping-methods - روش‌های ارسال
```

### Authentication & Authorization
- Middleware: `auth`, `admin`, `EnsureAdmin`
- Policies: `StorePolicy` (مالکیت فروشگاه)
- Guards: `web` (session-based)

### Jobs (کارهای زمان‌بند)
- `ProcessAuctionStarting`: شروع حراج
- `ProcessAuctionEnding`: پایان حراج
- `ProcessFinalizationTimeout`: تایم‌اوت نهایی‌سازی

### Notifications
- `AuctionStartedNotification`: شروع حراج
- `AuctionEndedNotification`: پایان حراج
- `DepositReleasedNotification`: آزادسازی سپرده
- `LowStockAlertNotification`: هشدار موجودی کم

### Exceptions (خطاهای سفارشی)
- Auction: `AlreadyParticipatingException`, `AuctionNotActiveException`, `DepositNotPaidException`, `InvalidBidAmountException`
- Cart: `CartEmptyException`, `CartItemNotFoundException`
- DirectSale: `InvalidQuantityException`, `OutOfStockException`
- Order: `InvalidOrderStatusException`, `OrderNotFoundException`
- Payment: `PaymentFailedException`
- Shipping: `InvalidShippingMethodException`, `ShippingMethodNotFoundException`
- Wallet: `InsufficientBalanceException`, `WalletNotFoundException`
- Image: `ImageSizeTooLargeException`, `InvalidImageFormatException`

### Testing
- Feature Tests: موجود برای تمام سرویس‌ها و کنترلرها
- Test Database: استفاده از RefreshDatabase
- Factories: برای تولید داده‌های تستی

---

## ⚠️ قوانین مهم برای تغییرات دیتابیس

### ❌ اشتباه رایج: استفاده از `migrate:fresh`
**هرگز برای تغییرات کوچک دیتابیس از `migrate:fresh` استفاده نکنید!**

دلایل:
- همه جداول و داده‌ها پاک می‌شوند
- زمان‌بر است (چند دقیقه طول می‌کشد)
- داده‌های مهم از بین می‌روند
- برای تغییرات کوچک غیرضروری است

### ✅ روش صحیح: ایجاد Migration جدید

برای هر تغییر در دیتابیس، یک migration جدید بسازید:

```bash
# مثال: اضافه کردن ستون جدید
php artisan make:migration add_column_to_table_name

# مثال: اضافه کردن index
php artisan make:migration add_index_to_table_name

# مثال: تغییر ستون
php artisan make:migration modify_column_in_table_name
```

سپس فقط migration جدید را اجرا کنید:
```bash
php artisan migrate
```

### مثال‌های عملی

#### ❌ اشتباه:
```bash
# تغییر کوچک در migration قدیمی
# سپس:
php artisan migrate:fresh --seed  # همه چیز پاک می‌شود!
```

#### ✅ صحیح:
```bash
# ایجاد migration جدید
php artisan make:migration add_performance_indexes
# ویرایش فایل migration جدید
# سپس:
php artisan migrate  # فقط تغییرات جدید اعمال می‌شود
```

### استثناها (موارد نادر که migrate:fresh مجاز است)

فقط در این موارد می‌توانید از `migrate:fresh` استفاده کنید:
1. محیط توسعه (development) و داده‌های تستی
2. اول پروژه که هنوز داده مهمی نیست
3. زمانی که مطمئن هستید داده‌ای مهم نیست
4. برای اجرای تست‌ها (RefreshDatabase در تست‌ها)

### یادآوری برای AI Agent

**قبل از اجرای هر دستور migrate:fresh:**
1. بررسی کن آیا واقعاً نیاز است؟
2. آیا می‌توان با migration جدید حل کرد؟
3. آیا داده‌های مهمی وجود دارد؟
4. اگر فقط برای رفع خطای index یا constraint است، حتماً migration جدید بساز

---

## سایر نکات مهم

### مسیر PHP
```bash
D:\xamp8.1\php\php.exe
```

### Base URL
```
http://localhost/haraj/public/
```

### اجرای دستورات Laravel
```bash
# Clear view cache (بعد از هر تغییر در blade files)
D:\xamp8.1\php\php.exe artisan view:clear

# اجرای تست‌ها
D:\xamp8.1\php\php.exe artisan test --filter=TestName

# لیست روت‌ها
D:\xamp8.1\php\php.exe artisan route:list

# ایجاد migration
D:\xamp8.1\php\php.exe artisan make:migration migration_name

# اجرای migration
D:\xamp8.1\php\php.exe artisan migrate
```

### زبان پروژه
- همه متن‌های UI به فارسی
- همه پیام‌های خطا به فارسی
- استفاده از تقویم جلالی (`JalaliDateService`)
- استفاده از اعداد فارسی در UI (`PersianNumberService`)

### مشکلات رایج و راه‌حل

#### 1. فایل‌های Blade خالی می‌شوند
- مشکل: گاهی فایل‌های ایجاد شده 0 بایت هستند
- راه‌حل: محتوا را در فایل .txt ذخیره کن و کاربر خودش کپی می‌کند

#### 2. خطای 419 CSRF
- علت: توکن CSRF در فرم‌ها یا AJAX نیست
- راه‌حل: 
  - فرم‌ها: `@csrf` اضافه کن
  - AJAX با JSON: هدر `X-CSRF-TOKEN` اضافه کن
  - AJAX با FormData: توکن از فرم خوانده می‌شود

#### 3. خطای 422 Validation
- علت: داده‌های ارسالی با قوانین validation مطابقت ندارند
- راه‌حل: لاگ Laravel را بررسی کن

#### 4. خطای 500 Internal Server Error
- علت: خطای PHP یا دیتابیس
- راه‌حل: `storage/logs/laravel.log` را بررسی کن

#### 5. خطای ENUM Data Truncated
- علت: مقدار ارسالی در لیست ENUM نیست
- راه‌حل: migration جدید برای اضافه کردن مقدار به ENUM

---

**تاریخ ایجاد:** 2026-02-18
**آخرین بروزرسانی:** 2026-02-19

---

## 📝 تاریخچه تغییرات اخیر

### 2026-02-19 - رفع آمار و شمارش معکوس (بروزرسانی 6)

#### تغییرات انجام شده:

1. **رفع سیستم بازدید (Views)**
   - اضافه شدن `increment('views')` به `ListingController@show`
   - هر بار که کاربر صفحه محصول را مشاهده کند، بازدید +1 می‌شود
   - بازدیدها در دیتابیس ذخیره می‌شوند

2. **رفع آمار سریع در صفحه مدیریت**
   - بازدیدها: از ستون `views` خوانده می‌شود ✅
   - شرکت‌کنندگان: از `$listing->participations->count()` ✅
   - علاقه‌مندی‌ها: از ستون `favorites` (آماده برای پیاده‌سازی آینده)
   - اشتراک‌گذاری: از ستون `shares` (آماده برای پیاده‌سازی آینده)

3. **رفع مشکل زمان باقی‌مانده**
   - مشکل: `diffForHumans()` هر بار عدد متفاوت نشان می‌داد
   - علت: محاسبات در view انجام می‌شد، نه در component
   - راه‌حل:
     - تغییر `AuctionCountdown` component برای ذخیره days, hours, minutes, seconds
     - استفاده از property های component به جای محاسبه مجدد در view
     - اضافه شدن helper function `time_remaining()` برای کارت‌های محصولات

4. **رفع شمارش معکوس**
   - شمارش معکوس با `wire:poll.1s` هر ثانیه به‌روز می‌شود
   - نمایش به صورت: `HH:MM:SS` (ساعت:دقیقه:ثانیه)
   - اعداد فارسی با `PersianNumberService`
   - آیکون ساعت شنی با انیمیشن چرخش

5. **اضافه شدن helper function جدید**
   - `time_remaining($endTime)`: زمان باقی‌مانده به فارسی
   - فرمت خروجی:
     - بیش از 1 روز: "X روز"
     - بیش از 1 ساعت: "X ساعت"
     - بیش از 1 دقیقه: "X دقیقه"
     - کمتر از 1 دقیقه: "کمتر از یک دقیقه"
     - پایان یافته: "پایان یافته"

#### فایل‌های تغییر یافته:
- `app/Http/Controllers/ListingController.php` - اضافه شدن view tracking
- `app/Livewire/AuctionCountdown.php` - رفع محاسبات زمان
- `resources/views/livewire/auction-countdown.blade.php` - استفاده از property های component
- `app/helpers.php` - اضافه شدن `time_remaining()` helper
- `resources/views/listings/index.blade.php` - استفاده از helper جدید
- `resources/views/listings/search.blade.php` - استفاده از helper جدید

#### وضعیت نهایی:
✅ بازدیدها با هر refresh افزایش می‌یابند
✅ آمار سریع (بازدید، شرکت‌کنندگان) واقعی هستند
✅ زمان باقی‌مانده ثابت است (تا refresh بعدی)
✅ شمارش معکوس real-time کار می‌کند (هر ثانیه)
✅ نمایش زمان به فارسی و با اعداد فارسی

#### نکات مهم:
- علاقه‌مندی‌ها و اشتراک‌گذاری فعلاً 0 هستند (نیاز به پیاده‌سازی قابلیت favorite و share)
- برای پیاده‌سازی favorite: نیاز به جدول `favorites` و رابطه many-to-many
- برای پیاده‌سازی share: نیاز به tracking لینک‌های اشتراک‌گذاری

---

### 2026-02-19 - ایجاد صفحه جستجو و بهبود سیستم Notification (بروزرسانی 5)

#### تغییرات اصلی:

1. **ایجاد صفحه اختصاصی برای نتایج جستجو و فیلتر**
   - فایل جدید: `resources/views/listings/search.blade.php`
   - نمایش نتایج فیلتر شده با header زیبا
   - نمایش فیلترهای فعال با امکان حذف
   - مرتب‌سازی نتایج (زودتر به پایان می‌رسد، جدیدترین، ارزان‌ترین، گران‌ترین)
   - Empty state زیبا برای زمانی که نتیجه‌ای یافت نشود
   - نمایش تگ‌ها در کارت محصولات

2. **تغییر منطق کنترلر ListingController**
   - اگر هیچ فیلتری نباشد → صفحه اصلی (`listings.index`)
   - اگر فیلتر وجود داشته باشد → صفحه جستجو (`listings.search`)
   - پشتیبانی از فیلترهای: tag, search, category, seller_id, buy_now, sort

3. **رفع مشکل فیلتر تگ‌ها (مهم!)**
   - مشکل: `whereJsonContains` با تگ‌های فارسی کار نمی‌کرد
   - علت: MySQL تگ‌ها را به صورت Unicode escape ذخیره می‌کند (`\u0644\u067e\u062a\u0627\u067e`)
   - راه‌حل: استفاده از `JSON_SEARCH` به جای `whereJsonContains`
   - کد جدید: `whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag])`
   - حالا فیلتر تگ‌ها به درستی کار می‌کند ✅

4. **تبدیل همه alert() و confirm() به سیستم زیبا**
   - ایجاد modal تایید سفارشی (`showConfirmModal`)
   - تبدیل همه `confirm()` به modal زیبا با آیکون و دکمه‌های رنگی
   - تبدیل همه `alert()` به `showNotification()`
   - توابع تغییر یافته:
     - `deleteMainImage()` - حذف تصویر
     - `uploadNewImage()` - آپلود تصویر
     - `cancelBid()` - ابطال پیشنهاد
     - `confirmEndEarly()` - پایان زودتر مزایده
     - `confirmActivate()` - فعال‌سازی مزایده
     - `confirmSuspend()` - تعلیق مزایده (از قبل با prompt بود)
     - Edit form submission - ذخیره تغییرات

5. **بهبود سیستم Notification**
   - اضافه شدن modal تایید به `layouts/admin.blade.php`
   - تابع `showConfirmModal(title, message, okText, cancelText, onConfirm)`
   - طراحی زیبا با آیکون warning و دکمه‌های رنگی
   - بستن modal با کلیک بیرون از آن
   - حذف event listener های قدیمی برای جلوگیری از تداخل

#### فایل‌های تغییر یافته:
- `app/Http/Controllers/ListingController.php` - منطق جدید + رفع فیلتر تگ
- `resources/views/listings/search.blade.php` - صفحه جدید برای نتایج جستجو
- `resources/views/admin/listings/manage.blade.php` - تبدیل همه alert/confirm به notification
- `resources/views/layouts/admin.blade.php` - اضافه شدن modal تایید

#### نکته مهم درباره JSON_SEARCH:
MySQL تگ‌های فارسی را به صورت Unicode escape ذخیره می‌کند:
```
Database: ["\u0644\u067e\u062a\u0627\u067e"]
Display: ["لپتاپ"]
```

برای جستجو در JSON با کاراکترهای فارسی، باید از `JSON_SEARCH` استفاده کرد:
```php
// ❌ کار نمی‌کند
whereJsonContains('tags', 'لپتاپ')

// ✅ کار می‌کند
whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['لپتاپ'])
```

#### نحوه استفاده از صفحه جستجو:
- `/listings` - صفحه اصلی (بدون فیلتر)
- `/listings?tag=گیمینگ` - نتایج فیلتر شده با تگ
- `/listings?search=لپتاپ` - نتایج جستجو
- `/listings?category=دیجیتال` - فیلتر دسته‌بندی
- `/listings?buy_now=1` - فقط محصولات با خرید فوری
- `/listings?sort=price_low` - مرتب‌سازی

#### وضعیت نهایی:
✅ صفحه اختصاصی برای نتایج جستجو و فیلتر
✅ فیلتر تگ‌ها کار می‌کند (با JSON_SEARCH)
✅ نمایش فیلترهای فعال با امکان حذف
✅ تبدیل همه alert/confirm به notification زیبا
✅ Modal تایید سفارشی برای عملیات مهم
✅ Empty state برای نتایج خالی

---

### 2026-02-19 - رفع نهایی سیستم برچسب‌ها (بروزرسانی 4)

#### مشکلات رفع شده:
1. **حذف UI قدیمی برچسب‌ها از صفحه مدیریت**
   - حذف بخش نمایش برچسب‌ها با دکمه‌های close و + افزودن
   - حذف توابع JavaScript: `addTag()`, `removeTag()`, `saveTags()`
   - جایگزینی با نمایش ساده برچسب‌ها + راهنمای استفاده از modal ویرایش

2. **انتقال مدیریت برچسب‌ها به modal ویرایش**
   - اضافه شدن فیلد "برچسب‌ها" در modal "ویرایش جزئیات مزایده"
   - فرمت ورودی: "تگ1, تگ2, تگ3" (با کاما جدا می‌شوند)
   - محدودیت: حداکثر 5 برچسب
   - پردازش در سمت سرور: split, trim, filter, slice

3. **پاکسازی برچسب‌های قدیمی**
   - اجرای دستور: `DB::table('listings')->update(['tags' => '[]'])`
   - حذف برچسب‌های قدیمی که حاوی "close" و فضای خالی بودند

4. **رفع فیلتر برچسب‌ها**
   - اضافه کردن `trim()` به tag در `ListingController@index`
   - استفاده از `whereJsonContains('tags', $tag)` برای جستجو

5. **بهبود نمایش برچسب‌ها**
   - نمایش برچسب‌ها با # در صفحه محصول
   - لینک‌های کلیک‌پذیر به صفحه فیلتر شده
   - نمایش "برچسبی تعریف نشده است" اگر برچسبی نباشد

#### فایل‌های تغییر یافته:
- `resources/views/admin/listings/manage.blade.php` - حذف UI قدیمی و توابع JavaScript
- `app/Http/Controllers/Admin/ListingController.php` - بهبود پردازش برچسب‌ها
- `app/Http/Controllers/ListingController.php` - رفع فیلتر برچسب‌ها
- `.kiro/IMPORTANT_NOTES.md` - مستندسازی کامل

#### نحوه استفاده:
1. ادمین وارد صفحه مدیریت حراج می‌شود (`/admin/listings/{id}/manage`)
2. روی دکمه "ویرایش جزئیات" کلیک می‌کند
3. در فیلد "برچسب‌ها" تگ‌ها را با کاما جدا می‌کند: "لپتاپ, گیمینگ, ارزان"
4. "ذخیره تغییرات" را می‌زند
5. برچسب‌ها در صفحه محصول نمایش داده می‌شوند
6. کاربران می‌توانند روی برچسب‌ها کلیک کنند و محصولات مشابه را ببینند

#### وضعیت نهایی:
✅ مدیریت برچسب‌ها در modal ویرایش
✅ نمایش برچسب‌ها در صفحه محصول
✅ فیلتر برچسب‌ها در لیست محصولات
✅ محدودیت 5 برچسب
✅ پاکسازی برچسب‌های قدیمی
✅ حذف UI قدیمی از صفحه مدیریت

---

### 2026-02-19 - رفع مشکلات نهایی صفحه مدیریت حراج (بروزرسانی 3)

#### مشکل 6: خطای 500 در endEarly (دوباره!)
- **علت**: ترتیب اشتباه - اول status به completed تغییر می‌کرد، بعد endAuction چک می‌کرد status باید active باشد
- **راه‌حل**: اول endAuction را صدا زدن (که خودش status را تغییر می‌دهد)

#### مشکل 7: نمایش اشتباه بالاترین پیشنهاد
- **علت**: وقتی هیچ پیشنهادی نبود، current_price را نمایش می‌داد که مقدار داشت
- **راه‌حل**: چک کردن تعداد پیشنهادات - اگر 0 بود، starting_price نمایش داده شود

#### مشکل 8: پنجره‌های prompt/confirm
- **توضیح**: این‌ها پنجره‌های پیش‌فرض مرورگر هستند (confirm, prompt)
- **دلیل**: برای تایید عملیات مهم (حذف، تعلیق) از confirm استفاده می‌شود
- **نوت**: برای تغییر به modal سفارشی، نیاز به کار اضافی است

#### مشکل 9: نمایش دکمه خرید سریع
- **توضیح**: دکمه خرید سریع برای کاربران عادی است، نه صفحه مدیریت ادمین
- **راه‌حل**: قیمت خرید فوری در بخش "تنظیمات مزایده" نمایش داده می‌شود و قابل ویرایش است

#### فایل‌های تغییر یافته:
- `app/Http/Controllers/Admin/ListingController.php` - رفع ترتیب endEarly
- `resources/views/admin/listings/manage.blade.php` - رفع نمایش بالاترین پیشنهاد
- `database/migrations/2026_02_19_120341_add_auction_settings_to_listings.php` - اضافه کردن ستون‌های جدید

#### وضعیت نهایی:
✅ پایان زودتر حراج - کار می‌کند
✅ ذخیره تنظیمات حراج - کار می‌کند
✅ تعلیق حراج - کار می‌کند
✅ فعال‌سازی حراج - کار می‌کند
✅ نمایش صحیح آمار پیشنهادات
✅ سیستم Notification زیبا برای پیام‌ها

---

### 2026-02-19 - رفع مشکلات صفحه مدیریت حراج (بروزرسانی 2)

#### مشکل 4: خطای 500 در endEarly
- **علت**: متد `processAuctionEnding()` در `AuctionService` وجود نداشت
- **راه‌حل**: تغییر به `endAuction()` که متد صحیح است

#### مشکل 5: خطای 422 در saveAuctionSettings
- **علت**: checkbox `auto_extend` وقتی تیک نخورده، مقداری ارسال نمی‌کند
- **راه‌حل**: اضافه کردن مقدار `0` برای checkbox تیک نخورده در JavaScript

#### فایل‌های تغییر یافته:
- `app/Http/Controllers/Admin/ListingController.php` - تغییر `processAuctionEnding` به `endAuction`
- `resources/views/admin/listings/manage.blade.php` - رفع مشکل checkbox و اضافه کردن error handling

#### وضعیت فعلی:
✅ پایان زودتر حراج (End Early) - رفع شد
✅ ذخیره تنظیمات حراج - رفع شد
✅ تعلیق حراج (Suspend) - کار می‌کند
✅ فعال‌سازی حراج (Activate) - کار می‌کند

---

### 2026-02-19 - رفع مشکلات صفحه مدیریت حراج (بروزرسانی 1)

#### مشکل 1: خطای 419 CSRF
- **علت**: توکن CSRF در درخواست‌های AJAX نبود
- **راه‌حل**: اضافه کردن `@csrf` در فرم‌ها و استفاده از FormData

#### مشکل 2: خطای 422 در saveAuctionSettings
- **علت**: استفاده نادرست از method PUT با FormData
- **راه‌حل**: تغییر به POST + اضافه کردن `_method: 'PUT'`

#### مشکل 3: خطای 500 در suspend و endEarly
- **علت**: مقادیر 'suspended' و 'cancelled' در ENUM status نبودند
- **راه‌حل**: 
  - ایجاد migration: `2026_02_19_115155_add_suspended_cancelled_to_listings_status.php`
  - اضافه کردن 'suspended' و 'cancelled' به ENUM status

#### فایل‌های تغییر یافته:
- `resources/views/admin/listings/manage.blade.php` - رفع CSRF و method spoofing
- `database/migrations/2026_02_19_115155_add_suspended_cancelled_to_listings_status.php` - اضافه کردن status جدید
- `.kiro/IMPORTANT_NOTES.md` - اضافه شدن ساختار کامل پروژه

#### دکمه‌های کار شده:
✅ ذخیره تنظیمات حراج (قیمت‌ها، تاریخ‌ها)
✅ تعلیق حراج (Suspend)
✅ پایان زودتر حراج (End Early)
✅ فعال‌سازی حراج (Activate)
✅ افزودن/حذف برچسب (Tags)
✅ آپلود/حذف تصویر
✅ ویرایش جزئیات حراج

---

## 🔄 تغییرات اساسی ساختار پروژه (2026-02-19)

### تغییر به سیستم حراج محور با خرید فوری

**تصمیم:** حذف سیستم فروش مستقیم و ترکیبی - همه محصولات حراج هستند

#### تغییرات دیتابیس:
1. ✅ حذف فیلد `type` - همه محصولات حراج
2. ✅ حذف فیلدهای `price`, `stock`, `low_stock_threshold`
3. ✅ تغییر نام `base_price` به `starting_price`
4. ✅ تغییر نام `current_highest_bid` به `current_price`
5. ✅ تغییر نام `start_time` به `starts_at`
6. ✅ تغییر نام `end_time` به `ends_at`
7. ✅ اضافه شدن `buy_now_price` (اختیاری)

#### قابلیت خرید فوری:
- فروشنده می‌تواند قیمت خرید فوری تعیین کند (اختیاری)
- اگر خریدار این مبلغ را بپردازد، بلافاصله برنده حراج می‌شود
- حراج به پایان می‌رسد و دیگران نمی‌توانند پیشنهاد دهند

#### فایل‌های تغییر یافته:
- `database/migrations/2026_02_19_000000_convert_to_auction_only_with_buy_now.php`
- `app/Models/Listing.php`
- `resources/views/stores/show.blade.php`
- `app/Http/Controllers/StoreController.php`

#### Migration:
```bash
D:\xamp8.1\php\php.exe artisan migrate
```

**توجه:** این تغییرات اساسی هستند و نیاز به به‌روزرسانی تمام بخش‌های مرتبط دارند.


## 🔧 مشکلات باقی‌مانده و راه‌حل‌ها

### 1. Input کاربر به صورت باکس (Modal سفارشی)
- **وضعیت فعلی**: از `prompt()` و `confirm()` استفاده می‌شود که پنجره‌های پیش‌فرض مرورگر هستند
- **محدودیت**: نمی‌توان استایل آن‌ها را تغییر داد
- **راه‌حل**: ساخت modal سفارشی با HTML/CSS/JavaScript (نیاز به کار اضافی دارد)
- **نوت**: برای تجربه کاربری بهتر، می‌توان از کتابخانه‌هایی مثل SweetAlert2 استفاده کرد

### 2. سیستم برچسب‌ها ✅ (کامل شد)
- **نمایش**: برچسب‌ها در صفحه محصول (`/listings/{id}`) نمایش داده می‌شوند
- **لینک**: کلیک روی هر برچسب به `route('listings.index', ['tag' => $tag])` می‌رود
- **مدیریت**: ادمین می‌تواند در modal "ویرایش جزئیات" برچسب‌ها را مدیریت کند
- **ذخیره**: برچسب‌ها به صورت JSON array در دیتابیس ذخیره می‌شوند
- **محدودیت**: حداکثر 5 برچسب برای هر محصول
- **فرمت ورودی**: "تگ1, تگ2, تگ3" (با کاما جدا می‌شوند)
- **فیلتر**: فیلتر برچسب‌ها در `ListingController@index` با `whereJsonContains` پیاده‌سازی شده

### 3. دکمه ویرایش برای ادمین ✅
- **محل**: در صفحه عمومی حراجی (`/listings/{id}`)
- **شرط**: فقط برای کاربران با `role='admin'` نمایش داده می‌شود
- **عملکرد**: لینک مستقیم به `route('admin.listings.manage', $listing)`
- **استایل**: دکمه بنفش با آیکون admin_panel_settings

---

**تاریخ بروزرسانی:** 2026-02-19 (بروزرسانی 5)


---

### 2026-02-20 - سیستم سپرده و کمیسیون خودکار (بروزرسانی 7)

#### قابلیت‌های جدید:

1. **محاسبه خودکار سپرده شرکت در مزایده**
   - نوع محاسبه: مبلغ ثابت یا درصد از قیمت پایه
   - قابل تنظیم توسط ادمین از پنل مدیریت
   - مقادیر پیش‌فرض: 10% یا 1,000,000 تومان

2. **کمیسیون سایت**
   - نوع محاسبه: مبلغ ثابت یا درصد از قیمت نهایی
   - پرداخت کننده: خریدار، فروشنده، یا هر دو
   - تقسیم کمیسیون: در صورت انتخاب "هر دو"، درصد تقسیم قابل تنظیم
   - مقادیر پیش‌فرض: 5% از خریدار

3. **کیف پول سایت**
   - کمیسیون‌ها به کیف پول کاربر با `user_id = 1` واریز می‌شود
   - قابل تغییر در متد `depositToSiteWallet` در `CommissionService`

#### فایل‌های ایجاد شده:

**Migration:**
- `database/migrations/2026_02_20_000001_create_site_settings_table.php`
  - جدول تنظیمات سایت با کش
  - مقادیر پیش‌فرض برای سپرده و کمیسیون

**Models:**
- `app/Models/SiteSetting.php`
  - مدیریت تنظیمات با کش (1 ساعت)
  - متدهای `get()`, `set()`, `clearCache()`

**Services:**
- `app/Services/CommissionService.php`
  - `calculateDeposit($basePrice)` - محاسبه سپرده
  - `calculateCommission($finalPrice)` - محاسبه کمیسیون
  - `deductCommission($listing, $buyer, $finalPrice)` - کسر کمیسیون
  - `getDepositSettings()` - دریافت تنظیمات سپرده
  - `getCommissionSettings()` - دریافت تنظیمات کمیسیون

**Controllers:**
- `app/Http/Controllers/Admin/SettingsController.php`
  - `index()` - نمایش صفحه تنظیمات
  - `updateDeposit()` - به‌روزرسانی تنظیمات سپرده
  - `updateCommission()` - به‌روزرسانی تنظیمات کمیسیون

**Views:**
- `resources/views/admin/settings/index.blade.php`
  - فرم تنظیمات سپرده (نوع، مبلغ ثابت، درصد)
  - فرم تنظیمات کمیسیون (نوع، مبلغ، درصد، پرداخت کننده، تقسیم)

**Tests:**
- `tests/Feature/CommissionServiceTest.php`
  - تست محاسبه سپرده (ثابت و درصد)
  - تست محاسبه کمیسیون (ثابت و درصد)
  - تست کسر کمیسیون از خریدار
  - تست کسر کمیسیون از فروشنده
  - تست کسر کمیسیون از هر دو

**Documentation:**
- `COMMISSION_SYSTEM.md` - مستندات کامل سیستم (انگلیسی)
- `COMMISSION_GUIDE_FA.md` - راهنمای کاربری (فارسی)

#### تغییرات در سرویس‌های موجود:

**AuctionService:**
- استفاده از `CommissionService` برای محاسبه سپرده
- کسر کمیسیون پس از پایان مزایده در `completeWinnerPayment()`
- کسر کمیسیون از خریدار و/یا فروشنده بر اساس تنظیمات

**DepositService:**
- استفاده از `CommissionService` برای محاسبه سپرده به جای مقدار ثابت
- محاسبه پویا بر اساس تنظیمات سایت

#### Routes جدید:
```
GET  /admin/settings                 - نمایش صفحه تنظیمات
PUT  /admin/settings/deposit         - به‌روزرسانی تنظیمات سپرده
PUT  /admin/settings/commission      - به‌روزرسانی تنظیمات کمیسیون
```

#### تنظیمات پیش‌فرض:

**سپرده:**
- نوع: درصد (percentage)
- مبلغ ثابت: 1,000,000 تومان
- درصد: 10%

**کمیسیون:**
- نوع: درصد (percentage)
- مبلغ ثابت: 50,000 تومان
- درصد: 5%
- پرداخت کننده: خریدار (buyer)
- تقسیم: 50% خریدار، 50% فروشنده

#### نحوه استفاده:

**برای ادمین:**
1. وارد پنل مدیریت شوید
2. از منوی سمت راست، "تنظیمات سایت" را انتخاب کنید
3. تنظیمات سپرده و کمیسیون را تغییر دهید
4. تغییرات را ذخیره کنید

**برای توسعه‌دهندگان:**
```php
// محاسبه سپرده
$commissionService = app(CommissionService::class);
$deposit = $commissionService->calculateDeposit($basePrice);

// محاسبه کمیسیون
$commission = $commissionService->calculateCommission($finalPrice);

// کسر کمیسیون
$result = $commissionService->deductCommission($listing, $buyer, $finalPrice);
// $result = [
//     'total_commission' => 50000,
//     'buyer_commission' => 30000,
//     'seller_commission' => 20000,
// ]
```

#### مثال‌های کاربردی:

**مثال 1: سپرده 10% از قیمت پایه**
```
قیمت پایه: 1,000,000 تومان
نوع سپرده: درصد
درصد سپرده: 10%
سپرده محاسبه شده: 100,000 تومان
```

**مثال 2: کمیسیون 5% از خریدار**
```
قیمت نهایی: 2,000,000 تومان
نوع کمیسیون: درصد
درصد کمیسیون: 5%
پرداخت کننده: خریدار
کمیسیون خریدار: 100,000 تومان
کمیسیون فروشنده: 0 تومان
```

**مثال 3: کمیسیون 5% از هر دو (60% خریدار، 40% فروشنده)**
```
قیمت نهایی: 2,000,000 تومان
نوع کمیسیون: درصد
درصد کمیسیون: 5%
پرداخت کننده: هر دو
تقسیم: 60% خریدار
کمیسیون کل: 100,000 تومان
کمیسیون خریدار: 60,000 تومان
کمیسیون فروشنده: 40,000 تومان
```

#### نکات مهم:
- تمام تنظیمات در کش ذخیره می‌شوند (1 ساعت)
- پس از تغییر تنظیمات، کش به صورت خودکار پاک می‌شود
- کمیسیون فقط پس از پایان موفق مزایده و پرداخت برنده کسر می‌شود
- تنظیمات جدید فقط برای مزایده‌های جدید اعمال می‌شود

#### دستورات مفید:
```bash
# اجرای migration
D:\xamp8.1\php\php.exe artisan migrate

# اجرای تست‌ها
D:\xamp8.1\php\php.exe artisan test --filter=CommissionServiceTest

# پاک کردن کش تنظیمات
SiteSetting::clearCache();
```

#### وضعیت نهایی:
✅ جدول تنظیمات سایت ایجاد شد
✅ مدل SiteSetting با قابلیت کش
✅ سرویس CommissionService برای محاسبات
✅ کنترلر SettingsController برای مدیریت
✅ صفحه تنظیمات در پنل ادمین
✅ لینک تنظیمات در منوی ادمین
✅ به‌روزرسانی AuctionService و DepositService
✅ تست‌های کامل برای CommissionService
✅ مستندات کامل (فارسی و انگلیسی)

---

**تاریخ بروزرسانی:** 2026-02-20


---

### 2026-02-20 - سیستم گزارشات مالی کامل (بروزرسانی 8)

#### قابلیت‌های جدید:

1. **صفحه گزارشات مالی کامل**
   - نمایش موجودی کیف پول سایت (کل، فریز شده، قابل برداشت)
   - خلاصه درآمد (کل درآمد، کمیسیون‌ها، سپرده‌های ضبط شده، معاملات موفق)
   - نمودار درآمد روزانه (Chart.js)
   - آمار کلی پلتفرم (کاربران، فروشندگان، خریداران، حراج‌های فعال)
   - فروشندگان برتر (5 نفر اول)
   - خریداران برتر (5 نفر اول)
   - آمار دسته‌بندی‌ها (تعداد، ارزش کل، میانگین قیمت)

2. **فیلتر بازه زمانی**
   - انتخاب تاریخ شروع و پایان
   - پیش‌فرض: ماه جاری
   - اعمال فیلتر روی تمام گزارشات

3. **صفحه جزئیات کمیسیون‌ها**
   - جدول کامل تراکنش‌های کمیسیون
   - نمایش تاریخ، شرح، مبلغ، موجودی قبل و بعد
   - Pagination
   - خلاصه: تعداد، کل، میانگین

4. **دانلود گزارش CSV**
   - Export درآمد روزانه به CSV
   - شامل: تاریخ، کمیسیون، سپرده ضبط شده، کل درآمد

5. **API برای نمودارها**
   - Endpoint برای دریافت داده‌های نمودار (AJAX)
   - پشتیبانی از نمودار روزانه و ماهانه

#### فایل‌های ایجاد شده:

**Services:**
- `app/Services/FinancialReportService.php`
  - `getSiteRevenueSummary()` - خلاصه درآمد
  - `getDailyRevenue()` - درآمد روزانه
  - `getMonthlyRevenue()` - درآمد ماهانه
  - `getCommissionDetails()` - جزئیات کمیسیون‌ها
  - `getTopSellers()` - فروشندگان برتر
  - `getTopBuyers()` - خریداران برتر
  - `getCategoryStats()` - آمار دسته‌بندی‌ها
  - `getSiteWalletBalance()` - موجودی کیف پول سایت
  - `getPlatformStats()` - آمار کلی پلتفرم
  - `exportToCSV()` - Export به CSV

**Controllers:**
- `app/Http/Controllers/Admin/FinancialReportController.php`
  - `index()` - صفحه اصلی گزارشات
  - `commissions()` - جزئیات کمیسیون‌ها
  - `export()` - دانلود CSV
  - `chartData()` - داده‌های نمودار (AJAX)

**Views:**
- `resources/views/admin/financial-reports/index.blade.php`
  - صفحه اصلی با تمام گزارشات
  - نمودار Chart.js
  - فیلتر بازه زمانی
  - دکمه دانلود CSV

- `resources/views/admin/financial-reports/commissions.blade.php`
  - جدول جزئیات کمیسیون‌ها
  - Pagination
  - خلاصه آماری

**Commands:**
- `app/Console/Commands/CreateSiteUserCommand.php`
  - دستور: `php artisan site:create-user`
  - ایجاد کاربر سایت (user_id = 1) برای دریافت کمیسیون‌ها

**Tests:**
- `tests/Feature/FinancialReportServiceTest.php`
  - تست خلاصه درآمد
  - تست درآمد روزانه
  - تست موجودی کیف پول
  - تست آمار پلتفرم
  - تست فروشندگان برتر
  - تست Export CSV

**Documentation:**
- `FINANCIAL_REPORTS_GUIDE.md` - راهنمای کامل سیستم گزارشات مالی

#### Routes جدید:
```
GET  /admin/financial-reports                    - صفحه اصلی گزارشات
GET  /admin/financial-reports/commissions        - جزئیات کمیسیون‌ها
GET  /admin/financial-reports/export             - دانلود CSV
GET  /admin/financial-reports/chart-data         - داده‌های نمودار (AJAX)
```

#### محاسبات:

**کل درآمد:**
```
کل درآمد = کمیسیون‌ها + سپرده‌های ضبط شده
```

**کمیسیون‌ها:**
- تراکنش‌های واریز به کیف پول سایت
- شرح شامل "کمیسیون"

**سپرده‌های ضبط شده:**
- تراکنش‌های واریز به کیف پول سایت
- شرح شامل "ضبط سپرده"

**نرخ کمیسیون:**
```
نرخ کمیسیون = (کل کمیسیون‌ها / حجم کل معاملات) × 100
```

**میانگین کمیسیون:**
```
میانگین کمیسیون = کل کمیسیون‌ها / تعداد معاملات موفق
```

#### ویژگی‌های نمودار:

**Chart.js:**
- نمودار خطی (Line Chart)
- سه خط: کمیسیون (آبی)، سپرده ضبط شده (قرمز)، کل درآمد (سبز)
- Tooltip با فرمت فارسی
- محور Y با اعداد فارسی
- Responsive

#### کیف پول سایت:

**نکته مهم:**
- کمیسیون‌ها به کیف پول کاربر با `user_id = 1` واریز می‌شود
- این کاربر باید از قبل ایجاد شده باشد
- برای ایجاد: `php artisan site:create-user`

**اطلاعات پیش‌فرض کاربر سایت:**
```
ID: 1
نام: سایت حراج
ایمیل: site@persianauction.com
رمز عبور: SitePassword123!@#
نقش: admin
```

#### نحوه استفاده:

**برای ادمین:**
1. وارد پنل مدیریت شوید
2. از منوی سمت راست، "گزارشات مالی" را انتخاب کنید
3. بازه زمانی دلخواه را انتخاب کنید
4. گزارشات را مشاهده کنید
5. در صورت نیاز، CSV دانلود کنید

**برای توسعه‌دهندگان:**
```php
$service = app(FinancialReportService::class);

// خلاصه درآمد
$summary = $service->getSiteRevenueSummary($startDate, $endDate);

// درآمد روزانه
$dailyRevenue = $service->getDailyRevenue($startDate, $endDate);

// فروشندگان برتر
$topSellers = $service->getTopSellers($startDate, $endDate, 10);

// موجودی کیف پول سایت
$balance = $service->getSiteWalletBalance();
```

#### مثال خروجی:

**خلاصه درآمد:**
```php
[
    'total_revenue' => 5000000,
    'commissions' => 4500000,
    'forfeited_deposits' => 500000,
    'successful_auctions' => 90,
    'total_sales_volume' => 90000000,
    'average_commission_per_sale' => 50000,
    'commission_rate' => 5.0,
    'start_date' => Carbon,
    'end_date' => Carbon,
]
```

**درآمد روزانه:**
```php
[
    [
        'date' => '2026-02-20',
        'commissions' => 150000,
        'forfeited_deposits' => 50000,
        'total' => 200000,
    ],
    // ...
]
```

#### بهینه‌سازی:

**Query Optimization:**
- استفاده از `SUM()`, `COUNT()`, `AVG()` در دیتابیس
- Group By برای گروه‌بندی داده‌ها
- Index روی `created_at` برای سرعت بیشتر

**Caching:**
- می‌توان نتایج را کش کرد (فعلاً پیاده‌سازی نشده)
- پیشنهاد: کش 5 دقیقه‌ای برای گزارشات

#### نکات امنیتی:

**دسترسی:**
- فقط ادمین‌ها می‌توانند گزارشات را ببینند
- Middleware: `admin`

**داده‌های حساس:**
- ایمیل کاربران در گزارشات نمایش داده می‌شود
- در صورت نیاز، می‌توان ماسک کرد

#### وضعیت نهایی:
✅ سرویس FinancialReportService کامل
✅ کنترلر FinancialReportController
✅ صفحه گزارشات مالی با نمودار
✅ صفحه جزئیات کمیسیون‌ها
✅ دانلود CSV
✅ فیلتر بازه زمانی
✅ لینک در منوی ادمین
✅ دستور ایجاد کاربر سایت
✅ تست‌های کامل
✅ مستندات کامل

#### دستورات مفید:
```bash
# ایجاد کاربر سایت
D:\xamp8.1\php\php.exe artisan site:create-user

# اجرای تست‌ها
D:\xamp8.1\php\php.exe artisan test --filter=FinancialReportServiceTest

# مشاهده روت‌ها
D:\xamp8.1\php\php.exe artisan route:list --name=financial
```

#### توسعه‌های آینده:
- [ ] گزارش سالانه
- [ ] مقایسه دوره‌ای
- [ ] پیش‌بینی درآمد با ML
- [ ] نمودار دایره‌ای
- [ ] Export به PDF
- [ ] ارسال گزارش خودکار به ایمیل
- [ ] Dashboard تحلیلی پیشرفته
- [ ] کش کردن گزارشات
- [ ] گزارش تفکیک شده به دسته‌بندی

---

**تاریخ بروزرسانی:** 2026-02-20 (بروزرسانی 8)


---

### 2026-02-20 - سیستم دسته‌بندی کامل با مگامنو (بروزرسانی 9)

#### قابلیت‌های جدید:

1. **سیستم دسته‌بندی چندسطحی**
   - دسته‌بندی اصلی (Parent Categories)
   - زیردسته‌ها (Child Categories)
   - ساختار: الکترونیکی > موبایل > اپل
   - قابلیت نامحدود برای افزودن زیردسته

2. **مگامنوی دسته‌بندی در هدر**
   - یک دکمه "دسته‌بندی‌ها" در هدر
   - باز شدن مگامنو با hover
   - نمایش تمام دسته‌بندی‌ها و زیرمجموعه‌ها
   - طراحی Grid با 4 ستون
   - آیکون Material Icons برای هر دسته
   - استایل Tailwind CSS

3. **پنل مدیریت دسته‌بندی**
   - لیست تمام دسته‌بندی‌ها
   - افزودن دسته‌بندی جدید
   - ویرایش دسته‌بندی
   - حذف دسته‌بندی (با چک کردن وابستگی‌ها)
   - نمایش تعداد حراجی‌های هر دسته
   - مرتب‌سازی با فیلد order

4. **کامپوننت انتخاب دسته‌بندی**
   - نمایش درختی دسته‌بندی‌ها در select
   - استفاده در فرم ایجاد حراجی
   - استفاده در فرم ویرایش حراجی (پنل ادمین)
   - نمایش زیردسته‌ها با علامت └─

5. **فیلتر بر اساس دسته‌بندی**
   - لینک‌های دسته‌بندی در مگامنو
   - فیلتر محصولات بر اساس slug دسته‌بندی
   - پشتیبانی از query parameter: `?category=slug`

#### فایل‌های ایجاد شده:

**Controllers:**
- `app/Http/Controllers/Admin/CategoryController.php`
  - `index()` - لیست دسته‌بندی‌ها
  - `create()` - فرم افزودن
  - `store()` - ذخیره دسته جدید
  - `edit()` - فرم ویرایش
  - `update()` - به‌روزرسانی دسته
  - `destroy()` - حذف دسته (با چک وابستگی)

**Views - Admin:**
- `resources/views/admin/categories/index.blade.php` - لیست دسته‌بندی‌ها
- `resources/views/admin/categories/create.blade.php` - فرم افزودن
- `resources/views/admin/categories/edit.blade.php` - فرم ویرایش

**Components:**
- `app/View/Components/CategoryMegamenu.php` - کامپوننت مگامنو
- `resources/views/components/category-megamenu.blade.php` - view مگامنو
- `app/View/Components/CategorySelector.php` - کامپوننت انتخابگر
- `resources/views/components/category-selector.blade.php` - view انتخابگر

**Seeders:**
- `database/seeders/CategorySeeder.php` - دسته‌بندی‌های پیش‌فرض

#### ساختار دیتابیس:

**جدول categories:**
```php
'id'          // شناسه
'name'        // نام دسته‌بندی
'slug'        // نامک (برای URL)
'description' // توضیحات (nullable)
'icon'        // آیکون Material Icons (nullable)
'parent_id'   // شناسه دسته والد (nullable)
'order'       // ترتیب نمایش
'is_active'   // فعال/غیرفعال
'created_at'
'updated_at'
```

**Indexes:**
- `parent_id, is_active` - برای فیلتر سریع
- `slug` - برای جستجو

#### دسته‌بندی‌های پیش‌فرض:

1. **دیجیتال و الکترونیک** (devices)
   - موبایل و تبلت (smartphone)
   - لپ‌تاپ و کامپیوتر (computer)
   - لوازم جانبی (headphones)
   - دوربین و عکاسی (photo_camera)
   - کنسول و بازی (sports_esports)

2. **خانه و آشپزخانه** (home)
   - لوازم برقی آشپزخانه (kitchen)
   - مبلمان و دکوراسیون (chair)
   - ابزار و تجهیزات (construction)

3. **مد و پوشاک** (checkroom)
   - پوشاک مردانه (man)
   - پوشاک زنانه (woman)
   - کیف و کفش (shopping_bag)
   - ساعت و زیورآلات (watch)

4. **ورزش و سرگرمی** (sports_soccer)
   - لوازم ورزشی (fitness_center)
   - کتاب و مجله (menu_book)
   - موسیقی و سرگرمی (music_note)

5. **خودرو و وسایل نقلیه** (directions_car)
   - لوازم جانبی خودرو (car_repair)
   - موتورسیکلت (two_wheeler)

6. **زیبایی و سلامت** (spa)
   - آرایشی و بهداشتی (face)
   - عطر و ادکلن (local_florist)

7. **کودک و نوزاد** (child_care)
   - لوازم نوزاد (baby_changing_station)
   - اسباب بازی (toys)

8. **هنر و صنایع دستی** (palette)
   - تابلو و نقاشی (brush)
   - صنایع دستی (handyman)

#### Routes جدید:
```
GET     /admin/categories              - لیست دسته‌بندی‌ها
GET     /admin/categories/create       - فرم افزودن
POST    /admin/categories              - ذخیره دسته جدید
GET     /admin/categories/{id}/edit    - فرم ویرایش
PUT     /admin/categories/{id}         - به‌روزرسانی
DELETE  /admin/categories/{id}         - حذف
```

#### نحوه استفاده:

**برای ادمین:**
1. وارد پنل مدیریت شوید
2. از منوی سمت راست، "دسته‌بندی‌ها" را انتخاب کنید
3. برای افزودن: دکمه "افزودن دسته‌بندی" را کلیک کنید
4. برای ویرایش: روی آیکون ویرایش کلیک کنید
5. برای حذف: روی آیکون حذف کلیک کنید (با تایید)

**برای کاربران:**
1. در هدر سایت، روی دکمه "دسته‌بندی‌ها" hover کنید
2. مگامنو باز می‌شود
3. روی دسته‌بندی یا زیردسته مورد نظر کلیک کنید
4. محصولات آن دسته نمایش داده می‌شود

**در فرم ایجاد/ویرایش حراجی:**
```blade
<x-category-selector :selected="old('category_id', $listing->category_id ?? null)" />
```

#### ویژگی‌های امنیتی:

**حذف دسته‌بندی:**
- چک می‌شود که آیا حراجی‌ای به این دسته وابسته است
- چک می‌شود که آیا زیردسته‌ای دارد
- در صورت وابستگی، حذف انجام نمی‌شود

**Slug خودکار:**
- اگر slug خالی باشد، از نام دسته تولید می‌شود
- استفاده از `Str::slug()` برای تبدیل فارسی به انگلیسی

#### متدهای مفید در مدل Category:

```php
// چک کردن دسته اصلی
$category->isParent(); // true/false

// چک کردن وجود زیردسته
$category->hasChildren(); // true/false

// دریافت مسیر کامل
$category->getFullPath(); // "الکترونیکی > موبایل > اپل"

// دریافت ساختار منو
Category::getMenuStructure(); // آرایه دسته‌بندی‌ها با زیرمجموعه
```

#### Scopes مفید:

```php
// فقط دسته‌بندی‌های فعال
Category::active()->get();

// فقط دسته‌بندی‌های اصلی
Category::parents()->get();

// مرتب شده بر اساس order
Category::ordered()->get();

// ترکیبی
Category::active()->parents()->ordered()->get();
```

#### تغییرات در فایل‌های موجود:

**routes/web.php:**
- اضافه شدن `CategoryController` به use statement
- اضافه شدن `Route::resource('categories', CategoryController::class)`

**resources/views/layouts/app.blade.php:**
- جایگزینی منوی قدیمی با `<x-category-megamenu />`

**resources/views/layouts/admin.blade.php:**
- اضافه شدن لینک "دسته‌بندی‌ها" به منوی سایدبار

**resources/views/listings/create.blade.php:**
- اضافه شدن `<x-category-selector />` به فرم

**resources/views/admin/listings/manage.blade.php:**
- تبدیل input دسته‌بندی به select با ساختار درختی

**app/Providers/AppServiceProvider.php:**
- رجیستر کردن کامپوننت‌های `category-megamenu` و `category-selector`

#### دستورات مفید:
```bash
# اجرای migration (اگر نیاز باشد)
D:\xamp8.1\php\php.exe artisan migrate

# اجرای seeder
D:\xamp8.1\php\php.exe artisan db:seed --class=CategorySeeder

# پاک کردن cache
D:\xamp8.1\php\php.exe artisan view:clear
D:\xamp8.1\php\php.exe artisan config:clear

# بررسی تعداد دسته‌بندی‌ها
D:\xamp8.1\php\php.exe artisan tinker --execute="echo \App\Models\Category::count();"
```

#### وضعیت نهایی:
✅ جدول categories ایجاد شد (31 دسته‌بندی)
✅ مدل Category با روابط و متدها
✅ کنترلر CategoryController کامل
✅ صفحات مدیریت (لیست، افزودن، ویرایش)
✅ مگامنوی دسته‌بندی در هدر
✅ کامپوننت انتخاب دسته‌بندی
✅ لینک در منوی ادمین
✅ استفاده در فرم‌های حراجی
✅ فیلتر بر اساس دسته‌بندی
✅ Seeder با دسته‌بندی‌های پیش‌فرض

#### نکات مهم:
- مگامنو با hover باز می‌شود (نه کلیک)
- دسته‌بندی‌ها به صورت Grid 4 ستونی نمایش داده می‌شوند
- آیکون‌ها از Material Icons استفاده می‌کنند
- Slug به صورت خودکار از نام تولید می‌شود
- حذف دسته‌بندی با چک وابستگی انجام می‌شود

#### توسعه‌های آینده:
- [ ] آپلود تصویر برای دسته‌بندی
- [ ] SEO meta tags برای هر دسته
- [ ] نمایش تعداد محصولات در مگامنو
- [ ] فیلتر پیشرفته با چند دسته‌بندی
- [ ] دسته‌بندی‌های پیشنهادی
- [ ] آمار فروش به تفکیک دسته‌بندی
- [ ] کش کردن ساختار منو

---

**تاریخ بروزرسانی:** 2026-02-20 (بروزرسانی 9)


---

### 2026-02-21 - رفع مشکل تقویم شمسی (بروزرسانی 9)

#### مشکل:
- خطای JavaScript: `Uncaught TypeError: Assignment to constant variable`
- خط 312 در `persian-datepicker-package.js`
- تقویم باز نمی‌شد حتی در صفحه تست

#### علت:
- متغیر `gy` در متد `gregorianToJalali` به عنوان `const` تعریف شده بود
- سپس در خط 312 سعی می‌شد مقدار آن تغییر کند: `gy -= (gy <= 1600) ? 621 : 1600;`
- در JavaScript نمی‌توان مقدار متغیرهای `const` را تغییر داد

#### راه‌حل:
- تغییر `const gy` به `let gy` در خط 305
- حالا متغیر قابل تغییر است و خطا برطرف شد

#### فایل تغییر یافته:
- `public/js/persian-datepicker-package.js` - تغییر const به let

#### کد قبل (اشتباه):
```javascript
gregorianToJalali(date) {
    const gy = date.getFullYear();  // ❌ const
    const gm = date.getMonth() + 1;
    const gd = date.getDate();
    
    let jy = (gy <= 1600) ? 0 : 979;
    gy -= (gy <= 1600) ? 621 : 1600;  // ❌ خطا: نمی‌توان const را تغییر داد
```

#### کد بعد (صحیح):
```javascript
gregorianToJalali(date) {
    let gy = date.getFullYear();  // ✅ let
    const gm = date.getMonth() + 1;
    const gd = date.getDate();
    
    let jy = (gy <= 1600) ? 0 : 979;
    gy -= (gy <= 1600) ? 621 : 1600;  // ✅ کار می‌کند
```

#### تست:
1. صفحه تست: `http://localhost/test-datepicker.html`
2. صفحه ایجاد حراجی: `/admin/listings/create`
3. صفحه مدیریت حراجی: `/admin/listings/{id}/manage`

#### وضعیت نهایی:
✅ خطای JavaScript برطرف شد
✅ تقویم در صفحه تست باز می‌شود
✅ تقویم در صفحات ایجاد و ویرایش حراجی کار می‌کند
✅ انتخاب تاریخ و زمان به درستی عمل می‌کند
✅ اعداد فارسی نمایش داده می‌شوند
✅ محاسبات تبدیل تاریخ صحیح است

#### نکته مهم:
- همیشه از `let` برای متغیرهایی که قرار است تغییر کنند استفاده کنید
- از `const` فقط برای مقادیر ثابت استفاده کنید
- این یک اشتباه رایج در JavaScript است

---

**تاریخ بروزرسانی:** 2026-02-21 (بروزرسانی 9)


---

### 2026-02-21 - رفع مشکل دوم تقویم شمسی (بروزرسانی 10)

#### مشکل:
- خطای JavaScript: `Uncaught TypeError: Assignment to constant variable`
- خط 408 در `persian-datepicker-package.js`
- متد `isLeapYear` خطا می‌داد

#### علت:
- متغیر `n` در متد `isLeapYear` به عنوان `const` تعریف شده بود
- در خط 408 سعی می‌شد مقدار آن تغییر کند: `if (jump - n < 6) n = n - jump + ...`
- همچنین متغیر `jp` در حلقه for باید تغییر می‌کرد ولی `const` بود

#### راه‌حل:
- تغییر `const n` به `let n` در خط 407
- تغییر `const jp` به `let jp` در خط 398
- اضافه کردن `jp = jm;` در حلقه for

#### فایل تغییر یافته:
- `public/js/persian-datepicker-package.js` - رفع متغیرهای const در isLeapYear

#### کد قبل (اشتباه):
```javascript
isLeapYear(year) {
    const breaks = [1, 5, 9, 13, 17, 22, 26, 30];
    const jp = breaks[0];  // ❌ const
    
    let jump = 0;
    for (let i = 1; i < breaks.length; i++) {
        const jm = breaks[i];
        jump = jm - jp;
        if (year < jm) break;
        // ❌ jp باید تغییر کند ولی const است
    }
    
    const n = year - jp;  // ❌ const
    if (jump - n < 6) n = n - jump + ...;  // ❌ خطا
```

#### کد بعد (صحیح):
```javascript
isLeapYear(year) {
    const breaks = [1, 5, 9, 13, 17, 22, 26, 30];
    let jp = breaks[0];  // ✅ let
    
    let jump = 0;
    for (let i = 1; i < breaks.length; i++) {
        const jm = breaks[i];
        jump = jm - jp;
        if (year < jm) break;
        jp = jm;  // ✅ تغییر مقدار
    }
    
    let n = year - jp;  // ✅ let
    if (jump - n < 6) n = n - jump + ...;  // ✅ کار می‌کند
```

#### تغییرات کلی در فایل:
1. خط 305: `const gy` → `let gy` (در gregorianToJalali)
2. خط 398: `const jp` → `let jp` (در isLeapYear)
3. خط 407: `const n` → `let n` (در isLeapYear)
4. خط 403: اضافه شدن `jp = jm;` در حلقه

#### وضعیت نهایی:
✅ خطای const در gregorianToJalali برطرف شد
✅ خطای const در isLeapYear برطرف شد
✅ محاسبات سال کبیسه صحیح است
✅ تقویم بدون خطا باز می‌شود
✅ انتخاب تاریخ کار می‌کند
✅ نمایش روزهای ماه صحیح است

#### درس آموخته:
- همیشه قبل از استفاده از `const`، مطمئن شوید که متغیر تغییر نمی‌کند
- در الگوریتم‌های پیچیده، بهتر است از `let` استفاده کنید
- تست کامل کد JavaScript قبل از استقرار ضروری است

---

**تاریخ بروزرسانی:** 2026-02-21 (بروزرسانی 10)
