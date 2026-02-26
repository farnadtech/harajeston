<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Listing;

$listing = Listing::with('images')->find(24);

if ($listing && $listing->images->first()) {
    $image = $listing->images->first();
    echo "Image file_path: {$image->file_path}\n";
    echo "Full path should be: storage/{$image->file_path}\n";
    echo "Using asset(): " . asset('storage/' . $image->file_path) . "\n";
    echo "Using url(): " . url('storage/' . $image->file_path) . "\n";
    
    // Check if file exists
    $fullPath = storage_path('app/public/' . $image->file_path);
    echo "\nFile exists at {$fullPath}: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
} else {
    echo "No image found for listing 24\n";
}
