# 🔧 راه حل قطعی لود ویژگی‌ها

## مشکل
بعد از انتخاب دسته‌بندی، ویژگی‌ها لود نمی‌شن.

## علت احتمالی
1. API endpoint کار نمی‌کنه
2. Alpine.js event listener اجرا نمی‌شه
3. Event dispatch نمی‌شه

## تست
باز کن: `http://localhost/test-attributes-load.html`

### مراحل تست:
1. روی دکمه "تست API برای دسته 58" کلیک کن
2. اگر API کار کرد، روی "Dispatch Event برای دسته 58" کلیک کن
3. لاگ‌ها رو بررسی کن

## راه حل‌های احتمالی

### راه حل 1: بررسی API Route
```bash
php artisan route:list | grep categories
```

باید این route وجود داشته باشه:
```
GET|HEAD  api/categories/{category}/attributes
```

### راه حل 2: اضافه کردن Route (اگر نیست)
در `routes/api.php`:
```php
Route::get('/categories/{category}/attributes', [CategoryController::class, 'getAttributes']);
```

### راه حل 3: اضافه کردن متد در Controller
در `app/Http/Controllers/Api/CategoryController.php`:
```php
public function getAttributes($categoryId)
{
    $attributes = \App\Models\CategoryAttribute::where('category_id', $categoryId)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get(['id', 'name', 'type', 'options', 'is_required']);
    
    return response()->json([
        'attributes' => $attributes
    ]);
}
```

### راه حل 4: Debug Alpine.js
اضافه کن به console:
```javascript
console.log('Alpine data:', section.__x.$data);
console.log('Has category:', section.__x.$data.hasCategory);
console.log('Loading:', section.__x.$data.loading);
console.log('Attributes:', section.__x.$data.attributes);
```

## نتیجه تست
بعد از تست، نتیجه رو اینجا بنویس تا بفهمیم کجا مشکل هست.
