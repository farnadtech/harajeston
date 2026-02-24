# راهنمای کامل سیستم درگاه‌های پرداخت

## نصب و راه‌اندازی

### 1. نصب پکیج Larapay
پکیج Larapay با موفقیت نصب شده است و تمام درگاه‌های پرداخت ایرانی را پشتیبانی می‌کند.

```bash
composer require farayaz/larapay
```

### 2. Migration و Seeder
دو جدول جدید ایجاد شده:
- `payment_gateways`: ذخیره اطلاعات درگاه‌ها
- ستون‌های جدید در `wallet_transactions`: برای پیگیری تراکنش‌های پرداخت

```bash
php artisan migrate
php artisan db:seed --class=PaymentGatewaySeeder
```

## درگاه‌های پشتیبانی شده

### 1. زرین‌پال (Zarinpal)
- **نیازمندی‌ها:** Merchant ID
- **دریافت اطلاعات:** از پنل زرین‌پال > درگاه پرداخت
- **محبوب‌ترین درگاه** برای پرداخت‌های آنلاین

### 2. بانک ملت (Mellat)
- **نیازمندی‌ها:** Terminal ID, Username, Password
- **دریافت اطلاعات:** از بانک ملت

### 3. بانک سامان (Saman)
- **نیازمندی‌ها:** Merchant ID
- **دریافت اطلاعات:** از بانک سامان

### 4. بانک پارسیان (Parsian)
- **نیازمندی‌ها:** PIN
- **دریافت اطلاعات:** از بانک پارسیان

### 5. بانک پاسارگاد (Pasargad)
- **نیازمندی‌ها:** Merchant ID, Terminal ID, Certificate Path
- **دریافت اطلاعات:** از بانک پاسارگاد
- **نکته:** فایل گواهی را در `storage/certificates/` قرار دهید

### 6. بانک صادرات (Saderat)
- **نیازمندی‌ها:** Merchant ID, Terminal ID, Public Key
- **دریافت اطلاعات:** از بانک صادرات

### 7. بانک سپه (Sepehr)
- **نیازمندی‌ها:** Terminal ID
- **دریافت اطلاعات:** از بانک سپه

### 8. ایران کیش (Irankish)
- **نیازمندی‌ها:** Merchant ID, SHA1 Key
- **دریافت اطلاعات:** از ایران کیش

## نحوه استفاده برای ادمین

### 1. دسترسی به پنل مدیریت
- وارد پنل ادمین شوید
- از منوی سمت راست، گزینه "درگاه‌های پرداخت" را انتخاب کنید

### 2. تنظیم درگاه
1. روی دکمه "ویرایش" کلیک کنید
2. اطلاعات احراز هویت را وارد کنید
3. وضعیت را روی "فعال" قرار دهید
4. ترتیب نمایش را تنظیم کنید
5. ذخیره کنید

### 3. فعال/غیرفعال کردن
- با کلیک روی دکمه "فعال/غیرفعال" می‌توانید سریعاً وضعیت درگاه را تغییر دهید

## نحوه استفاده برای کاربران

### 1. شارژ کیف پول
1. وارد بخش "کیف پول" شوید
2. مبلغ مورد نظر را وارد کنید
3. یکی از درگاه‌های فعال را انتخاب کنید
4. روی "پرداخت و شارژ کیف پول" کلیک کنید
5. به درگاه پرداخت منتقل می‌شوید
6. پس از پرداخت موفق، به سایت برمی‌گردید و کیف پول شارژ می‌شود

### 2. محاسبه مالیات
- مالیات 9% به صورت خودکار محاسبه و به مبلغ اضافه می‌شود
- مبلغ نهایی قابل پرداخت نمایش داده می‌شود

## ساختار فایل‌ها

### Models
- `app/Models/PaymentGateway.php`: مدل درگاه‌های پرداخت
- `app/Models/WalletTransaction.php`: مدل تراکنش‌های کیف پول

### Services
- `app/Services/PaymentGatewayService.php`: سرویس مدیریت پرداخت‌ها
  - `initiateCharge()`: ایجاد پرداخت
  - `verifyPayment()`: تایید پرداخت
  - `configureGateway()`: تنظیم درگاه

### Controllers
- `app/Http/Controllers/Admin/PaymentGatewayController.php`: کنترلر پنل ادمین
- `app/Http/Controllers/WalletController.php`: کنترلر کیف پول (به‌روزرسانی شده)

### Views
- `resources/views/admin/payment-gateways/index.blade.php`: لیست درگاه‌ها
- `resources/views/admin/payment-gateways/edit.blade.php`: ویرایش درگاه
- `resources/views/wallet/show.blade.php`: نمایش کیف پول (به‌روزرسانی شده)

### Routes
```php
// Admin Routes
Route::get('/admin/payment-gateways', [PaymentGatewayController::class, 'index']);
Route::get('/admin/payment-gateways/{gateway}/edit', [PaymentGatewayController::class, 'edit']);
Route::put('/admin/payment-gateways/{gateway}', [PaymentGatewayController::class, 'update']);
Route::patch('/admin/payment-gateways/{gateway}/toggle', [PaymentGatewayController::class, 'toggle']);

// User Routes
Route::post('/wallet/add-funds', [WalletController::class, 'addFunds']);
Route::any('/wallet/payment/callback', [WalletController::class, 'paymentCallback']);
```

## فلوی پرداخت

### 1. ایجاد پرداخت
```
کاربر → انتخاب مبلغ و درگاه → PaymentGatewayService::initiateCharge()
→ ایجاد تراکنش در دیتابیس → Larapay::generate()
→ انتقال به درگاه پرداخت
```

### 2. بازگشت از درگاه
```
درگاه پرداخت → Callback URL → PaymentGatewayService::verifyPayment()
→ Larapay::verify() → به‌روزرسانی تراکنش
→ شارژ کیف پول → نمایش پیام موفقیت
```

## امنیت

### 1. ذخیره‌سازی اطلاعات
- اطلاعات احراز هویت در فیلد JSON ذخیره می‌شود
- توصیه می‌شود از encryption برای فیلد credentials استفاده کنید

### 2. Validation
- تمام ورودی‌ها اعتبارسنجی می‌شوند
- فقط درگاه‌های فعال قابل استفاده هستند
- مبالغ حداقل و حداکثر کنترل می‌شوند

### 3. Transaction Safety
- استفاده از Database Transaction برای عملیات مالی
- ثبت لاگ برای خطاها
- جلوگیری از تراکنش‌های تکراری

## تنظیمات پیشرفته

### 1. تغییر مالیات
مالیات در تنظیمات سایت قابل تغییر است:
```php
SiteSetting::set('wallet_charge_tax', 9); // 9%
```

### 2. محدودیت مبلغ
```php
SiteSetting::set('wallet_min_deposit', 10000);
SiteSetting::set('wallet_max_deposit', 100000000);
```

### 3. Callback URL
URL بازگشت از درگاه:
```
https://yourdomain.com/wallet/payment/callback
```

## عیب‌یابی

### مشکل: درگاه فعال نمی‌شود
- بررسی کنید اطلاعات احراز هویت صحیح وارد شده باشد
- لاگ‌های Laravel را بررسی کنید

### مشکل: پرداخت موفق نیست
- بررسی کنید Callback URL در تنظیمات درگاه صحیح باشد
- لاگ‌های `storage/logs/laravel.log` را بررسی کنید

### مشکل: کیف پول شارژ نمی‌شود
- بررسی کنید تراکنش در دیتابیس ثبت شده باشد
- وضعیت تراکنش را چک کنید (pending/completed/failed)

## تست

### تست محیط توسعه
برخی درگاه‌ها محیط sandbox دارند:
- زرین‌پال: استفاده از Merchant ID تستی
- سایر درگاه‌ها: مستندات هر درگاه را بررسی کنید

### تست دستی
1. یک درگاه را فعال کنید
2. مبلغ کمی (مثلاً 1000 تومان) شارژ کنید
3. پرداخت را کامل کنید
4. بررسی کنید کیف پول شارژ شده باشد

## پشتیبانی

برای مشکلات مربوط به:
- **پکیج Larapay:** https://github.com/farayaz/larapay
- **درگاه‌های پرداخت:** با پشتیبانی هر درگاه تماس بگیرید

## نکات مهم

1. ✅ همیشه در محیط تست آزمایش کنید
2. ✅ اطلاعات احراز هویت را امن نگه دارید
3. ✅ لاگ‌های پرداخت را بررسی کنید
4. ✅ Callback URL را در تنظیمات درگاه ثبت کنید
5. ✅ SSL Certificate برای سایت فعال باشد
6. ✅ مالیات را طبق قوانین کشور تنظیم کنید

## به‌روزرسانی‌های آینده

- [ ] افزودن گزارش تراکنش‌های پرداخت
- [ ] پشتیبانی از پرداخت اقساطی
- [ ] افزودن درگاه‌های بین‌المللی
- [ ] سیستم کش‌بک و تخفیف
