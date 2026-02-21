# 📅 راهنمای تقویم شمسی پیشرفته

## نصب و راه‌اندازی

تقویم شمسی جدید با استفاده از یک پکیج کامل و حرفه‌ای پیاده‌سازی شده است.

### فایل‌های مورد نیاز

1. **JavaScript**: `public/js/persian-datepicker-package.js`
2. **CSS**: `public/css/persian-datepicker-package.css`
3. **تست**: `public/test-datepicker.html`

## نحوه استفاده

### 1. اضافه کردن فایل‌ها به صفحه

```blade
@push('styles')
<link rel="stylesheet" href="{{ url('css/persian-datepicker-package.css') }}">
@endpush

@push('scripts')
<script src="{{ url('js/persian-datepicker-package.js') }}"></script>
@endpush
```

### 2. ایجاد فیلد تاریخ

```html
<input type="text" 
       name="starts_at" 
       id="starts_at" 
       class="persian-datepicker-input"
       placeholder="انتخاب تاریخ و زمان"
       autocomplete="off">
```

### 3. فعال‌سازی خودکار

تقویم به صورت خودکار برای تمام المان‌هایی که کلاس `persian-datepicker-input` دارند فعال می‌شود.

### 4. فعال‌سازی دستی (اختیاری)

```javascript
const datepicker = new PersianDatePicker('#myInput', {
    format: 'YYYY/MM/DD HH:mm',
    timePicker: true,
    autoClose: false
});
```

## ویژگی‌ها

### ✨ قابلیت‌های اصلی

- ✅ تقویم کامل شمسی با ماه‌ها و روزهای فارسی
- ✅ انتخاب ساعت و دقیقه
- ✅ نمایش روز امروز با رنگ متمایز
- ✅ محاسبات دقیق سال کبیسه
- ✅ تبدیل خودکار اعداد به فارسی
- ✅ طراحی مدرن و زیبا
- ✅ انیمیشن‌های روان
- ✅ سازگار با موبایل
- ✅ دکمه امروز برای انتخاب سریع
- ✅ Modal تمام صفحه با overlay

### 🎨 طراحی

- رنگ‌بندی مدرن با گرادیانت بنفش-آبی
- انیمیشن fade-in هنگام باز شدن
- Hover effects روی روزها
- نمایش روز انتخاب شده با رنگ متمایز
- نمایش روز امروز با پس‌زمینه زرد

### ⚙️ تنظیمات

```javascript
{
    format: 'YYYY/MM/DD HH:mm',  // فرمت خروجی
    timePicker: true,             // نمایش انتخابگر زمان
    initialValue: true,           // مقدار اولیه از input
    autoClose: false              // بستن خودکار پس از انتخاب
}
```

## مثال‌های کاربردی

### مثال 1: تاریخ ساده بدون زمان

```javascript
new PersianDatePicker('#date', {
    timePicker: false,
    autoClose: true
});
```

### مثال 2: تاریخ و زمان با فرمت سفارشی

```javascript
new PersianDatePicker('#datetime', {
    format: 'YYYY/MM/DD - HH:mm',
    timePicker: true
});
```

### مثال 3: گوش دادن به تغییرات

```javascript
document.getElementById('myDate').addEventListener('change', function(e) {
    console.log('تاریخ انتخاب شده:', e.target.value);
});
```

## تست تقویم

برای تست تقویم، فایل زیر را در مرورگر باز کنید:

```
http://localhost/test-datepicker.html
```

این صفحه شامل:
- دو فیلد تاریخ برای تست
- نمایش نتایج انتخاب شده
- لیست کامل ویژگی‌ها
- طراحی زیبا برای نمایش

## صفحات استفاده شده

تقویم در صفحات زیر به‌روزرسانی شده است:

1. ✅ `resources/views/admin/listings/create.blade.php` - ایجاد حراجی
2. ✅ `resources/views/admin/listings/manage.blade.php` - مدیریت حراجی

## رفع مشکلات رایج

### تقویم باز نمی‌شود

- مطمئن شوید فایل‌های CSS و JS به درستی لود شده‌اند
- کنسول مرورگر را برای خطاها بررسی کنید
- مطمئن شوید کلاس `persian-datepicker-input` به input اضافه شده

### تاریخ به درستی نمایش داده نمی‌شود

- فرمت تاریخ را بررسی کنید
- مطمئن شوید اعداد فارسی به درستی تبدیل می‌شوند

### مشکل در موبایل

- تقویم به صورت responsive طراحی شده
- در صورت مشکل، viewport را بررسی کنید

## API Reference

### Constructor

```javascript
new PersianDatePicker(selector, options)
```

**Parameters:**
- `selector`: string | HTMLElement - انتخابگر یا المان
- `options`: object - تنظیمات (اختیاری)

### Methods

- `show()` - نمایش تقویم
- `hide()` - مخفی کردن تقویم
- `destroy()` - حذف تقویم
- `selectToday()` - انتخاب امروز
- `formatDate(date)` - فرمت کردن تاریخ
- `parseDate(dateStr)` - تجزیه رشته تاریخ

### Events

```javascript
input.addEventListener('change', function(e) {
    // تاریخ تغییر کرد
});
```

## مقایسه با نسخه قبلی

| ویژگی | نسخه قبلی | نسخه جدید |
|-------|----------|-----------|
| طراحی | ساده | مدرن و حرفه‌ای |
| انیمیشن | خیر | بله ✅ |
| Modal | خیر | بله ✅ |
| Responsive | محدود | کامل ✅ |
| سال کبیسه | بله | بله ✅ |
| دکمه امروز | خیر | بله ✅ |
| اعداد فارسی | بله | بله ✅ |

## پشتیبانی

در صورت بروز مشکل:

1. فایل `test-datepicker.html` را تست کنید
2. کنسول مرورگر را بررسی کنید
3. مطمئن شوید فایل‌های CSS و JS لود شده‌اند
4. نسخه مرورگر را به‌روزرسانی کنید

## نکات مهم

⚠️ **توجه**: این تقویم نیاز به JavaScript دارد و بدون آن کار نمی‌کند.

✅ **توصیه**: برای بهترین تجربه، از مرورگرهای مدرن استفاده کنید.

🎯 **نکته**: تقویم به صورت خودکار برای تمام inputهایی با کلاس `persian-datepicker-input` فعال می‌شود.

## لایسنس

این تقویم برای استفاده در پروژه حراجی ایرانی طراحی شده است.

---

**نسخه**: 2.0.0  
**تاریخ به‌روزرسانی**: 1403/12/02  
**توسعه‌دهنده**: تیم توسعه پلتفرم حراجی
