<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slug = $_GET['slug'] ?? 'tst-frnad-1';
$listing = \App\Models\Listing::where('slug', $slug)->first();

echo "<h2>Test Tags Cast</h2>";

echo "<h3>1. Raw Attribute:</h3>";
echo "<pre>";
var_dump($listing->getAttributes()['tags']);
echo "</pre>";

echo "<h3>2. Casted Attribute:</h3>";
echo "<pre>";
var_dump($listing->tags);
echo "</pre>";

echo "<h3>3. Is Array?</h3>";
echo "<pre>";
echo is_array($listing->tags) ? 'YES' : 'NO';
echo "</pre>";

echo "<h3>4. Implode Test:</h3>";
echo "<pre>";
if (is_array($listing->tags)) {
    echo implode(', ', $listing->tags);
} else {
    echo "NOT AN ARRAY - Cannot implode";
}
echo "</pre>";

echo "<h3>5. View Code Test:</h3>";
echo "<pre>";
$value = old('tags', is_array($listing->tags) ? implode(', ', $listing->tags) : '');
echo "Value: " . $value;
echo "</pre>";
