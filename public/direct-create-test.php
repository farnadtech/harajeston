<?php
// Direct test of create method

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Direct Controller Test</h1>";
echo "<div style='font-family: Tahoma; padding: 20px;'>";

try {
    // Check if user is authenticated
    if (!Auth::check()) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✗ Not authenticated</strong><br>";
        echo "You need to login first.<br>";
        echo "<a href='/haraj/public/login'>Go to Login</a>";
        echo "</div>";
        exit;
    }
    
    $user = Auth::user();
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✓ Authenticated</strong><br>";
    echo "User: " . $user->name . "<br>";
    echo "Role: " . $user->role . "<br>";
    echo "</div>";
    
    if ($user->role !== 'seller') {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>⚠ Not a seller</strong><br>";
        echo "Your role is: " . $user->role . "<br>";
        echo "You need to be a seller to create listings.";
        echo "</div>";
        exit;
    }
    
    // Try to instantiate controller
    $listingService = app(App\Services\ListingService::class);
    $depositService = app(App\Services\DepositService::class);
    $controller = new App\Http\Controllers\ListingController($listingService, $depositService);
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✓ Controller instantiated successfully</strong><br>";
    echo "</div>";
    
    // Try to call create method
    echo "<h2>Calling create() method...</h2>";
    $response = $controller->create();
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✓ create() method executed successfully!</strong><br>";
    echo "Response type: " . get_class($response) . "<br>";
    echo "</div>";
    
    // Render the view
    echo "<h2>Rendering view...</h2>";
    echo "<hr>";
    echo $response->render();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✗ Error:</strong><br>";
    echo $e->getMessage() . "<br><br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "</div>";
