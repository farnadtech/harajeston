# اصلاح لینک‌های نوتیفیکیشن سفارش

## مشکل

وقتی سفارش جدیدی ثبت می‌شد، نوتیفیکیشن برای فروشنده با لینک ادمین ارسال می‌شد:
```
http://localhost/haraj/public/admin/orders/15
```

این باعث می‌شد فروشنده‌های عادی نتوانند به صفحه سفارش دسترسی داشته باشند (403 Forbidden).

## راه‌حل

### 1. اصلاح NotificationService.php

#### notifyNewOrder()
**قبل:**
```php
// Notify seller
'link' => route('admin.orders.show', $order->id),
```

**بعد:**
```php
// Notify seller - use regular orders.show route
'link' => route('orders.show', $order->id),
```

#### notifyOrderStatusUpdated()
**قبل:**
```php
// Notify seller
'link' => route('admin.orders.show', $order->id),
```

**بعد:**
```php
// Notify seller - use regular orders.show route
'link' => route('orders.show', $order->id),
```

### 2. اصلاح نوتیفیکیشن‌های موجود

اسکریپت `fix-notification-links.php` برای اصلاح نوتیفیکیشن‌های قدیمی:
- 6 نوتیفیکیشن اصلاح شد
- همه لینک‌های `/admin/orders/` به `/orders/` تبدیل شدند

## نتیجه

✅ فروشنده‌ها حالا می‌توانند از طریق نوتیفیکیشن به صفحه سفارش دسترسی داشته باشند
✅ خریدارها همچنان به صفحه عادی سفارش هدایت می‌شوند
✅ ادمین‌ها به صفحه ادمین سفارش هدایت می‌شوند

## Routes

- **Seller/Buyer**: `orders.show` → `/orders/{order}`
- **Admin**: `admin.orders.show` → `/admin/orders/{order}`

## تست

برای تست:
1. یک سفارش جدید ایجاد کنید
2. نوتیفیکیشن فروشنده را چک کنید
3. روی لینک کلیک کنید
4. باید به `/orders/{id}` هدایت شوید (نه `/admin/orders/{id}`)
