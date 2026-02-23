<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <title>تست داشبورد</title>
</head>
<body>
    <h1>داشبورد فروشنده</h1>
    <p>نام: {{ auth()->user()->name }}</p>
    <p>مزایده‌های فعال: {{ $stats['active_auctions'] ?? 0 }}</p>
    <p>در انتظار تایید: {{ $stats['pending_listings'] ?? 0 }}</p>
    <p>تکمیل شده: {{ $stats['completed_auctions'] ?? 0 }}</p>
    <p>درآمد کل: {{ number_format($stats['total_sales'] ?? 0) }} تومان</p>
</body>
</html>
