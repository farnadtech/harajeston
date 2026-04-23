<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a seller
$seller = \App\Models\User::where('role', 'seller')->where('seller_status', 'active')->first();

$listingService = app(\App\Services\ListingService::class);

$data = [
    'title' => 'تست حراجی با سپرده - ' . now()->format('H:i:s'),
    'description' => 'این یک حراجی تستی با سپرده است',
    'category_id' => 1,
    'condition' => 'new',
    'starting_price' => 10000,
    'buy_now_price' => 50000,
    'deposit_amount' => 5000, // سپرده 5000 تومان
    'starts_at' => now()->format('Y-m-d H:i:s'),
    'ends_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
    'shipping_methods' => [1],
];

$listing = $listingService->createListing($seller, $data);

echo "✓ حراجی با سپرده ایجاد شد:\n";
echo "ID: {$listing->id}\n";
echo "عنوان: {$listing->title}\n";
echo "Slug: {$listing->slug}\n";
echo "قیمت شروع: " . number_format($listing->starting_price) . " تومان\n";
echo "قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
echo "مبلغ سپرده: " . number_format($listing->deposit_amount) . " تومان\n";
echo "وضعیت: {$listing->status}\n\n";

echo "لینک: http://localhost/haraj/public/listings/{$listing->slug}\n";
