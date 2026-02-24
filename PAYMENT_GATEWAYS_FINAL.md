# سیستم درگاه‌های پرداخت - نسخه نهایی

## ✅ درگاه‌های فعال

سیستم از 4 درگاه پرداخت معتبر ایرانی پشتیبانی می‌کند:

### 1. زرین‌پال (ZarinPal)
- **رنگ:** زرد/نارنجی
- **نیازمندی:** Merchant ID
- **وب‌سایت:** https://www.zarinpal.com
- **محبوبیت:** ⭐⭐⭐⭐⭐ (محبوب‌ترین)

### 2. زیبال (Zibal)
- **رنگ:** سبز آبی
- **نیازمندی:** Merchant ID
- **وب‌سایت:** https://zibal.ir
- **محبوبیت:** ⭐⭐⭐⭐

### 3. وندار (Vandar)
- **رنگ:** بنفش/آبی
- **نیازمندی:** API Key
- **وب‌سایت:** https://vandar.io
- **محبوبیت:** ⭐⭐⭐⭐

### 4. پی‌پینگ (PayPing)
- **رنگ:** قرمز/صورتی
- **نیازمندی:** API Key
- **وب‌سایت:** https://payping.ir
- **محبوبیت:** ⭐⭐⭐⭐

## 🎨 طراحی صفحه مدیریت

### ویژگی‌های UI:
- ✅ طراحی مدرن با Gradient و Shadow
- ✅ کارت‌های جداگانه برای هر درگاه
- ✅ آیکون‌های اختصاصی برای هر درگاه
- ✅ نمایش وضعیت فعال/غیرفعال با رنگ‌بندی
- ✅ نمایش وضعیت تنظیمات (تنظیم شده/نیاز به تنظیم)
- ✅ آمار کلی در بالای صفحه
- ✅ راهنمای کامل در پایین صفحه
- ✅ انیمیشن‌های نرم و حرفه‌ای

### صفحه Index:
```
URL: /admin/payment-gateways
```
- نمایش 4 کارت درگاه در Grid 2 ستونی
- آمار کلی: کل درگاه‌ها، فعال، نیاز به تنظیم
- دکمه‌های ویرایش و فعال/غیرفعال
- راهنمای کامل استفاده

### صفحه Edit:
```
URL: /admin/payment-gateways/{gateway}/edit
```
- فرم ویرایش با طراحی مدرن
- Toggle برای فعال/غیرفعال
- فیلد ترتیب نمایش
- فیلدهای اختصاصی هر درگاه با رنگ‌بندی
- Sidebar با اطلاعات و راهنما
- لینک مستقیم به پنل هر درگاه

## 📊 آمار و اطلاعات

### تعداد فایل‌های ایجاد شده: 15+
### تعداد فایل‌های به‌روزرسانی شده: 10+
### خطوط کد نوشته شده: 2000+

## 🚀 دستورات مهم

### نصب و راه‌اندازی:
```bash
# نصب پکیج
composer require farayaz/larapay

# اجرای Migration
php artisan migrate

# اجرای Seeder
php artisan db:seed --class=PaymentGatewaySeeder

# پاک‌سازی درگاه‌های قدیمی
php artisan tinker
DB::table('payment_gateways')->whereNotIn('name', ['zarinpal', 'zibal', 'vandar', 'payping'])->delete();
```

### تست:
```bash
# بررسی Routes
php artisan route:list --name=payment-gateways

# بررسی تعداد درگاه‌ها
php artisan tinker
App\Models\PaymentGateway::count()
```

## 📁 ساختار فایل‌ها

```
app/
├── Models/
│   ├── PaymentGateway.php (✅ فقط 4 درگاه)
│   └── WalletTransaction.php
├── Services/
│   └── PaymentGatewayService.php (✅ به‌روزرسانی شده)
└── Http/Controllers/Admin/
    └── PaymentGatewayController.php

resources/views/admin/payment-gateways/
├── index.blade.php (✅ طراحی جدید کامل)
└── edit.blade.php (✅ طراحی جدید کامل)

database/
├── migrations/
│   ├── 2026_02_24_000000_create_payment_gateways_table.php
│   └── 2026_02_24_000001_add_payment_fields_to_wallet_transactions.php
└── seeders/
    └── PaymentGatewaySeeder.php (✅ فقط 4 درگاه)

config/
└── larapay.php (✅ فقط 4 درگاه)
```

## 🎯 نحوه استفاده

### برای ادمین:

1. **ورود به پنل:**
   ```
   http://localhost/haraj/public/admin/payment-gateways
   ```

2. **فعال‌سازی درگاه:**
   - روی "ویرایش تنظیمات" کلیک کنید
   - اطلاعات احراز هویت را وارد کنید
   - Toggle "فعال‌سازی درگاه" را روشن کنید
   - ذخیره کنید

3. **تنظیم ترتیب:**
   - عدد کوچکتر = اولویت بالاتر
   - مثال: 1, 2, 3, 4

### برای کاربران:

1. وارد کیف پول شوید
2. مبلغ را وارد کنید
3. یکی از درگاه‌های فعال را انتخاب کنید
4. پرداخت را انجام دهید

## 🔐 امنیت

- ✅ اطلاعات در JSON ذخیره می‌شود
- ✅ Validation کامل
- ✅ CSRF Protection
- ✅ Database Transaction
- ✅ Error Logging

## 📝 تنظیمات .env

```env
# درگاه پیش‌فرض
LARAPAY_DEFAULT_GATEWAY=zarinpal

# زرین‌پال
ZARINPAL_MERCHANT_ID=
ZARINPAL_SANDBOX=false

# زیبال
ZIBAL_MERCHANT_ID=

# وندار
VANDAR_API_KEY=

# پی‌پینگ
PAYPING_API_KEY=
```

## 🎨 رنگ‌بندی درگاه‌ها

| درگاه | رنگ اصلی | رنگ Gradient |
|-------|---------|--------------|
| زرین‌پال | زرد (#FDB913) | from-yellow-500 to-orange-600 |
| زیبال | سبز آبی (#00A693) | from-teal-500 to-cyan-600 |
| وندار | بنفش (#6366F1) | from-indigo-500 to-purple-600 |
| پی‌پینگ | قرمز (#FF6B6B) | from-red-500 to-pink-600 |

## ✨ ویژگی‌های خاص

### صفحه Index:
- Grid 2 ستونی responsive
- کارت‌های با Shadow و Hover Effect
- آمار زنده در بالای صفحه
- Badge های رنگی برای وضعیت
- راهنمای جامع در پایین

### صفحه Edit:
- Layout 3 ستونی (2 ستون فرم + 1 ستون Sidebar)
- Toggle زیبا برای فعال/غیرفعال
- فیلدهای رنگی برای هر درگاه
- Sidebar با اطلاعات و راهنما
- لینک‌های مستقیم به پنل درگاه‌ها

## 🧪 تست

### تست صفحه:
```
http://localhost/haraj/public/admin/payment-gateways
```

### چک‌لیست تست:
- ✅ صفحه Index به درستی نمایش داده می‌شود
- ✅ 4 کارت درگاه نمایش داده می‌شود
- ✅ آمار صحیح است
- ✅ دکمه ویرایش کار می‌کند
- ✅ دکمه فعال/غیرفعال کار می‌کند
- ✅ صفحه Edit به درستی نمایش داده می‌شود
- ✅ فرم ذخیره می‌شود
- ✅ Toggle کار می‌کند

## 📞 پشتیبانی

### مستندات درگاه‌ها:
- زرین‌پال: https://docs.zarinpal.com
- زیبال: https://docs.zibal.ir
- وندار: https://docs.vandar.io
- پی‌پینگ: https://docs.payping.ir

### مستندات Larapay:
- GitHub: https://github.com/farayaz/larapay

## 🎉 وضعیت نهایی

- ✅ پکیج نصب شد
- ✅ Migration اجرا شد
- ✅ Seeder اجرا شد
- ✅ درگاه‌های قدیمی حذف شدند
- ✅ 4 درگاه جدید اضافه شدند
- ✅ صفحه Index طراحی شد
- ✅ صفحه Edit طراحی شد
- ✅ Service به‌روزرسانی شد
- ✅ Config به‌روزرسانی شد
- ✅ Routes اضافه شدند
- ✅ منوی ادمین به‌روزرسانی شد

**همه چیز آماده است! 🚀**

---

**تاریخ:** 24 فوریه 2026  
**نسخه:** 2.0.0  
**وضعیت:** ✅ کامل و آماده استفاده
