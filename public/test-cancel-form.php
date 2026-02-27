<?php
// Test cancel order form submission
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get authenticated user
$user = auth()->user();
if (!$user) {
    die("Please login first");
}

// Get an order
$order = \App\Models\Order::where('buyer_id', $user->id)
    ->orWhere('seller_id', $user->id)
    ->where('status', 'processing')
    ->first();

if (!$order) {
    die("No processing orders found for testing");
}

echo "<h2>Testing Cancel Order Form</h2>";
echo "<p>Order ID: {$order->id}</p>";
echo "<p>Order Number: {$order->order_number}</p>";
echo "<p>Status: {$order->status}</p>";
echo "<p>Total: " . number_format($order->total) . " تومان</p>";

// Check authorization
$isBuyer = $order->buyer_id === $user->id;
$isSeller = $order->seller_id === $user->id;

echo "<p>Is Buyer: " . ($isBuyer ? 'Yes' : 'No') . "</p>";
echo "<p>Is Seller: " . ($isSeller ? 'Yes' : 'No') . "</p>";

// Get penalty settings
$penaltyType = \App\Models\SiteSetting::get('order_cancellation_penalty_type', 'percentage');
$penaltyValue = (float) \App\Models\SiteSetting::get('order_cancellation_penalty_value', 10);

echo "<p>Penalty Type: {$penaltyType}</p>";
echo "<p>Penalty Value: {$penaltyValue}</p>";

if ($penaltyType === 'percentage') {
    $penalty = ($order->total * $penaltyValue) / 100;
} else {
    $penalty = $penaltyValue;
}

echo "<p>Calculated Penalty: " . number_format($penalty) . " تومان</p>";

// Check wallet balance
$wallet = $user->wallet;
echo "<p>Wallet Balance: " . number_format($wallet->balance) . " تومان</p>";
echo "<p>Can Afford Penalty: " . ($wallet->balance >= $penalty ? 'Yes' : 'No') . "</p>";

// Test form submission
echo "<hr>";
echo "<h3>Test Form Submission</h3>";
echo "<form action='" . route('orders.cancelWithPenalty', $order) . "' method='POST'>";
echo csrf_field();
echo "<button type='submit' style='padding: 10px 20px; background: red; color: white; border: none; border-radius: 5px; cursor: pointer;'>لغو سفارش (تست)</button>";
echo "</form>";

echo "<hr>";
echo "<h3>Direct Test</h3>";
echo "<button onclick='testCancel()' style='padding: 10px 20px; background: blue; color: white; border: none; border-radius: 5px; cursor: pointer;'>تست با AJAX</button>";
echo "<div id='result' style='margin-top: 20px; padding: 10px; background: #f0f0f0;'></div>";

?>

<script>
function testCancel() {
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = 'در حال ارسال...';
    
    fetch('<?php echo route('orders.cancelWithPenalty', $order); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text();
    })
    .then(data => {
        console.log('Response data:', data);
        resultDiv.innerHTML = '<pre>' + data + '</pre>';
    })
    .catch(error => {
        console.error('Error:', error);
        resultDiv.innerHTML = '<span style="color: red;">خطا: ' + error.message + '</span>';
    });
}
</script>
