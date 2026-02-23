# 🎯 راه حل قطعی فلش Select در RTL

## مشکل
فلش select در سمت راست قرار داره و باید به سمت چپ منتقل بشه.

## راه حل نهایی

### گام 1: استفاده از CSS ساده
```css
select {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-color: white !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-size: 1.5em 1.5em !important;
    background-position: left 0.5rem center !important;
    padding-left: 2.5rem !important;
    padding-right: 0.75rem !important;
}
```

### گام 2: اگر بالا کار نکرد، از این استفاده کن
```css
select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E") no-repeat;
    background-size: 1.5em 1.5em;
    background-position: 0.5rem center;
    padding: 0.75rem 0.75rem 0.75rem 2.5rem;
}
```

## تست
باز کن: `http://localhost/test-select-arrow.html`

## نکات مهم
1. از `!important` استفاده کن تا Tailwind رو override کنه
2. `background-position: left 0.5rem center` فلش رو در سمت چپ قرار میده
3. `padding-left: 2.5rem` فضا برای فلش ایجاد می‌کنه
