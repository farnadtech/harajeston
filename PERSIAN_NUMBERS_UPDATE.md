# به‌روزرسانی اعداد فارسی

## تغییرات انجام شده

### 1. تغییر Cast های Model
تمام فیلدهای قیمت در مدل‌ها از `decimal:2` به `integer` تغییر یافتند:
- `Listing`: starting_price, current_price, buy_now_price, required_deposit
- `Bid`: amount
- `Order`: subtotal, shipping_cost, total
- `OrderItem`: price_snapshot, subtotal
- `CartItem`: price_snapshot
- `Wallet`: balance, frozen
- `WalletTransaction`: amount, balance_before, balance_after, frozen_before, frozen_after
- `AuctionParticipation`: deposit_amount
- `ShippingMethod`: base_cost

### 2. Helper Functions جدید
فایل `app/helpers.php` ایجاد شد با توابع زیر:

- `fa_number($number)`: تبدیل اعداد به فارسی
- `fa_price($amount)`: فرمت قیمت با جداکننده هزارگان و اعداد فارسی
- `en_number($number)`: تبدیل اعداد فارسی به انگلیسی

### 3. به‌روزرسانی View ها
تمام `number_format()` در view های اصلی با `fa_price()` جایگزین شد:
- `resources/views/listings/show.blade.php`
- `resources/views/listings/index.blade.php`
- `resources/views/stores/show.blade.php`
- `resources/views/wallet/show.blade.php`
- `resources/views/livewire/wallet-balance.blade.php`
- `resources/views/livewire/auction-bidding.blade.php`

## دستورات لازم

برای اعمال تغییرات، دستورات زیر را اجرا کنید:

```bash
# 1. بارگذاری مجدد autoload
composer dump-autoload

# 2. پاک کردن cache ها
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 3. اجرای مجدد سرور (در صورت نیاز)
php artisan serve
```

## نتیجه

حالا تمام اعداد در سایت به صورت زیر نمایش داده می‌شوند:

**قبل:**
- `11200000.00` تومان
- `52,500,000` تومان

**بعد:**
- `۱۱,۲۰۰,۰۰۰` تومان
- `۵۲,۵۰۰,۰۰۰` تومان

## استفاده در View های جدید

برای استفاده در view های جدید:

```blade
{{-- نمایش قیمت با فرمت فارسی --}}
{{ fa_price($listing->price) }}

{{-- نمایش عدد ساده با فرمت فارسی --}}
{{ fa_number($count) }}

{{-- تبدیل عدد فارسی به انگلیسی (برای ورودی فرم) --}}
{{ en_number($persianNumber) }}
```

## نکات مهم

1. اعداد در دیتابیس به صورت integer ذخیره می‌شوند (بدون اعشار)
2. تمام قیمت‌ها به تومان هستند
3. در فرم‌های ورودی، اعداد فارسی به انگلیسی تبدیل می‌شوند
4. جداکننده هزارگان به صورت خودکار اضافه می‌شود
