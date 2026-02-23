<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "<h1>Testing Seller Dashboard Render</h1>";

try {
    if (!auth()->check()) {
        echo "<p style='color: red;'>Not authenticated. <a href='/haraj/public/login'>Login here</a></p>";
        exit;
    }
    
    $user = auth()->user();
    echo "<p>✓ User: {$user->name}</p>";
    
    // Prepare data
    $stats = [
        'active_auctions' => 5,
        'pending_listings' => 2,
        'completed_auctions' => 10,
        'total_sales' => 5000000,
    ];
    
    $activeListings = collect([]);
    $recentOrders = collect([]);
    
    echo "<p>✓ Data prepared</p>";
    echo "<hr>";
    
    // Render view
    $html = view('dashboard.seller', compact('stats', 'activeListings', 'recentOrders'))->render();
    
    echo "<p>✓ View rendered. Length: " . strlen($html) . " bytes</p>";
    
    // Check for specific sections
    $hasSearch = strpos($html, 'جستجو') !== false;
    $hasNotification = strpos($html, 'notifications') !== false;
    $hasChart = strpos($html, 'نمودار فروش') !== false;
    $hasActivities = strpos($html, 'فعالیت‌های اخیر') !== false;
    
    echo "<h2>Section Check:</h2>";
    echo "<p>" . ($hasSearch ? "✓" : "✗") . " Search field</p>";
    echo "<p>" . ($hasNotification ? "✓" : "✗") . " Notification button</p>";
    echo "<p>" . ($hasChart ? "✓" : "✗") . " Sales chart</p>";
    echo "<p>" . ($hasActivities ? "✓" : "✗") . " Recent activities</p>";
    
    echo "<hr>";
    echo "<h2>Rendered HTML:</h2>";
    echo $html;
    
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
