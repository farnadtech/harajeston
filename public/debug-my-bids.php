<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a user's bids
$user = \App\Models\User::where('role', 'buyer')->first();

if (!$user) {
    echo "No buyer found";
    exit;
}

echo "<h2>Debug My Bids for User: {$user->name}</h2>";

$bids = \App\Models\Bid::where('user_id', $user->id)
    ->with(['listing.images', 'listing.seller'])
    ->latest()
    ->take(3)
    ->get();

echo "<h3>Found " . $bids->count() . " bids</h3>";

foreach ($bids as $bid) {
    $listing = $bid->listing;
    
    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
    echo "<h4>Listing: {$listing->title}</h4>";
    echo "<p><strong>Status:</strong> {$listing->status}</p>";
    echo "<p><strong>End Time:</strong> {$listing->end_time}</p>";
    echo "<p><strong>End Time (parsed):</strong> " . \Carbon\Carbon::parse($listing->end_time)->format('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>Now:</strong> " . now()->format('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>Is Future:</strong> " . (\Carbon\Carbon::parse($listing->end_time)->isFuture() ? 'YES' : 'NO') . "</p>";
    
    echo "<h5>Images:</h5>";
    if ($listing->images->count() > 0) {
        foreach ($listing->images as $image) {
            echo "<p>Image Path: {$image->image_path}</p>";
            echo "<p>Full URL: " . asset('storage/' . $image->image_path) . "</p>";
            $fullPath = storage_path('app/public/' . $image->image_path);
            echo "<p>File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "</p>";
            if (file_exists($fullPath)) {
                echo "<img src='" . asset('storage/' . $image->image_path) . "' style='max-width: 200px;'>";
            }
        }
    } else {
        echo "<p>No images</p>";
    }
    
    echo "</div>";
}
