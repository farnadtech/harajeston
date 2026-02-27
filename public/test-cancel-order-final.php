<?php
// Test cancel order functionality
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h2>Testing Cancel Order Functionality</h2>";

// Check enum values
echo "<h3>1. Check wallet_transactions type ENUM values:</h3>";
echo "<pre>";
$result = DB::select("SHOW COLUMNS FROM wallet_transactions WHERE Field = 'type'");
if (!empty($result)) {
    echo "Type column definition:\n";
    print_r($result[0]);
    
    // Check if our new types are there
    $typeDefinition = $result[0]->Type;
    $hasOrderCancellation = strpos($typeDefinition, 'order_cancellation_penalty') !== false;
    $hasRevenue = strpos($typeDefinition, 'order_cancellation_penalty_revenue') !== false;
    $hasUnfreeze = strpos($typeDefinition, 'unfreeze_refund') !== false;
    
    echo "\n\n✓ Checking new types:\n";
    echo "  - order_cancellation_penalty: " . ($hasOrderCancellation ? '✓ EXISTS' : '✗ MISSING') . "\n";
    echo "  - order_cancellation_penalty_revenue: " . ($hasRevenue ? '✓ EXISTS' : '✗ MISSING') . "\n";
    echo "  - unfreeze_refund: " . ($hasUnfreeze ? '✓ EXISTS' : '✗ MISSING') . "\n";
}
echo "</pre>";

// Check if user is logged in
$user = auth()->user();
if (!$user) {
    echo "<p style='color: red;'>Please login first to test cancel order functionality</p>";
    echo "<p><a href='/login'>Go to Login</a></p>";
    exit;
}

echo "<h3>2. Current User:</h3>";
echo "<p>Name: {$user->name}</p>";
echo "<p>Email: {$user->email}</p>";
echo "<p>Role: {$user->role}</p>";

// Get wallet info
$wallet = $user->wallet;
echo "<h3>3. Wallet Info:</h3>";
echo "<p>Balance: " . number_format($wallet->balance) . " تومان</p>";
echo "<p>Frozen: " . number_format($wallet->frozen) . " تومان</p>";

// Get a processing order
$order = \App\Models\Order::where(function($q) use ($user) {
    $q->where('buyer_id', $user->id)
      ->orWhere('seller_id', $user->id);
})
->where('status', 'processing')
->first();

if (!$order) {
    echo "<h3>4. No Processing Orders Found</h3>";
    echo "<p>You need a processing order to test cancel functionality</p>";
    
    // Show all orders
    $allOrders = \App\Models\Order::where(function($q) use ($user) {
        $q->where('buyer_id', $user->id)
          ->orWhere('seller_id', $user->id);
    })->get();
    
    if ($allOrders->count() > 0) {
        echo "<h4>Your Orders:</h4>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Order #</th><th>Status</th><th>Total</th><th>Role</th></tr>";
        foreach ($allOrders as $o) {
            $role = $o->buyer_id === $user->id ? 'Buyer' : 'Seller';
            echo "<tr>";
            echo "<td>{$o->order_number}</td>";
            echo "<td>{$o->status}</td>";
            echo "<td>" . number_format($o->total) . "</td>";
            echo "<td>{$role}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<h3>4. Found Processing Order:</h3>";
    echo "<p>Order Number: {$order->order_number}</p>";
    echo "<p>Status: {$order->status}</p>";
    echo "<p>Total: " . number_format($order->total) . " تومان</p>";
    
    $isBuyer = $order->buyer_id === $user->id;
    $isSeller = $order->seller_id === $user->id;
    echo "<p>Your Role: " . ($isBuyer ? 'Buyer' : 'Seller') . "</p>";
    
    // Calculate penalty
    $penaltyType = \App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage');
    $penaltyValue = (float) \App\Models\SiteSetting::get('order_cancellation_penalty_value', 10);
    
    if ($penaltyType === 'percentage') {
        $penalty = ($order->total * $penaltyValue) / 100;
    } else {
        $penalty = $penaltyValue;
    }
    
    echo "<h3>5. Penalty Calculation:</h3>";
    echo "<p>Penalty Type: {$penaltyType}</p>";
    echo "<p>Penalty Value: {$penaltyValue}</p>";
    echo "<p>Calculated Penalty: " . number_format($penalty) . " تومان</p>";
    echo "<p>Can Afford: " . ($wallet->balance >= $penalty ? '✓ YES' : '✗ NO') . "</p>";
    
    echo "<h3>6. Test Cancel Order:</h3>";
    echo "<p><a href='/orders/{$order->id}' style='padding: 10px 20px; background: blue; color: white; text-decoration: none; border-radius: 5px;'>Go to Order Page</a></p>";
}

echo "<hr>";
echo "<p><a href='/dashboard'>Back to Dashboard</a></p>";
