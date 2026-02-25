# راهنمای سیستم مدیریت عکس‌ها

## ساختار ذخیره‌سازی عکس‌ها

### مسیرها
```
storage/app/public/
├── listings/           # عکس‌های آگهی‌ها
│   ├── {listing_id}/   # هر آگهی یک پوشه جداگانه دارد
│   │   ├── image1.jpg
│   │   ├── image2.png
│   │   └── ...
├── stores/             # عکس‌های فروشگاه‌ها
│   ├── logos/          # لوگوی فروشگاه‌ها
│   └── banners/        # بنر فروشگاه‌ها
└── users/              # عکس‌های کاربران (در صورت نیاز)
```

### Symbolic Link
برای دسترسی عمومی به عکس‌ها، باید symbolic link ایجاد شود:

```bash
php artisan storage:link
```

این دستور یک لینک از `storage/app/public` به `public/storage` ایجاد می‌کند.

## استفاده در کد

### 1. مدل ListingImage

مدل `ListingImage` دارای accessor برای URL است:

```php
// app/Models/ListingImage.php

public function getUrlAttribute(): string
{
    // استفاده از url() helper برای تولید URL کامل با APP_URL
    return url('storage/' . $this->file_path);
}
```

### 2. استفاده در Blade Views

**روش صحیح (استفاده از accessor):**
```blade
@if($listing->images->count() > 0)
    <img src="{{ $listing->images->first()->url }}" alt="{{ $listing->title }}">
@endif
```

**روش جایگزین (استفاده مستقیم از url helper):**
```blade
@if($listing->images->count() > 0)
    <img src="{{ url('storage/' . $listing->images->first()->file_path) }}" alt="{{ $listing->title }}">
@endif
```

**❌ روش‌های اشتباه:**
```blade
<!-- اشتباه: asset() فقط مسیر نسبی برمی‌گرداند -->
<img src="{{ asset('storage/' . $image->file_path) }}">

<!-- اشتباه: Storage::url() فقط مسیر نسبی برمی‌گرداند -->
<img src="{{ Storage::url($image->file_path) }}">

<!-- اشتباه: فیلد image_path وجود ندارد، باید file_path باشد -->
<img src="{{ $image->image_path }}">
```

### 3. ذخیره عکس جدید

```php
// در ImageService یا Controller

use Illuminate\Support\Facades\Storage;

// ذخیره عکس
$path = $request->file('image')->store('listings/' . $listing->id, 'public');

// ذخیره در دیتابیس
ListingImage::create([
    'listing_id' => $listing->id,
    'file_path' => $path,  // مثال: listings/123/abc123.jpg
    'file_name' => $request->file('image')->getClientOriginalName(),
    'display_order' => 1,
]);
```

## تنظیمات مهم

### 1. فایل .env

```env
APP_URL=http://localhost/haraj/public

# یا برای production:
APP_URL=https://yourdomain.com
```

**نکته مهم:** `APP_URL` باید دقیقاً با آدرس سایت شما مطابقت داشته باشد.

### 2. فایل config/filesystems.php

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

## نصب و راه‌اندازی (برای خریداران)

### مرحله 1: Clone پروژه
```bash
git clone https://github.com/farnadtech/harajeston.git
cd harajeston
```

### مرحله 2: نصب Dependencies
```bash
composer install
npm install
```

### مرحله 3: تنظیمات Environment
```bash
cp .env.example .env
php artisan key:generate
```

**ویرایش فایل .env:**
```env
APP_NAME="سایت حراج من"
APP_URL=http://localhost/haraj/public  # یا آدرس دامنه شما

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### مرحله 4: ایجاد دیتابیس و Migration
```bash
php artisan migrate
php artisan db:seed
```

### مرحله 5: ایجاد Symbolic Link برای عکس‌ها
```bash
php artisan storage:link
```

**نکته:** اگر خطای "symlink already exists" دریافت کردید:
```bash
# حذف لینک قبلی
rm public/storage  # در لینوکس/مک
# یا
rmdir public\storage  # در ویندوز

# ایجاد لینک جدید
php artisan storage:link
```

### مرحله 6: تنظیمات Permissions (فقط لینوکس/مک)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### مرحله 7: کامپایل Assets
```bash
npm run build
```

## عیب‌یابی

### مشکل: عکس‌ها نمایش داده نمی‌شوند (404)

**راه‌حل 1: بررسی Symbolic Link**
```bash
# بررسی وجود لینک
ls -la public/storage  # لینوکس/مک
dir public\storage     # ویندوز

# اگر وجود نداشت:
php artisan storage:link
```

**راه‌حل 2: بررسی APP_URL**
```bash
# مطمئن شوید APP_URL در .env درست است
php artisan config:clear
php artisan config:cache
```

**راه‌حل 3: بررسی مسیر فایل‌ها**
```php
// اجرای این اسکریپت در public/test-images.php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$image = \App\Models\ListingImage::first();
if ($image) {
    echo "File Path: " . $image->file_path . "\n";
    echo "URL: " . $image->url . "\n";
    echo "Full Path: " . storage_path('app/public/' . $image->file_path) . "\n";
    echo "Exists: " . (file_exists(storage_path('app/public/' . $image->file_path)) ? 'YES' : 'NO') . "\n";
}
```

### مشکل: عکس‌ها در localhost کار می‌کنند ولی در production نه

**راه‌حل:**
1. مطمئن شوید `APP_URL` در production به درستی تنظیم شده
2. مطمئن شوید `storage:link` در production اجرا شده
3. بررسی کنید که پوشه `storage/app/public` دارای permissions مناسب است

```bash
# در سرور production
cd /path/to/your/project
php artisan storage:link
chmod -R 775 storage
chown -R www-data:www-data storage
```

## بهترین روش‌ها (Best Practices)

### 1. همیشه از accessor استفاده کنید
```blade
<!-- ✅ درست -->
<img src="{{ $listing->images->first()->url }}">

<!-- ❌ اشتباه -->
<img src="/storage/{{ $listing->images->first()->file_path }}">
```

### 2. همیشه چک کنید عکس وجود دارد
```blade
@if($listing->images->count() > 0)
    <img src="{{ $listing->images->first()->url }}">
@else
    <img src="{{ asset('images/no-image.png') }}">
@endif
```

### 3. برای بهینه‌سازی، از eager loading استفاده کنید
```php
// ✅ درست
$listings = Listing::with('images')->get();

// ❌ اشتباه (N+1 problem)
$listings = Listing::all();
foreach ($listings as $listing) {
    $image = $listing->images->first(); // Query جداگانه برای هر listing
}
```

### 4. برای عکس‌های بزرگ، thumbnail ایجاد کنید
```php
// در ImageService
use Intervention\Image\Facades\Image;

public function createThumbnail($imagePath)
{
    $image = Image::make(storage_path('app/public/' . $imagePath));
    $image->fit(300, 300);
    
    $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
    $image->save(storage_path('app/public/' . $thumbnailPath));
    
    return $thumbnailPath;
}
```

## پشتیبانی

اگر مشکلی در سیستم عکس‌ها دارید:

1. ابتدا `php artisan storage:link` را اجرا کنید
2. `APP_URL` در `.env` را بررسی کنید
3. Cache را پاک کنید: `php artisan config:clear && php artisan cache:clear`
4. Permissions پوشه `storage` را بررسی کنید

برای سوالات بیشتر، به مستندات Laravel مراجعه کنید:
https://laravel.com/docs/filesystem
