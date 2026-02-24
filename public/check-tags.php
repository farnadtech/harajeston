<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slug = $_GET['slug'] ?? 'tst-frnad-1';
$listing = \App\Models\Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found: $slug");
}

echo "<h2>Listing: {$listing->title}</h2>";
echo "<h3>Tags (Raw from DB):</h3>";
echo "<pre>";
var_dump($listing->tags);
echo "</pre>";

echo "<h3>Tags Type:</h3>";
echo "<pre>";
echo gettype($listing->tags);
echo "</pre>";

echo "<h3>Is Array?</h3>";
echo "<pre>";
echo is_array($listing->tags) ? 'YES' : 'NO';
echo "</pre>";

if (is_array($listing->tags)) {
    echo "<h3>Imploded Tags:</h3>";
    echo "<pre>";
    echo implode(', ', $listing->tags);
    echo "</pre>";
}
