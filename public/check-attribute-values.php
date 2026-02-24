<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get listing by slug from URL
$slug = $_GET['slug'] ?? 'tst-frnad-1';

$listing = \App\Models\Listing::where('slug', $slug)->first();

if (!$listing) {
    die("Listing not found: $slug");
}

echo "<h2>Listing: {$listing->title}</h2>";
echo "<p>ID: {$listing->id}</p>";
echo "<p>Category ID: {$listing->category_id}</p>";

echo "<h3>Attribute Values (Raw Query):</h3>";
$values = DB::table('listing_attribute_values')
    ->where('listing_id', $listing->id)
    ->get();

echo "<pre>";
print_r($values->toArray());
echo "</pre>";

echo "<h3>Attribute Values (Relationship):</h3>";
$listing->load('attributeValues');
echo "<pre>";
print_r($listing->attributeValues->toArray());
echo "</pre>";

echo "<h3>Plucked Values:</h3>";
$plucked = $listing->attributeValues->pluck('value', 'category_attribute_id')->toArray();
echo "<pre>";
print_r($plucked);
echo "</pre>";

echo "<h3>JSON for JavaScript:</h3>";
echo "<pre>";
echo json_encode($plucked, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo "</pre>";
