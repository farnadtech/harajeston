<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::find(39);
if ($listing) {
    echo "قبل از تغییر:\n";
    echo "وضعیت: {$listing->status}\n";
    echo "زمان شروع: {$listing->starts_at}\n";
    echo "الان: " . now() . "\n\n";
    
    if ($listing->starts_at <= now() && $listing->status === 'pending') {
        $listing->status = 'active';
        $listing->save();
        
        echo "بعد از تغییر:\n";
        echo "وضعیت: {$listing->status}\n";
        echo "✓ آگهی فعال شد!\n";
    } else {
        echo "نیازی به تغییر نیست.\n";
    }
} else {
    echo "آگهی #39 یافت نشد!\n";
}
