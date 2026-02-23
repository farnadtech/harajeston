<?php
// Simple test for listing create route

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Start session
$app->make('Illuminate\Session\Middleware\StartSession')->handle(
    Illuminate\Http\Request::capture(), 
    function($req) { return $req; }
);

echo "<h1>Listing Create Route Test</h1>";

// Check auth
if (Auth::check()) {
    $user = Auth::user();
    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h2>✓ Logged In</h2>";
    echo "Name: <strong>" . $user->name . "</strong><br>";
    echo "Email: " . $user->email . "<br>";
    echo "Role: <strong>" . $user->role . "</strong><br>";
    echo "</div>";
    
    if ($user->role === 'seller') {
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h2>✓ You are a seller!</h2>";
        echo "<p>You should be able to access the create listing page.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h2>⚠ Not a seller</h2>";
        echo "<p>Your role is: " . $user->role . "</p>";
        echo "<p>You need to be a seller to create listings.</p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<h2>✗ Not logged in</h2>";
    echo "</div>";
}

echo "<h2>Try These Links:</h2>";
echo "<div style='background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 5px;'>";

// Try to generate route URL
try {
    $createUrl = route('listings.create');
    echo "<p><strong>Route URL:</strong> <a href='$createUrl' style='color: #007bff; font-weight: bold;'>$createUrl</a></p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error generating route: " . $e->getMessage() . "</p>";
}

// Direct URLs to try
echo "<h3>Direct URLs to try:</h3>";
echo "<ol>";
echo "<li><a href='/haraj/public/listings/create' style='color: #007bff;'>/haraj/public/listings/create</a></li>";
echo "<li><a href='http://localhost/haraj/public/listings/create' style='color: #007bff;'>http://localhost/haraj/public/listings/create</a></li>";
echo "</ol>";

echo "</div>";

// Check if controller exists
echo "<h2>Controller Check:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
if (class_exists('App\Http\Controllers\ListingController')) {
    echo "<p style='color: green;'>✓ ListingController exists</p>";
    
    $controller = new App\Http\Controllers\ListingController(
        app(App\Services\ListingService::class),
        app(App\Services\DepositService::class)
    );
    
    if (method_exists($controller, 'create')) {
        echo "<p style='color: green;'>✓ create() method exists</p>";
    } else {
        echo "<p style='color: red;'>✗ create() method NOT found</p>";
    }
} else {
    echo "<p style='color: red;'>✗ ListingController NOT found</p>";
}
echo "</div>";

// Check if view exists
echo "<h2>View Check:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
$viewPath = resource_path('views/listings/create-new.blade.php');
if (file_exists($viewPath)) {
    echo "<p style='color: green;'>✓ View file exists: listings/create-new.blade.php</p>";
    echo "<p>Path: $viewPath</p>";
} else {
    echo "<p style='color: red;'>✗ View file NOT found</p>";
    echo "<p>Expected path: $viewPath</p>";
}
echo "</div>";
