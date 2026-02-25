<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>بررسی صفحه ویرایش حراجی</h2>";

// Get a listing with images
$listing = \App\Models\Listing::with('images')->whereHas('images')->first();

if (!$listing) {
    echo "<p style='color:red'>هیچ حراجی با عکس پیدا نشد!</p>";
    exit;
}

echo "<h3>حراجی: {$listing->title}</h3>";
echo "<p>تعداد عکس‌ها: " . $listing->images->count() . "</p>";

echo "<h4>عکس‌های فعلی:</h4>";
echo "<div style='display:grid; grid-template-columns: repeat(4, 1fr); gap:10px;'>";
foreach ($listing->images as $image) {
    echo "<div style='border:1px solid #ddd; padding:10px; border-radius:8px;'>";
    echo "<img src='{$image->url}' style='width:100%; height:150px; object-fit:cover; border-radius:4px;'><br>";
    echo "<small>ID: {$image->id}</small><br>";
    echo "<small>Path: {$image->file_path}</small><br>";
    echo "<small>Order: {$image->display_order}</small>";
    echo "</div>";
}
echo "</div>";

echo "<hr>";
echo "<h4>لینک ویرایش:</h4>";
echo "<a href='" . url("/listings/{$listing->slug}/edit") . "' target='_blank' style='display:inline-block; padding:10px 20px; background:#135bec; color:white; text-decoration:none; border-radius:8px;'>ویرایش حراجی</a>";
