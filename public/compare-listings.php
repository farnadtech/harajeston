<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$l1 = \App\Models\Listing::where('slug', 'tst-frnad-1')->with('images')->first();
$l2 = \App\Models\Listing::where('slug', 'tblt-aypd-pro-129-aynch')->with('images')->first();

echo "<h2>Comparison</h2>";

echo "<h3>Listing 1: {$l1->title}</h3>";
echo "Images: " . $l1->images->count() . "<br>";
if ($l1->images->count() > 0) {
    $img = $l1->images->first();
    echo "File Path: {$img->file_path}<br>";
    echo "URL: {$img->url}<br>";
    echo "Exists: " . (file_exists(storage_path('app/public/' . $img->file_path)) ? 'YES' : 'NO') . "<br>";
    echo "<img src='{$img->url}' style='max-width:200px'><br>";
}

echo "<h3>Listing 2: {$l2->title}</h3>";
echo "Images: " . $l2->images->count() . "<br>";
if ($l2->images->count() > 0) {
    $img = $l2->images->first();
    echo "File Path: {$img->file_path}<br>";
    echo "URL: {$img->url}<br>";
    echo "Exists: " . (file_exists(storage_path('app/public/' . $img->file_path)) ? 'YES' : 'NO') . "<br>";
    echo "<img src='{$img->url}' style='max-width:200px'><br>";
}
