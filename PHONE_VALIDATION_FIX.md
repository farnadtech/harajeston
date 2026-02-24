# اعتبارسنجی شماره تلفن در ثبت نام

## خلاصه
اضافه شدن validation کامل برای شماره تلفن در صفحه ثبت نام که اطمینان می‌دهد شماره تلفن:
- دقیقاً 11 رقم باشد
- با 09 شروع شود
- تکراری نباشد

## تغییرات

### 1. Frontend Validation (resources/views/auth/register.blade.php)

#### فیلد Input
```html
<input class="..." 
       id="phone"
       name="phone" 
       type="text"
       pattern="09[0-9]{9}"
       maxlength="11"
       required
       oninput="validatePhone(this)"/>
```

**ویژگی‌های اضافه شده:**
- `id="phone"` - برای دسترسی از JavaScript
- `pattern="09[0-9]{9}"` - الگوی HTML5 (11 رقم، شروع با 09)
- `maxlength="11"` - محدودیت طول
- `oninput="validatePhone(this)"` - اعتبارسنجی زنده

#### پیام‌های راهنما
```html
<p class="text-xs text-gray-500 mt-1">شماره موبایل باید 11 رقمی و با 09 شروع شود</p>
<p id="phoneError" class="text-xs text-red-600 mt-1 hidden">شماره تلفن باید 11 رقمی و با 09 شروع شود</p>
```

#### JavaScript Validation
```javascript
function validatePhone(input) {
    // حذف کاراکترهای غیر عددی
    let value = input.value.replace(/\D/g, '');
    
    // محدود کردن به 11 رقم
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    
    input.value = value;
    
    // بررسی فرمت (11 رقم + شروع با 09)
    if (value.length === 11 && value.startsWith('09')) {
        // معتبر است
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-200');
        errorElement.classList.add('hidden');
    } else if (value.length > 0) {
        // نامعتبر است
        input.classList.add('border-red-500');
        errorElement.classList.remove('hidden');
    }
}
```

**ویژگی‌ها:**
- حذف خودکار کاراکترهای غیر عددی
- محدودیت به 11 رقم
- نمایش/مخفی کردن پیام خطا
- تغییر رنگ border

#### Form Submit Validation
```javascript
document.querySelector('form').addEventListener('submit', function(e) {
    const phoneInput = document.getElementById('phone');
    const phone = phoneInput.value;
    
    if (phone.length !== 11 || !phone.startsWith('09')) {
        e.preventDefault();
        phoneInput.classList.add('border-red-500');
        document.getElementById('phoneError').classList.remove('hidden');
        phoneInput.focus();
        return false;
    }
});
```

جلوگیری از ارسال فرم اگر شماره نامعتبر باشد.

### 2. Backend Validation (routes/web.php)

```php
'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users'],
```

**قوانین اعتبارسنجی:**
- `required` - الزامی
- `string` - رشته متنی
- `regex:/^09[0-9]{9}$/` - الگوی regex (دقیقاً 11 رقم، شروع با 09)
- `unique:users` - تکراری نباشد

**پیام‌های خطا:**
```php
'phone.required' => 'شماره تلفن الزامی است.',
'phone.regex' => 'شماره تلفن باید 11 رقمی و با 09 شروع شود.',
'phone.unique' => 'این شماره تلفن قبلاً ثبت شده است.',
```

## الگوی Regex

```regex
^09[0-9]{9}$
```

**توضیح:**
- `^` - شروع رشته
- `09` - باید با 09 شروع شود
- `[0-9]{9}` - دقیقاً 9 رقم دیگر (0 تا 9)
- `$` - پایان رشته

**مجموع:** 2 رقم (09) + 9 رقم = 11 رقم

## مثال‌های معتبر

✅ `09123456789`  
✅ `09351234567`  
✅ `09901234567`  
✅ `09191234567`  

## مثال‌های نامعتبر

❌ `9123456789` - فاقد 0 در ابتدا  
❌ `091234567` - کمتر از 11 رقم  
❌ `091234567890` - بیشتر از 11 رقم  
❌ `08123456789` - شروع با 08  
❌ `09-123-456-789` - دارای خط تیره  
❌ `09 12 345 6789` - دارای فاسله  

## نحوه کار

### 1. کاربر شروع به تایپ می‌کند
- JavaScript خودکار کاراکترهای غیر عددی را حذف می‌کند
- طول به 11 رقم محدود می‌شود

### 2. اعتبارسنجی زنده (Real-time)
- با هر تغییر، فرمت بررسی می‌شود
- اگر نامعتبر باشد:
  - Border قرمز می‌شود
  - پیام خطا نمایش داده می‌شود

### 3. ارسال فرم
- JavaScript قبل از ارسال بررسی می‌کند
- اگر نامعتبر باشد، فرم ارسال نمی‌شود

### 4. اعتبارسنجی سرور
- Laravel با regex بررسی می‌کند
- بررسی تکراری بودن در دیتابیس
- در صورت خطا، کاربر به صفحه ثبت نام برمی‌گردد

## مزایا

### امنیت
- جلوگیری از ورود شماره‌های نامعتبر
- جلوگیری از ثبت شماره تکراری

### تجربه کاربری
- اعتبارسنجی زنده (بدون نیاز به ارسال فرم)
- پیام‌های خطای واضح
- حذف خودکار کاراکترهای اضافی

### سازگاری
- فرمت استاندارد ایران (09xxxxxxxxx)
- پشتیبانی از همه اپراتورها

## تست

### تست Frontend
1. باز کردن صفحه ثبت نام
2. وارد کردن شماره‌های مختلف:
   - `09123456789` → باید قبول شود
   - `9123456789` → باید خطا دهد
   - `091234567` → باید خطا دهد
   - `abc09123456789` → حروف باید حذف شوند

### تست Backend
1. ارسال فرم با شماره معتبر → باید ثبت شود
2. ارسال فرم با شماره نامعتبر → باید خطا برگردد
3. ارسال فرم با شماره تکراری → باید خطا برگردد

## فایل‌های تغییر یافته

1. `resources/views/auth/register.blade.php`
   - اضافه شدن attributes به input
   - اضافه شدن پیام‌های راهنما
   - اضافه شدن JavaScript validation

2. `routes/web.php`
   - تغییر validation rule برای phone
   - اضافه شدن regex pattern
   - اضافه شدن unique check
   - اضافه شدن پیام‌های خطای فارسی

## وضعیت

✅ **پیاده‌سازی کامل شد**
- Frontend validation با JavaScript
- Backend validation با Laravel
- پیام‌های خطای فارسی
- اعتبارسنجی زنده
- بررسی تکراری بودن
