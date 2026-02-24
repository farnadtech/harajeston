# Tags Fix - Complete Solution

## مشکلات

### 1. برچسب‌ها در صفحه ویرایش نشان داده نمی‌شدند
- دلیل: Double encoding در دیتابیس
- برچسب‌ها به صورت `"[\"تگ\"]"` ذخیره می‌شدند به جای `["تگ"]`

### 2. جستجو با برچسب کار نمی‌کرد
- دلیل: فقط از `JSON_SEARCH` استفاده می‌شد که با فرمت جدید کار نمی‌کرد

## راه‌حل‌ها

### 1. فیکس ListingService (app/Services/ListingService.php)

مشکل اصلی: در متد `updateListing`، وقتی tags به صورت array می‌آمد، دوباره `json_encode` می‌شد، در حالی که مدل Listing خودش cast به array داره و دوباره encode می‌کنه.

**قبل:**
```php
'tags' => isset($data['tags']) ? (is_array($data['tags']) ? json_encode($data['tags'], JSON_UNESCAPED_UNICODE) : $this->processTags($data['tags'])) : $listing->tags,
```

**بعد:**
```php
'tags' => isset($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : $this->processTagsToArray($data['tags'])) : $listing->tags,
```

**متد جدید اضافه شده:**
```php
protected function processTagsToArray(string $tagsString): ?array
{
    $tags = array_map('trim', explode(',', $tagsString));
    $tags = array_filter($tags);
    $tags = array_slice($tags, 0, 5);
    
    return !empty($tags) ? array_values($tags) : null;
}
```

### 2. فیکس جستجو (app/Http/Controllers/ListingController.php)

**قبل:**
```php
if ($request->has('tag') && $request->tag) {
    $tag = trim($request->tag);
    $query->whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag]);
}
```

**بعد:**
```php
if ($request->has('tag') && $request->tag) {
    $tag = trim($request->tag);
    $query->where(function($q) use ($tag) {
        $q->whereJsonContains('tags', $tag)
          ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag]);
    });
}
```

این تغییر از هر دو متد استفاده می‌کنه تا با فرمت‌های قدیمی و جدید کار کنه.

### 3. فیکس دیتای موجود

اسکریپت `public/fix-tags-final.php` برای فیکس دیتای double-encoded:

```php
$correctTags = ["برچسب"];
\DB::table('listings')
    ->where('id', $listing_id)
    ->update(['tags' => json_encode($correctTags, JSON_UNESCAPED_UNICODE)]);
```

## نحوه کار سیستم

### 1. ذخیره برچسب‌ها
- کاربر وارد می‌کنه: `"تست, جدید, برچسب"`
- `UpdateListingRequest` تبدیل می‌کنه به: `["تست", "جدید", "برچسب"]`
- `ListingService` آرایه رو مستقیم به مدل می‌ده
- مدل Listing با cast `'tags' => 'array'` خودش encode می‌کنه
- در دیتابیس ذخیره می‌شه: `["تست","جدید","برچسب"]`

### 2. نمایش در صفحه ویرایش
```php
value="{{ old('tags', is_array($listing->tags) ? implode(', ', $listing->tags) : '') }}"
```
- مدل cast می‌کنه به array
- با `implode` تبدیل می‌شه به string با کاما

### 3. نمایش در صفحه محصول
```php
@foreach($listing->tags as $tag)
    <a href="{{ route('listings.index', ['tag' => $tag]) }}">
        #{{ $tag }}
    </a>
@endforeach
```

### 4. جستجو با برچسب
- کلیک روی برچسب → `?tag=تست`
- Controller با `whereJsonContains` جستجو می‌کنه
- نتایج فیلتر شده نمایش داده می‌شوند

## تست‌ها

✅ نمایش برچسب‌ها در صفحه ویرایش  
✅ ذخیره برچسب‌های جدید  
✅ نمایش برچسب‌ها در صفحه محصول  
✅ جستجو با کلیک روی برچسب  
✅ عدم double encoding  

## فایل‌های تغییر یافته

1. `app/Services/ListingService.php`
   - تغییر نحوه ذخیره tags در updateListing
   - اضافه کردن متد processTagsToArray

2. `app/Http/Controllers/ListingController.php`
   - بهبود جستجو با برچسب (whereJsonContains + JSON_SEARCH)

3. `resources/views/listings/edit.blade.php`
   - نمایش صحیح برچسب‌ها با implode

## نکات مهم

- مدل Listing دارای cast `'tags' => 'array'` است
- هرگز نباید دستی `json_encode` کرد وقتی مدل cast داره
- برای جستجو از `whereJsonContains` استفاده کن (Laravel 5.7+)
- همیشه `JSON_UNESCAPED_UNICODE` برای فارسی استفاده کن

## وضعیت

✅ **تمام مشکلات برطرف شد**
- برچسب‌ها در صفحه ویرایش نمایش داده می‌شوند
- برچسب‌های جدید صحیح ذخیره می‌شوند
- جستجو با برچسب کار می‌کند
- دیگر double encoding رخ نمی‌دهد
