<?php
// Test if routes are accessible
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<h1>Route Test</h1>";
echo "<p>Testing admin listing routes...</p>";

$routes = [
    'admin.listings.approve' => ['listing' => 17],
    'admin.listings.suspend' => ['listing' => 16],
    'admin.listings.activate' => ['listing' => 17],
    'admin.listings.reject' => ['listing' => 17],
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Route Name</th><th>Generated URL</th></tr>";

foreach ($routes as $name => $params) {
    try {
        $url = route($name, $params);
        echo "<tr><td>$name</td><td>$url</td></tr>";
    } catch (Exception $e) {
        echo "<tr><td>$name</td><td style='color:red'>Error: " . $e->getMessage() . "</td></tr>";
    }
}

echo "</table>";
?>
