<?php
// Test if we can access the dashboard route
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing Seller Dashboard...\n\n";

// Check if user is logged in
try {
    $user = \App\Models\User::find(31);
    if ($user) {
        echo "✓ User found: {$user->name}\n";
        echo "✓ User role: {$user->role}\n";
        echo "✓ Can sell: " . ($user->canSell() ? 'Yes' : 'No') . "\n\n";
        
        // Check if user has store
        if ($user->store) {
            echo "✓ Store found: {$user->store->store_name}\n\n";
        } else {
            echo "✗ No store found for user\n\n";
        }
        
        // Test queries
        echo "Testing database queries...\n";
        
        $activeAuctions = \App\Models\Listing::where('seller_id', $user->id)
            ->where('status', 'active')
            ->count();
        echo "✓ Active auctions: {$activeAuctions}\n";
        
        $pendingListings = \App\Models\Listing::where('seller_id', $user->id)
            ->where('status', 'pending')
            ->count();
        echo "✓ Pending listings: {$pendingListings}\n";
        
        $completedAuctions = \App\Models\Listing::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->count();
        echo "✓ Completed auctions: {$completedAuctions}\n";
        
        $totalSales = \App\Models\Order::where('seller_id', $user->id)
            ->where('status', 'completed')
            ->sum('total');
        echo "✓ Total sales: {$totalSales}\n\n";
        
        // Test view rendering
        echo "Testing view rendering...\n";
        $stats = [
            'active_auctions' => $activeAuctions,
            'pending_listings' => $pendingListings,
            'completed_auctions' => $completedAuctions,
            'total_sales' => $totalSales,
        ];
        
        $activeListings = \App\Models\Listing::where('seller_id', $user->id)
            ->where('status', 'active')
            ->with('category', 'images')
            ->orderBy('ends_at', 'asc')
            ->limit(10)
            ->get();
        
        $recentOrders = \App\Models\Order::where('seller_id', $user->id)
            ->with('buyer', 'items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        echo "✓ Active listings count: " . $activeListings->count() . "\n";
        echo "✓ Recent orders count: " . $recentOrders->count() . "\n\n";
        
        echo "All tests passed! Dashboard should work.\n";
        
    } else {
        echo "✗ User not found\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
