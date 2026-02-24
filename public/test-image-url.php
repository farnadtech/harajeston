<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a listing with image
$listing = \App\Models\Listing::whereHas('images')->with('images')->first();

if ($listing && $listing->images->count() > 0) {
    $image = $listing->images->first();
    
    echo "<h2>Image URL Test</h2>";
    echo "<p><strong>Listing:</strong> {$listing->title}</p>";
    echo "<p><strong>File Path:</strong> {$image->file_path}</p>";
    echo "<p><strong>URL Accessor:</strong> {$image->url}</p>";
    echo "<p><strong>Asset Helper:</strong> " . asset('storage/' . $image->file_path) . "</p>";
    echo "<p><strong>Storage URL:</strong> " . \Storage::url($image->file_path) . "</p>";
    
    echo "<h3>Test Image Display:</h3>";
    echo "<img src='{$image->url}' style='max-width: 300px; border: 2px solid red;'><br>";
    echo "<p>Red border = using accessor</p>";
    
    echo "<img src='" . asset('storage/' . $image->file_path) . "' style='max-width: 300px; border: 2px solid blue;'><br>";
    echo "<p>Blue border = using asset helper</p>";
    
    // Check if file exists
    $fullPath = storage_path('app/public/' . $image->file_path);
    echo "<p><strong>File exists:</strong> " . (file_exists($fullPath) ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>Full path:</strong> {$fullPath}</p>";
} else {
    echo "<p>No listing with images found</p>";
}
