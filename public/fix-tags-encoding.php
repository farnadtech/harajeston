<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Fix Tags Double Encoding</h2>";

$listings = DB::table('listings')->whereNotNull('tags')->get();

echo "<p>Found " . $listings->count() . " listings with tags</p>";

foreach ($listings as $listing) {
    echo "<hr>";
    echo "<h3>Listing ID: {$listing->id} - {$listing->title}</h3>";
    
    echo "<p><strong>Before:</strong> " . htmlspecialchars($listing->tags) . "</p>";
    
    // Decode once
    $decoded = json_decode($listing->tags, true);
    
    if (is_array($decoded)) {
        // Already correct format
        echo "<p style='color: green;'>✓ Already correct format</p>";
    } else {
        // Double encoded - decode again
        $decoded = json_decode($decoded, true);
        
        if (is_array($decoded)) {
            // Fix it
            $fixed = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            
            DB::table('listings')
                ->where('id', $listing->id)
                ->update(['tags' => $fixed]);
            
            echo "<p><strong>After:</strong> " . htmlspecialchars($fixed) . "</p>";
            echo "<p style='color: blue;'>✓ Fixed!</p>";
        } else {
            echo "<p style='color: red;'>✗ Cannot fix - invalid format</p>";
        }
    }
}

echo "<hr>";
echo "<h3>Done!</h3>";
echo "<p><a href='test-tags-cast.php?slug=tst-frnad-1'>Test Again</a></p>";
