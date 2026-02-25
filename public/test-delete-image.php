<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>تست حذف عکس</h2>";

// Get a listing with multiple images
$listing = \App\Models\Listing::with('images')->whereHas('images')->first();

if (!$listing) {
    echo "<p style='color:red'>هیچ حراجی با عکس پیدا نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>تعداد عکس‌های قبل از حذف: " . $listing->images->count() . "</p>";

if ($listing->images->count() > 1) {
    $imageToDelete = $listing->images->last();
    echo "<p>عکس برای حذف: ID = {$imageToDelete->id}, Path = {$imageToDelete->file_path}</p>";
    
    // Test deletion
    try {
        $imageService = app(\App\Services\ImageService::class);
        $result = $imageService->delete($imageToDelete, true);
        
        echo "<p style='color:green'>✓ عکس با موفقیت حذف شد!</p>";
        
        // Reload listing
        $listing = $listing->fresh();
        echo "<p>تعداد عکس‌های بعد از حذف: " . $listing->images->count() . "</p>";
        
    } catch (\Exception $e) {
        echo "<p style='color:red'>✗ خطا در حذف: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:orange'>این حراجی فقط یک عکس دارد، نمیتوان حذف کرد.</p>";
}

echo "<hr>";
echo "<h4>عکس‌های باقیمانده:</h4>";
foreach ($listing->images as $img) {
    echo "<div style='margin:10px 0; padding:10px; border:1px solid #ddd;'>";
    echo "ID: {$img->id} | Path: {$img->file_path}<br>";
    echo "<img src='{$img->url}' style='max-width:200px;'>";
    echo "</div>";
}
