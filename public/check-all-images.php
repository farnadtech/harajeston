<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$images = \App\Models\ListingImage::with('listing')->take(10)->get();

echo "<h2>All Images (first 10)</h2>";
foreach ($images as $img) {
    $exists = file_exists(storage_path('app/public/' . $img->file_path));
    echo "<p>";
    echo "<strong>Listing:</strong> {$img->listing->title}<br>";
    echo "<strong>File Path:</strong> {$img->file_path}<br>";
    echo "<strong>Full Path:</strong> " . storage_path('app/public/' . $img->file_path) . "<br>";
    echo "<strong>Exists:</strong> " . ($exists ? 'YES' : 'NO') . "<br>";
    echo "</p>";
}

echo "<h3>Storage Directory Check:</h3>";
$storagePublic = storage_path('app/public');
echo "<p>Storage public path: {$storagePublic}</p>";
echo "<p>Exists: " . (is_dir($storagePublic) ? 'YES' : 'NO') . "</p>";

if (is_dir($storagePublic . '/listings')) {
    echo "<p>Listings folder exists</p>";
    $files = scandir($storagePublic . '/listings');
    echo "<p>Files in listings: " . implode(', ', array_diff($files, ['.', '..'])) . "</p>";
}
