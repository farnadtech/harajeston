<?php
// Create storage symlink manually
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

// Remove existing link if exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } else {
        echo "Error: 'storage' exists but is not a symlink<br>";
        exit;
    }
}

// Create symlink
if (symlink($target, $link)) {
    echo "✅ Storage link created successfully!<br>";
    echo "Target: $target<br>";
    echo "Link: $link<br>";
} else {
    echo "❌ Failed to create storage link<br>";
    echo "Try running: php artisan storage:link<br>";
}
