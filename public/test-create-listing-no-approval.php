<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check settings
$requireApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
echo "تنظیمات تایید ادمین: " . ($requireApproval ? 'فعال' : 'غیرفعال') . "\n\n";

// Get a seller
$seller = \App\Models\User::where('role', 'seller')->where('seller_status', 'active')->first();
if (!$seller) {
    echo "فروشنده‌ای یافت نشد!\n";
    exit;
}

echo "فروشنده: {$seller->name} (ID: {$seller->id})\n\n";

// Create a listing that starts now
$listingService = app(\App\Services\ListingService::class);

$data = [
    'title' => 'تست آگهی بدون تایید - ' . now()->format('H:i:s'),
    'description' => 'این یک آگهی تستی است',
    'category_id' => 1,
    'condition' => 'new',
    'starting_price' => 10000,
    'buy_now_price' => 50000,
    'deposit_amount' => 5000,
    'starts_at' => now()->format('Y-m-d H:i:s'),
    'ends_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
    'shipping_methods' => [1],
];

$listing = $listingService->createListing($seller, $data);

echo "آگهی ایجاد شد:\n";
echo "ID: {$listing->id}\n";
echo "عنوان: {$listing->title}\n";
echo "وضعیت: {$listing->status}\n";
echo "زمان شروع: {$listing->starts_at}\n";
echo "تاریخ تایید: " . ($listing->approved_at ?? 'null') . "\n\n";

if ($listing->status === 'active') {
    echo "✓ آگهی مستقیماً فعال شد (چون تایید ادمین غیرفعال است و زمان شروع رسیده)\n";
} elseif ($listing->status === 'pending') {
    echo "⚠ آگهی در وضعیت pending است\n";
}
