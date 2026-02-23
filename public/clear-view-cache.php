<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<h1>Clearing View Cache...</h1>";

try {
    // Clear view cache
    $viewPath = storage_path('framework/views');
    
    if (is_dir($viewPath)) {
        $files = glob($viewPath . '/*');
        $count = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        
        echo "<p>✓ Cleared {$count} cached view files</p>";
    } else {
        echo "<p>✗ View cache directory not found</p>";
    }
    
    // Clear config cache
    if (file_exists(base_path('bootstrap/cache/config.php'))) {
        unlink(base_path('bootstrap/cache/config.php'));
        echo "<p>✓ Cleared config cache</p>";
    }
    
    // Clear route cache
    if (file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
        unlink(base_path('bootstrap/cache/routes-v7.php'));
        echo "<p>✓ Cleared route cache</p>";
    }
    
    echo "<h2>Cache cleared successfully!</h2>";
    echo "<p><a href='/haraj/public/dashboard'>Go to Dashboard</a></p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
