# 📋 مستندات کامل سامانه حراجی ایرانی

> این مستند شامل تمام ویژگی‌ها، امکانات، تنظیمات، و جزئیات فنی سامانه است که می‌تواند برای بازسازی کامل سیستم استفاده شود.

## 📑 فهرست مطالب

1. [معماری کلی سیستم](#معماری-کلی-سیستم)
2. [تکنولوژی‌های استفاده شده](#تکنولوژیهای-استفاده-شده)
3. [ساختار دیتابیس](#ساختار-دیتابیس)
4. [نقش‌های کاربری](#نقشهای-کاربری)
5. [ویژگی‌های اصلی](#ویژگیهای-اصلی)
6. [پنل ادمین](#پنل-ادمین)
7. [سیستم مالی](#سیستم-مالی)
8. [API و Routing](#api-و-routing)
9. [تنظیمات و پیکربندی](#تنظیمات-و-پیکربندی)
10. [راهنمای نصب و راه‌اندازی](#راهنمای-نصب-و-راهاندازی)

---

## 🏗️ معماری کلی سیستم

### نوع سیستم
**پلتفرم حراجی آنلاین ایرانی** با قابلیت:
- حراجی زنده (Live Auction)
- خرید فوری (Buy Now)
- سیستم پیشنهاد قیمت (Bidding)
- مدیریت فروشگاه‌ها
- کیف پول داخلی

### معماری
- **Pattern**: MVC (Model-View-Controller)
- **Architecture**: Monolithic با Service Layer
- **Frontend**: Server-Side Rendering (Blade) + Alpine.js + Livewire
- **Backend**: Laravel 10.x
- **Database**: MySQL/MariaDB
- **Cache**: File-based (قابل تغییر به Redis)
- **Queue**: Database (قابل تغییر به Redis/RabbitMQ)

---


## 🛠️ تکنولوژی‌های استفاده شده

### Backend Stack
```
- PHP 8.1+
- Laravel 10.x
- Laravel Livewire 3.x
- Laravel Sanctum (API Authentication)
```

### Frontend Stack
```
- Blade Templates
- Alpine.js 3.x
- Tailwind CSS 3.x
- Tailwind Forms Plugin
- Material Symbols Icons
```

### JavaScript Libraries
```
- Persian Date Picker (Custom)
- Chart.js (برای گزارشات)
```

### Database & Storage
```
- MySQL 8.0+ / MariaDB 10.6+
- File Storage (Local/S3 Compatible)
```

### Development Tools
```
- Composer
- NPM/Yarn
- Laravel Mix/Vite
```

---

