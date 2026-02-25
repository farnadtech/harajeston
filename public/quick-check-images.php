<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::with('images')->first();
if ($listing && $listing->images->count() > 0) {
    $img = $listing->images->first();
    echo "Listing: {$listing->title}\n";
    echo "File Path: {$img->file_path}\n";
    echo "URL: {$img->url}\n";
    echo "Full Path: " . storage_path('app/public/' . $img->file_path) . "\n";
    echo "Exists: " . (file_exists(storage_path('app/public/' . $img->file_path)) ? 'YES' : 'NO') . "\n";
} else {
    echo "No images found\n";
}
