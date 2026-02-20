# پلتفرم مزایده و فروش آنلاین ایرانی

یک پلتفرم جامع چندفروشنده‌ای با قابلیت مزایده، فروش مستقیم، و مدیریت فروشگاه‌های آنلاین.

## ویژگی‌های اصلی

### 🎯 سیستم مزایده
- مزایده‌های زمان‌دار با شمارش معکوس
- سیستم سپرده (10% قیمت پایه)
- رتبه‌بندی پیشنهاددهندگان
- منطق آبشاری (Cascade Logic) برای انتخاب برنده
- مهلت 48 ساعته برای پرداخت نهایی

### 🛒 فروش مستقیم
- فروش محصولات با قیمت ثابت
- سبد خرید و سیستم سفارش
- مدیریت موجودی و هشدار موجودی کم
- روش‌های مختلف ارسال

### 🏪 ویترین فروشگاه (Storefront)
- صفحه اختصاصی برای هر فروشنده
- سفارشی‌سازی بنر و لوگو
- نمایش محصولات فروشنده
- آمار و گزارش فروش

### 💰 سیستم کیف پول
- کیف پول داخلی برای هر کاربر
- مدیریت موجودی و موجودی مسدود شده
- تاریخچه تراکنش‌ها با فیلتر
- خروجی CSV

### 🔔 سیستم اعلان‌ها
- اعلان شروع و پایان مزایده
- اعلان پیشنهاد بالاتر
- اعلان وضعیت سفارش
- اعلان موجودی کم

### 🎨 رابط کاربری
- طراحی RTL با Tailwind CSS
- فونت Vazirmatn
- تقویم جلالی
- اعداد فارسی
- کامپوننت‌های Livewire برای به‌روزرسانی لحظه‌ای

## نیازمندی‌های سیستم

- PHP 8.1 یا بالاتر
- MySQL 8.0 یا بالاتر
- Composer
- Node.js و NPM
- Redis (اختیاری، برای کش)

## نصب و راه‌اندازی

### 1. کلون کردن پروژه

```bash
git clone <repository-url>
cd haraj
```

### 2. نصب وابستگی‌ها

```bash
# نصب وابستگی‌های PHP
composer install

# نصب وابستگی‌های JavaScript
npm install
```

### 3. تنظیمات محیطی

```bash
# کپی فایل .env
cp .env.example .env

# تولید کلید برنامه
php artisan key:generate
```

### 4. تنظیمات دیتابیس

فایل `.env` را ویرایش کنید:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=haraj
DB_USERNAME=root
DB_PASSWORD=
```

### 5. اجرای Migration و Seeder

```bash
# ایجاد جداول دیتابیس
php artisan migrate

# ایجاد داده‌های نمایشی (اختیاری)
php artisan db:seed --class=DemoDataSeeder
```

### 6. ساخت فایل‌های Frontend

```bash
# برای محیط توسعه
npm run dev

# برای محیط تولید
npm run build
```

### 7. ایجاد لینک Storage

```bash
php artisan storage:link
```

### 8. راه‌اندازی سرور

```bash
php artisan serve
```

برنامه در آدرس `http://localhost:8000` در دسترس خواهد بود.

## کاربران نمایشی

پس از اجرای seeder، می‌توانید با این کاربران وارد شوید:

| نقش | ایمیل | رمز عبور |
|-----|-------|----------|
| مدیر | admin@haraj.test | password |
| فروشنده 1 | seller1@haraj.test | password |
| فروشنده 2 | seller2@haraj.test | password |
| خریدار 1 | buyer1@haraj.test | password |
| خریدار 2 | buyer2@haraj.test | password |

## تنظیمات Cron Job

برای اجرای خودکار وظایف زمان‌بندی شده (شروع/پایان مزایده‌ها)، این خط را به crontab اضافه کنید:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

یا در محیط توسعه، این دستور را اجرا کنید:

```bash
php artisan schedule:work
```

## اجرای تست‌ها

```bash
# اجرای همه تست‌ها
php artisan test

# اجرای تست‌های خاص
php artisan test --filter=WalletServiceTest

# اجرای تست‌ها با coverage
php artisan test --coverage
```

## ساختار پروژه

```
app/
├── Console/          # دستورات Artisan و زمان‌بندی
├── Exceptions/       # کلاس‌های Exception سفارشی
├── Http/
│   ├── Controllers/  # کنترلرها
│   ├── Middleware/   # Middleware ها
│   └── Requests/     # Form Request Validation
├── Jobs/             # Job های صف
├── Livewire/         # کامپوننت‌های Livewire
├── Models/           # مدل‌های Eloquent
├── Notifications/    # کلاس‌های اعلان
├── Policies/         # Policy های مجوزدهی
└── Services/         # لایه سرویس (Business Logic)

database/
├── factories/        # Factory ها برای تست
├── migrations/       # Migration های دیتابیس
└── seeders/          # Seeder ها

resources/
├── views/            # فایل‌های Blade
│   ├── layouts/      # قالب‌های اصلی
│   ├── livewire/     # ویوهای Livewire
│   ├── listings/     # صفحات آگهی
│   ├── dashboard/    # داشبوردها
│   └── admin/        # پنل مدیریت
└── js/               # فایل‌های JavaScript

tests/
├── Feature/          # تست‌های Feature
└── Unit/             # تست‌های Unit
```

## معماری و الگوهای طراحی

### Service Layer Pattern
منطق کسب‌وکار در کلاس‌های Service جدا شده است:
- `WalletService`: مدیریت کیف پول و تراکنش‌ها
- `AuctionService`: منطق مزایده
- `BidService`: مدیریت پیشنهادها
- `OrderService`: مدیریت سفارش‌ها
- `CartService`: مدیریت سبد خرید

### Repository Pattern
مدل‌های Eloquent به عنوان Repository عمل می‌کنند.

### Transaction Safety
همه عملیات مالی با Database Transaction و Row Locking محافظت شده‌اند.

### Property-Based Testing
تست‌های مبتنی بر ویژگی برای اطمینان از صحت منطق کسب‌وکار.

## امنیت

- ✅ Hash کردن رمز عبور با bcrypt
- ✅ محافظت CSRF
- ✅ Sanitization ورودی‌ها
- ✅ محافظت SQL Injection (Eloquent ORM)
- ✅ Rate Limiting
- ✅ احراز هویت با Laravel Sanctum (API)
- ✅ مجوزدهی با Policy ها
- ✅ HTTPS (توصیه شده در production)

## بهینه‌سازی عملکرد

- ✅ Index های دیتابیس برای کوئری‌های پرتکرار
- ✅ Eager Loading برای جلوگیری از N+1 Query
- ✅ کش کردن داده‌های استاتیک
- ✅ بهینه‌سازی Livewire Polling
- ✅ فشرده‌سازی تصاویر

## API Documentation

API با Laravel Sanctum محافظت شده است. برای دسترسی:

1. ثبت‌نام یا ورود از طریق `/api/register` یا `/api/login`
2. دریافت توکن
3. ارسال توکن در هدر: `Authorization: Bearer {token}`

### Endpoints اصلی

```
POST   /api/register          # ثبت‌نام
POST   /api/login             # ورود
POST   /api/logout            # خروج

GET    /api/listings          # لیست آگهی‌ها
GET    /api/listings/{id}     # جزئیات آگهی
POST   /api/listings          # ایجاد آگهی
POST   /api/listings/{id}/participate  # شرکت در مزایده

POST   /api/bids              # ثبت پیشنهاد

GET    /api/wallet            # اطلاعات کیف پول
GET    /api/wallet/transactions  # تراکنش‌ها

POST   /api/cart              # افزودن به سبد
GET    /api/cart              # مشاهده سبد
DELETE /api/cart/{id}         # حذف از سبد

POST   /api/checkout          # تکمیل خرید
```

## مشارکت در پروژه

1. Fork کنید
2. برنچ feature ایجاد کنید (`git checkout -b feature/AmazingFeature`)
3. تغییرات را commit کنید (`git commit -m 'Add some AmazingFeature'`)
4. به برنچ Push کنید (`git push origin feature/AmazingFeature`)
5. Pull Request باز کنید

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.

## پشتیبانی

برای گزارش باگ یا درخواست ویژگی جدید، لطفاً یک Issue ایجاد کنید.

---

**ساخته شده با ❤️ با Laravel 10 و Livewire 3**
