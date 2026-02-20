<?php
// Test file to check if view file has been updated
$viewPath = __DIR__ . '/../resources/views/listings/index.blade.php';
$content = file_get_contents($viewPath);

echo "<h1>View File Content Check</h1>";
echo "<p>File size: " . filesize($viewPath) . " bytes</p>";
echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($viewPath)) . "</p>";

// Check for specific content
if (strpos($content, '\App\Models\Listing::where') !== false) {
    echo "<p style='color: green; font-weight: bold;'>✓ File has been UPDATED (contains new code)</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ File is OLD (does not contain new code)</p>";
}

// Show first 500 characters
echo "<h2>First 500 characters:</h2>";
echo "<pre>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";
