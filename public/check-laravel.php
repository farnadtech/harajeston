<?php
echo "<h1>Laravel Check</h1>";
echo "<pre>";

// Check if Laravel is running
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "✓ Vendor folder exists\n";
    require __DIR__ . '/../vendor/autoload.php';
    
    if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
        echo "✓ Bootstrap file exists\n";
        
        try {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "✓ Laravel app loaded\n";
            
            // Check routes
            echo "\n--- Checking Routes ---\n";
            echo "Login route: " . (route_exists('login') ? '✓ EXISTS' : '✗ NOT FOUND') . "\n";
            echo "Register route: " . (route_exists('register') ? '✓ EXISTS' : '✗ NOT FOUND') . "\n";
            
        } catch (Exception $e) {
            echo "✗ Error loading Laravel: " . $e->getMessage() . "\n";
        }
    } else {
        echo "✗ Bootstrap file not found\n";
    }
} else {
    echo "✗ Vendor folder not found\n";
    echo "Run: composer install\n";
}

function route_exists($name) {
    try {
        route($name);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

echo "\n--- URLs to test ---\n";
echo "Login page: " . $_SERVER['HTTP_HOST'] . "/login\n";
echo "Register page: " . $_SERVER['HTTP_HOST'] . "/register\n";
echo "Auth page: " . $_SERVER['HTTP_HOST'] . "/auth\n";

echo "</pre>";

echo "<hr>";
echo "<h2>Quick Links</h2>";
echo "<a href='/login'>Go to Login</a><br>";
echo "<a href='/register'>Go to Register</a><br>";
?>
