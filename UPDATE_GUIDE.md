# راهنمای انتشار آپدیت

## خلاصه سریع (هر بار)

```
تغییر بده → commit → build-update.ps1 → آپلود 2 فایل → git tag → push
```

---

## گام به گام کامل

### گام ۱ — تغییرات رو commit کن
```bash
git add .
git commit -m "feat: توضیح تغییرات"
```

### گام ۲ — پکیج بساز
```powershell
.\build-update.ps1 -Version "1.3.0" -FromTag "v1.2.0"
```
دو فایل در `dist/` ساخته میشه:
- `update-v1.3.0.zip` — فایل آپدیت
- `version.json` — اطلاعات نسخه

### گام ۳ — آپلود روی سرور
هر دو فایل رو روی `https://iranbooklet.ir/harajino/` آپلود کن.

### گام ۴ — تگ بزن
```bash
git tag v1.3.0
git push origin main --tags
```

---

## وقتی دیتابیس تغییر می‌کنه

### مفهوم Migration چیست؟

Laravel یه سیستم داره به اسم **Migration** که مثل تاریخچه تغییرات دیتابیسه.
هر بار که ساختار دیتابیس عوض میشه (مثلاً ستون جدید اضافه میشه)، یه فایل migration جدید ساخته میشه.

وقتی کاربر آپدیت می‌کنه، سیستم این فایل‌های جدید رو اجرا می‌کنه و دیتابیس کاربر رو آپدیت می‌کنه — **بدون اینکه داده‌های قبلی پاک بشن**.

---

### قانون طلایی

**هیچ‌وقت فایل migration قدیمی رو ویرایش نکن.**

اگه بخوای چیزی تغییر بدی، همیشه یه فایل migration جدید می‌سازی.

---

### من (Kiro) این کار رو برات انجام میدم

وقتی بخوای دیتابیس تغییر کنه، فقط بگو چی می‌خوای. مثلاً:

> "می‌خوام به جدول کاربران یه ستون شماره تلفن اضافه کنم"

من فایل migration رو می‌سازم، تو فقط commit می‌کنی و آپدیت می‌دی.

---

### انواع تغییر دیتابیس

#### ۱. اضافه کردن ستون جدید به جدول موجود

مثال: اضافه کردن `phone` به جدول `users`

فایل migration که من می‌سازم:
```php
// database/migrations/2026_05_01_add_phone_to_users_table.php
public function up(): void
{
    if (!Schema::hasColumn('users', 'phone')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
        });
    }
}
```

چرا `hasColumn` چک می‌کنیم؟ چون ممکنه کاربری قبلاً این ستون رو داشته باشه و بدون این چک خطا بده.

---

#### ۲. ساختن جدول کاملاً جدید

مثال: جدول `reviews` برای نظرات

```php
public function up(): void
{
    if (!Schema::hasTable('reviews')) {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });
    }
}
```

---

#### ۳. تغییر نام ستون (خطرناک‌ترین حالت)

این حالت باید با دقت انجام بشه تا داده‌ها از دست نرن:

```php
public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        // اول ستون جدید اضافه کن
        $table->string('customer_name')->nullable();
    });

    // داده‌های قدیمی رو کپی کن
    DB::table('orders')->update([
        'customer_name' => DB::raw('buyer_name')
    ]);

    Schema::table('orders', function (Blueprint $table) {
        // بعد ستون قدیمی رو حذف کن
        $table->dropColumn('buyer_name');
    });
}
```

---

#### ۴. حذف ستون

```php
public function up(): void
{
    if (Schema::hasColumn('users', 'old_field')) {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('old_field');
        });
    }
}
```

---

### چه اتفاقی موقع آپدیت کاربر می‌افته؟

```
کاربر کلیک "نصب آپدیت"
        ↓
سیستم فایل‌های جدید رو extract می‌کنه
        ↓
php artisan migrate اجرا میشه
        ↓
فقط migration هایی که قبلاً اجرا نشدن، اجرا میشن
        ↓
داده‌های قدیمی سالم، ساختار جدید اعمال شده ✅
```

Laravel یه جدول به اسم `migrations` داره که ثبت می‌کنه کدوم migration‌ها قبلاً اجرا شدن. پس هیچ migration‌ای دوبار اجرا نمیشه.

---

### نکته مهم برای migration های آپدیت

وقتی migration جدید می‌سازی، فایلش باید توی پکیج آپدیت باشه.
`build-update.ps1` به صورت خودکار فایل‌های `database/migrations/` رو که تغییر کردن داخل zip می‌ریزه.

---

## جدول نسخه‌بندی

| نوع تغییر | مثال | نسخه |
|-----------|------|-------|
| باگ‌فیکس کوچک | رفع خطای UI | 1.2.1 |
| ویژگی جدید | افزودن صفحه جدید | 1.3.0 |
| تغییر دیتابیس | اضافه کردن ستون | 1.3.0 |
| تغییر بزرگ | تغییر ساختار اصلی | 2.0.0 |

---

## آدرس‌های مهم

- سرور آپدیت: `https://iranbooklet.ir/harajino/`
- نسخه فعلی: `version.json` در root پروژه
- پکیج‌های آپدیت: پوشه `dist/`
- بکاپ‌های خودکار: `storage/backups/`
