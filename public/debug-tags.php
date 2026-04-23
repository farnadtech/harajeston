<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tag = 'تست2';

echo "Searching for tag: $tag\n\n";

// Check raw data
$listings = DB::select("SELECT id, title, tags FROM listings WHERE tags IS NOT NULL LIMIT 10");
foreach ($listings as $l) {
    echo "ID: {$l->id} | Title: {$l->title}\n";
    echo "Tags raw: {$l->tags}\n";
    
    $tags = json_decode($l->tags, true);
    echo "Tags decoded: " . implode(', ', $tags ?? []) . "\n";
    
    if (in_array($tag, $tags ?? [])) {
        echo ">>> MATCH FOUND!\n";
    }
    echo "---\n";
}

echo "\n\nDirect SQL search:\n";
$results = DB::select("SELECT id, title, tags FROM listings WHERE JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag]);
echo "Found: " . count($results) . " results\n";

echo "\nwhereLike search:\n";
$results2 = DB::select("SELECT id, title, tags FROM listings WHERE tags LIKE ?", ['%' . $tag . '%']);
echo "Found: " . count($results2) . " results\n";
foreach ($results2 as $r) {
    echo "- {$r->title}: {$r->tags}\n";
}
