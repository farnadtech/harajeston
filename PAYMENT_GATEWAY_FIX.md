# رفع خطای Larapay - سیستم درگاه پرداخت

## مشکل
خطای `Call to undefined method amount()` هنگام شارژ کیف پول

## علت
API پکیج Larapay به صورت متفاوتی کار می‌کند و از متدهای `amount()`, `description()`, `callback()` و `generate()` پشتیبانی نمی‌کند.

## راه‌حل اعمال شده

### 1. تصحیح API در PaymentGatewayService

**قبل:**
```php
$payment = app('larapay')
    ->amount($finalAmount)
    ->description("شارژ کیف پول")
    ->callback(route('wallet.payment.callback'))
    ->gateway($gatewayName)
    ->generate();
```

**بعد:**
```php
$larapay = app('larapay')->gateway($gatewayName, $gatewayConfig);

$result = $larapay->request(
    $transaction->id,        // شناسه تراکنش
    $amountToSend,          // مبلغ (تومان یا ریال)
    '',                     // کد ملی (اختیاری)
    $user->phone ?? '',     // شماره موبایل
    route('wallet.payment.callback'), // آدرس بازگشت
    []                      // کارت‌های مجاز (اختیاری)
);
```

### 2. تنظیمات صحیح درگاه‌ها

هر درگاه نیاز به تنظیمات خاص خود دارد:

- **ZarinPal**: `merchant_id`
- **Zibal**: `merchant` (نه `merchant_id`)
- **Vandar**: `api_key`
- **PayPing**: `token` (نه `api_key`)

### 3. تبدیل مبلغ

- همه درگاه‌ها به **تومان** کار می‌کنند (مبلغ ÷ 10)
- **استثنا**: PayPing خودش تقسیم بر 10 می‌کند، پس به ریال ارسال می‌شود

### 4. مدیریت Callback

توکن‌های بازگشتی از درگاه‌های مختلف:
- **ZarinPal**: `Authority`
- **Zibal**: `trackId`
- **Vandar**: `token`
- **PayPing**: `refid`

### 5. ذخیره درگاه در Session

برای شناسایی درگاه در callback، نام درگاه در session ذخیره می‌شود:
```php
session(['payment_gateway' => $gatewayName]);
```

## فایل‌های تغییر یافته

1. `app/Services/PaymentGatewayService.php` - اصلاح کامل API
2. `app/Http/Controllers/WalletController.php` - به‌روزرسانی callback
3. `public/test-payment-flow.php` - اسکریپت تست (جدید)

## نحوه تست

### 1. تست تنظیمات
```
http://localhost/haraj/public/test-payment-flow.php
```

### 2. تست واقعی پرداخت

1. وارد پنل ادمین شوید
2. به `admin/payment-gateways` بروید
3. یک درگاه را فعال کنید و اطلاعات آن را وارد کنید
4. به صفحه کیف پول بروید (`/wallet`)
5. مبلغی برای شارژ وارد کنید
6. درگاه را انتخاب کنید و روی "افزایش موجودی" کلیک کنید
7. به درگاه پرداخت منتقل می‌شوید
8. پس از پرداخت، به سایت برمی‌گردید و موجودی شارژ می‌شود

## نکات مهم

### برای محیط تست (Sandbox)

**ZarinPal Sandbox:**
- Merchant ID: `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`
- در تنظیمات درگاه، گزینه "حالت تست" را فعال کنید

**Zibal Test:**
- Merchant: `zibal`
- این merchant برای تست رایگان است

### مبالغ تست

- حداقل: 10,000 تومان
- حداکثر: 100,000,000 تومان
- مالیات: 9% (اضافه می‌شود)

### لاگ‌ها

خطاها در `storage/logs/laravel.log` ثبت می‌شوند:
```bash
tail -f storage/logs/laravel.log
```

## خطاهای رایج و راه‌حل

### 1. "merchant یافت نشد"
- اطلاعات درگاه را در پنل ادمین وارد کنید

### 2. "توکن پرداخت یافت نشد"
- مطمئن شوید callback URL صحیح است
- بررسی کنید که درگاه به callback برمی‌گردد

### 3. "مبلغ نامعتبر"
- مبلغ باید بین حداقل و حداکثر باشد
- مبلغ باید عدد صحیح باشد

## وضعیت فعلی

✅ API Larapay تصحیح شد
✅ تنظیمات 4 درگاه (ZarinPal, Zibal, Vandar, PayPing) اضافه شد
✅ مدیریت callback برای همه درگاه‌ها
✅ محاسبه مالیات 9%
✅ ذخیره تراکنش‌ها در دیتابیس
✅ به‌روزرسانی موجودی کیف پول

## مرحله بعدی

برای تست کامل، یکی از درگاه‌ها را با اطلاعات واقعی (یا sandbox) تنظیم کنید و فرآیند پرداخت را امتحان کنید.
