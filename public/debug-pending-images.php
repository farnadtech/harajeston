<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// پیدا کردن یک pending change که images داره
$change = \App\Models\ListingPendingChange::whereNotNull('changes->images')->first();

if (!$change) {
    echo "هیچ pending change با images پیدا نشد\n";
    exit;
}

echo "Pending Change ID: {$change->id}\n";
echo "Listing ID: {$change->listing_id}\n\n";

$changes = $change->changes;
echo "تمام تغییرات:\n";
print_r($changes);

if (isset($changes['images'])) {
    echo "\n\nمحتوای images:\n";
    print_r($changes['images']);
    
    echo "\n\nنوع داده images: " . gettype($changes['images']) . "\n";
    
    if (is_array($changes['images'])) {
        echo "تعداد آیتم‌ها: " . count($changes['images']) . "\n\n";
        
        foreach ($changes['images'] as $index => $imageData) {
            echo "Image #{$index}:\n";
            print_r($imageData);
            
            if (isset($imageData['id'])) {
                $image = \App\Models\ListingImage::find($imageData['id']);
                if ($image) {
                    echo "  ✓ تصویر پیدا شد: {$image->file_path}\n";
                } else {
                    echo "  ✗ تصویر با ID {$imageData['id']} پیدا نشد\n";
                }
            } else {
                echo "  ✗ فیلد id وجود ندارد\n";
            }
            echo "\n";
        }
    }
}

echo "\n\nتصاویر فعلی listing:\n";
$listing = \App\Models\Listing::find($change->listing_id);
foreach ($listing->images as $img) {
    echo "  - ID: {$img->id}, Path: {$img->file_path}\n";
}
