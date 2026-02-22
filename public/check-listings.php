<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<h1>Check Listings</h1>";

$listings = \App\Models\Listing::orderBy('id', 'desc')->take(10)->get();

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Seller</th></tr>";

foreach ($listings as $listing) {
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>" . htmlspecialchars($listing->title) . "</td>";
    echo "<td>{$listing->status}</td>";
    echo "<td>{$listing->seller->name}</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Check if ID 17 exists:</h2>";
$listing17 = \App\Models\Listing::find(17);
if ($listing17) {
    echo "<p style='color:green;'>✓ Listing 17 EXISTS</p>";
    echo "<p>Title: {$listing17->title}</p>";
    echo "<p>Status: {$listing17->status}</p>";
} else {
    echo "<p style='color:red;'>✗ Listing 17 NOT FOUND</p>";
}
?>
