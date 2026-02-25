<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get all images that don't exist
$images = \App\Models\ListingImage::all();
$fixed = 0;

foreach ($images as $image) {
    $fullPath = storage_path('app/public/' . $image->file_path);
    
    if (!file_exists($fullPath)) {
        // Create directory if needed
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Create a simple placeholder image
        $img = imagecreatetruecolor(800, 600);
        $bgColor = imagecolorallocate($img, 240, 240, 240);
        $textColor = imagecolorallocate($img, 100, 100, 100);
        imagefill($img, 0, 0, $bgColor);
        
        $text = 'No Image';
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = (800 - $textWidth) / 2;
        $y = (600 - $textHeight) / 2;
        
        imagestring($img, $font, $x, $y, $text, $textColor);
        
        // Save based on extension
        $ext = pathinfo($image->file_path, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg', 'jpeg'])) {
            imagejpeg($img, $fullPath, 90);
        } else {
            imagepng($img, $fullPath);
        }
        
        imagedestroy($img);
        $fixed++;
    }
}

echo "<h2>Image Restoration Complete</h2>";
echo "<p>Fixed {$fixed} missing images</p>";
echo "<p><a href='/haraj/public'>Go to Home</a></p>";
