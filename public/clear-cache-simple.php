<?php
// Clear all cache files
$cacheDir = __DIR__ . '/../bootstrap/cache';

if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            echo "Deleted: " . basename($file) . "<br>";
        }
    }
}

echo "<br>✓ Cache cleared!<br>";
echo "<a href='/haraj/public/orders/15'>Go to order</a>";
