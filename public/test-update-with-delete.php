<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>تست آپدیت با حذف عکس</h2>";

// Get a listing with multiple images
$listing = \App\Models\Listing::with('images')->whereHas('images')->first();

if (!$listing) {
    echo "<p style='color:red'>هیچ حراجی با عکس پیدا نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>تعداد عکس‌های قبل: " . $listing->images->count() . "</p>";

if ($listing->images->count() > 1) {
    $imageToDelete = $listing->images->last();
    echo "<p>عکس برای حذف: ID = {$imageToDelete->id}</p>";
    
    // Simulate form data
    $data = [
        'title' => $listing->title,
        'description' => $listing->description,
        'category_id' => $listing->category_id,
        'condition' => $listing->condition,
        'starting_price' => $listing->starting_price,
        'buy_now_price' => $listing->buy_now_price,
        'bid_increment' => $listing->bid_increment,
        'starts_at' => $listing->starts_at,
        'ends_at' => $listing->ends_at,
        'auto_extend' => $listing->auto_extend,
        'shipping_methods' => $listing->shippingMethods->pluck('id')->toArray(),
        'deleted_images' => (string)$imageToDelete->id, // This is the key field
    ];
    
    echo "<pre>Data being sent:\n";
    print_r($data);
    echo "</pre>";
    
    try {
        $listingService = app(\App\Services\ListingService::class);
        $updated = $listingService->updateListing($listing, $data);
        
        echo "<p style='color:green'>✓ حراجی با موفقیت آپدیت شد!</p>";
        echo "<p>تعداد عکس‌های بعد: " . $updated->images->count() . "</p>";
        
        if ($updated->images->count() < $listing->images->count()) {
            echo "<p style='color:green; font-weight:bold'>✓✓ عکس با موفقیت حذف شد!</p>";
        } else {
            echo "<p style='color:red; font-weight:bold'>✗ عکس حذف نشد!</p>";
        }
        
    } catch (\Exception $e) {
        echo "<p style='color:red'>✗ خطا: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p style='color:orange'>این حراجی فقط یک عکس دارد.</p>";
}
