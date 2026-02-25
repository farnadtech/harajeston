<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::where('slug', 'tst-frnad-1')->with('images')->first();

if ($listing) {
    echo "Listing: {$listing->title}\n";
    echo "ID: {$listing->id}\n";
    echo "Images count: " . $listing->images->count() . "\n\n";
    
    if ($listing->images->count() > 0) {
        foreach ($listing->images as $img) {
            echo "Image ID: {$img->id}\n";
            echo "File Path: {$img->file_path}\n";
            echo "URL: {$img->url}\n";
            $fullPath = storage_path('app/public/' . $img->file_path);
            echo "Full Path: {$fullPath}\n";
            echo "Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n\n";
        }
    } else {
        echo "No images found for this listing\n";
    }
} else {
    echo "Listing not found\n";
}
