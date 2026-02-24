<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get active listings with images
$listings = \App\Models\Listing::where('status', 'active')
    ->with('images')
    ->limit(5)
    ->get();

echo "<h2>Active Listings with Images</h2>";

foreach ($listings as $listing) {
    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
    echo "<h4>{$listing->title}</h4>";
    echo "<p><strong>ID:</strong> {$listing->id}</p>";
    echo "<p><strong>Status:</strong> {$listing->status}</p>";
    echo "<p><strong>Images Count:</strong> " . $listing->images->count() . "</p>";
    
    if ($listing->images->count() > 0) {
        echo "<h5>Images:</h5>";
        foreach ($listing->images as $image) {
            echo "<p>File Path: {$image->file_path}</p>";
            echo "<p>URL: {$image->url}</p>";
            echo "<img src='{$image->url}' style='max-width: 200px; margin: 10px 0;'><br>";
        }
    } else {
        echo "<p>No images found</p>";
    }
    echo "</div>";
}
