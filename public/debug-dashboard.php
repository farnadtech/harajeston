<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Dashboard Debug</h1>";

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<p>✓ Laravel loaded</p>";
    
    // Start session
    $app->make('Illuminate\Contracts\Http\Kernel')->handle(
        Illuminate\Http\Request::capture()
    );
    
    echo "<p>✓ Kernel loaded</p>";
    
    // Check if user is authenticated
    if (auth()->check()) {
        $user = auth()->user();
        echo "<p>✓ User authenticated: {$user->name}</p>";
        echo "<p>✓ User ID: {$user->id}</p>";
        echo "<p>✓ User role: {$user->role}</p>";
        echo "<p>✓ Can sell: " . ($user->canSell() ? 'Yes' : 'No') . "</p>";
        
        // Check store
        if ($user->store) {
            echo "<p>✓ Store exists: {$user->store->store_name}</p>";
        } else {
            echo "<p>⚠ No store found</p>";
        }
        
        // Try to render the view
        echo "<hr><h2>Attempting to render dashboard...</h2>";
        
        $stats = [
            'active_auctions' => \App\Models\Listing::where('seller_id', $user->id)->where('status', 'active')->count(),
            'pending_listings' => \App\Models\Listing::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'completed_auctions' => \App\Models\Listing::where('seller_id', $user->id)->where('status', 'completed')->count(),
            'total_sales' => \App\Models\Order::where('seller_id', $user->id)->where('status', 'completed')->sum('total'),
        ];
        
        $activeListings = \App\Models\Listing::where('seller_id', $user->id)
            ->where('status', 'active')
            ->with('category', 'images')
            ->limit(10)
            ->get();
        
        $recentOrders = \App\Models\Order::where('seller_id', $user->id)
            ->with('buyer', 'items')
            ->limit(10)
            ->get();
        
        echo "<p>✓ Stats loaded</p>";
        echo "<p>✓ Active listings: " . $activeListings->count() . "</p>";
        echo "<p>✓ Recent orders: " . $recentOrders->count() . "</p>";
        
        echo "<hr><h2>Rendering view...</h2>";
        
        $html = view('dashboard.seller', compact('stats', 'activeListings', 'recentOrders'))->render();
        
        echo "<p>✓ View rendered successfully!</p>";
        echo "<p>HTML length: " . strlen($html) . " bytes</p>";
        
        echo "<hr>";
        echo $html;
        
    } else {
        echo "<p>✗ User not authenticated</p>";
        echo "<p><a href='/haraj/public/login'>Login here</a></p>";
    }
    
} catch (\Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
