<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::find(39);
if ($listing) {
    echo "آگهی #39 - {$listing->title}\n";
    echo "وضعیت: {$listing->status}\n\n";
    
    echo "زمان‌ها:\n";
    echo "ایجاد: {$listing->created_at}\n";
    echo "شروع: {$listing->starts_at}\n";
    echo "پایان: {$listing->ends_at}\n";
    echo "الان: " . now() . "\n\n";
    
    echo "مقایسه:\n";
    echo "آیا زمان شروع رسیده؟ " . ($listing->starts_at <= now() ? 'بله' : 'خیر') . "\n";
    echo "آیا زمان پایان رسیده؟ " . ($listing->ends_at <= now() ? 'بله' : 'خیر') . "\n\n";
    
    echo "تنظیمات:\n";
    echo "تایید ادمین: " . (\App\Models\SiteSetting::get('require_listing_approval', false) ? 'فعال' : 'غیرفعال') . "\n";
    echo "تاریخ تایید: " . ($listing->approved_at ?? 'تایید نشده') . "\n";
}
