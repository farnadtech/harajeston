<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Auth Page</h1>";
echo "<pre>";

// Check if Laravel can load
try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "✓ Autoload successful\n";
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✓ App loaded\n";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ Kernel loaded\n";
    
    // Create a request for /login
    $request = Illuminate\Http\Request::create('/login', 'GET');
    echo "✓ Request created\n";
    
    // Handle the request
    $response = $kernel->handle($request);
    echo "✓ Response generated\n";
    
    echo "\n--- Response Status: " . $response->getStatusCode() . " ---\n";
    
    // Show the content
    echo "\n--- Response Content (first 500 chars) ---\n";
    echo substr($response->getContent(), 0, 500);
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
