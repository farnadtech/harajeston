<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

echo "<h1>Force Refresh Dashboard</h1>";

try {
    // Clear view cache
    $viewPath = storage_path('framework/views');
    $files = glob($viewPath . '/*');
    $count = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    
    echo "<p>✓ Cleared {$count} cached view files</p>";
    
    // Clear compiled views
    if (file_exists(base_path('bootstrap/cache/config.php'))) {
        unlink(base_path('bootstrap/cache/config.php'));
        echo "<p>✓ Cleared config cache</p>";
    }
    
    // Clear route cache  
    $routeCache = base_path('bootstrap/cache/routes-v7.php');
    if (file_exists($routeCache)) {
        unlink($routeCache);
        echo "<p>✓ Cleared route cache</p>";
    }
    
    echo "<hr>";
    echo "<h2>Cache Cleared!</h2>";
    echo "<p><strong>Now do these steps:</strong></p>";
    echo "<ol>";
    echo "<li>Close ALL browser tabs for this site</li>";
    echo "<li>Clear browser cache (Ctrl+Shift+Delete)</li>";
    echo "<li>Open a NEW browser tab</li>";
    echo "<li>Go to: <a href='/haraj/public/dashboard'>/haraj/public/dashboard</a></li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<p><a href='/haraj/public/dashboard' style='display: inline-block; padding: 10px 20px; background: #135bec; color: white; text-decoration: none; border-radius: 5px;'>Go to Dashboard Now</a></p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
