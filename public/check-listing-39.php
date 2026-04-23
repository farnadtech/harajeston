<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check site settings
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "تنظیمات تایید ادمین: " . ($requireApproval ? 'فعال' : 'غیرفعال') . "\n\n";

// Check listing 39
$listing = \App\Models\Listing::find(39);
if ($listing) {
    echo "آگهی #39:\n";
    echo "عنوان: {$listing->title}\n";
    echo "وضعیت: {$listing->status}\n";
    echo "فروشنده: {$listing->seller->name} (ID: {$listing->seller_id})\n";
    echo "تاریخ ایجاد: {$listing->created_at}\n";
    echo "تاریخ تایید: " . ($listing->approved_at ?? 'تایید نشده') . "\n";
    echo "\n";
} else {
    echo "آگهی #39 یافت نشد!\n";
}
