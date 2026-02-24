<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get listings without images
$listings = \App\Models\Listing::whereDoesntHave('images')->get();

echo "<h2>Adding Placeholder Images</h2>";
echo "<p>Found {$listings->count()} listings without images</p>";

// Create a simple placeholder image
$placeholderPath = 'listings/placeholder.jpg';
$storagePath = storage_path('app/public/' . $placeholderPath);

// Create directory if it doesn't exist
if (!file_exists(dirname($storagePath))) {
    mkdir(dirname($storagePath), 0755, true);
}

// Create a simple colored image if it doesn't exist
if (!file_exists($storagePath)) {
    $img = imagecreatetruecolor(800, 600);
    $bgColor = imagecolorallocate($img, 200, 200, 200);
    $textColor = imagecolorallocate($img, 100, 100, 100);
    imagefill($img, 0, 0, $bgColor);
    
    $text = 'No Image';
    $font = 5;
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (800 - $textWidth) / 2;
    $y = (600 - $textHeight) / 2;
    
    imagestring($img, $font, $x, $y, $text, $textColor);
    imagejpeg($img, $storagePath, 90);
    imagedestroy($img);
    echo "<p>Created placeholder image</p>";
}

// Add placeholder image to listings
$added = 0;
foreach ($listings as $listing) {
    \App\Models\ListingImage::create([
        'listing_id' => $listing->id,
        'file_path' => $placeholderPath,
        'file_name' => 'placeholder.jpg',
        'display_order' => 1,
    ]);
    $added++;
}

echo "<p>Added placeholder images to {$added} listings</p>";
echo "<p><a href='/haraj/public/my-bids'>View My Bids</a></p>";
