<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Listings</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background-color: #f2f2f2; }
        .draft { background-color: #fef3c7; }
        .pending { background-color: #dbeafe; }
        .active { background-color: #d1fae5; }
        .suspended { background-color: #fed7aa; }
    </style>
</head>
<body>
    <h1>Debug Listings Status</h1>
    <p>این صفحه برای debug وضعیت آگهی‌ها است</p>
    
    <?php
    // Load Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    // Get listings
    $listings = \App\Models\Listing::with('seller')->orderBy('id', 'desc')->take(20)->get();
    ?>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>عنوان</th>
                <th>فروشنده</th>
                <th>وضعیت (status)</th>
                <th>نوع (type)</th>
                <th>تاریخ ایجاد</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($listings as $listing): ?>
            <tr class="<?= $listing->status ?>">
                <td><?= $listing->id ?></td>
                <td><?= htmlspecialchars($listing->title) ?></td>
                <td><?= htmlspecialchars($listing->seller->name) ?></td>
                <td><strong><?= $listing->status ?></strong></td>
                <td><?= $listing->type ?></td>
                <td><?= $listing->created_at->format('Y-m-d H:i') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <h2>توضیحات وضعیت‌ها:</h2>
    <ul>
        <li><strong>draft:</strong> پیش‌نویس - نیاز به تایید ادمین</li>
        <li><strong>pending:</strong> در انتظار شروع - تایید شده ولی هنوز شروع نشده</li>
        <li><strong>active:</strong> فعال - در حال اجرا</li>
        <li><strong>suspended:</strong> معلق - توسط ادمین متوقف شده</li>
        <li><strong>completed:</strong> تکمیل شده</li>
        <li><strong>cancelled:</strong> لغو شده</li>
        <li><strong>rejected:</strong> رد شده</li>
    </ul>
</body>
</html>
