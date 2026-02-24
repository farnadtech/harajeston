<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Login as buyer (user_id = 40 from previous debug)
$user = \App\Models\User::find(40);

if (!$user) {
    echo "User 40 not found. Trying first buyer...\n";
    $user = \App\Models\User::where('role', 'buyer')->first();
}

if ($user) {
    echo "User: {$user->name} (ID: {$user->id})\n";
    echo "Role: {$user->role}\n\n";
    
    // Get listings where user has bids
    $listings = \App\Models\Listing::whereHas('bids', function($q) use ($user) {
        $q->where('user_id', $user->id);
    })->with(['images'])->get();
    
    echo "Found {$listings->count()} listings with bids\n\n";
    
    foreach ($listings as $listing) {
        echo "Listing: {$listing->title}\n";
        echo "  Images: " . $listing->images->count() . "\n";
        if ($listing->images->count() > 0) {
            $img = $listing->images->first();
            echo "  First image file_path: {$img->file_path}\n";
            echo "  Asset URL: " . asset('storage/' . $img->file_path) . "\n";
        }
        echo "\n";
    }
} else {
    echo "No user found\n";
}
