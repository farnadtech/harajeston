<?php
// Test if create route is accessible

echo "<h1>Testing Listing Create Route</h1>";

// Check if Laravel is loaded
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Route Test</h2>";

// Get all routes
$routes = Route::getRoutes();

echo "<h3>Looking for listings.create route:</h3>";
foreach ($routes as $route) {
    if ($route->getName() === 'listings.create') {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>✓ Route Found!</strong><br>";
        echo "Name: " . $route->getName() . "<br>";
        echo "URI: " . $route->uri() . "<br>";
        echo "Method: " . implode(', ', $route->methods()) . "<br>";
        echo "Action: " . $route->getActionName() . "<br>";
        echo "Middleware: " . implode(', ', $route->middleware()) . "<br>";
        echo "</div>";
    }
}

echo "<h3>All Listing Routes:</h3>";
echo "<ul>";
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'listing')) {
        echo "<li>";
        echo "<strong>" . $route->getName() . "</strong> - ";
        echo $route->uri() . " [" . implode(', ', $route->methods()) . "]";
        echo "</li>";
    }
}
echo "</ul>";

// Check authentication
echo "<h2>Authentication Check</h2>";
if (Auth::check()) {
    $user = Auth::user();
    echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>✓ User is logged in</strong><br>";
    echo "Name: " . $user->name . "<br>";
    echo "Email: " . $user->email . "<br>";
    echo "Role: " . $user->role . "<br>";
    echo "</div>";
    
    if ($user->role === 'seller') {
        echo "<div style='background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>✓ User is a seller - can create listings</strong><br>";
        echo "<a href='/haraj/public/listings/create' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Go to Create Listing</a>";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>⚠ User is not a seller</strong><br>";
        echo "Only sellers can create listings. Current role: " . $user->role;
        echo "</div>";
    }
} else {
    echo "<div style='background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>✗ User is not logged in</strong><br>";
    echo "You need to login first.<br>";
    echo "<a href='/haraj/public/login' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Go to Login</a>";
    echo "</div>";
}

echo "<h2>URL Test</h2>";
echo "<p>Try these URLs:</p>";
echo "<ul>";
echo "<li><a href='/haraj/public/listings/create'>Direct URL: /haraj/public/listings/create</a></li>";
echo "<li><a href='" . route('listings.create') . "'>Using route() helper: " . route('listings.create') . "</a></li>";
echo "</ul>";
