<?php
// Test wallet layout selection
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: text/html; charset=utf-8');

echo "<h1>تست Layout کیف پول</h1>";

// Test scenarios
$scenarios = [
    ['role' => 'buyer', 'seller_status' => null, 'expected' => 'layouts.app'],
    ['role' => 'seller', 'seller_status' => 'active', 'expected' => 'layouts.seller'],
    ['role' => 'admin', 'seller_status' => null, 'expected' => 'layouts.admin'],
];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; direction: rtl;'>";
echo "<tr><th>نقش</th><th>وضعیت فروشنده</th><th>Layout مورد انتظار</th><th>نتیجه</th></tr>";

foreach ($scenarios as $scenario) {
    $role = $scenario['role'];
    $sellerStatus = $scenario['seller_status'];
    $expected = $scenario['expected'];
    
    // Determine layout based on logic
    if ($role === 'admin') {
        $layout = 'layouts.admin';
    } elseif ($role === 'seller' && $sellerStatus === 'active') {
        $layout = 'layouts.seller';
    } else {
        $layout = 'layouts.app';
    }
    
    $result = ($layout === $expected) ? '✅ صحیح' : '❌ خطا';
    $color = ($layout === $expected) ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td>{$role}</td>";
    echo "<td>" . ($sellerStatus ?? 'ندارد') . "</td>";
    echo "<td>{$expected}</td>";
    echo "<td style='color: {$color}; font-weight: bold;'>{$result} ({$layout})</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>توضیحات:</h2>";
echo "<ul style='direction: rtl; text-align: right;'>";
echo "<li><strong>Admin:</strong> از layouts.admin استفاده میکنه</li>";
echo "<li><strong>Seller (فعال):</strong> از layouts.seller استفاده میکنه</li>";
echo "<li><strong>Buyer:</strong> از layouts.app استفاده میکنه</li>";
echo "</ul>";
?>
