<?php
// Get the HTML of my-bids page and check image src
$html = file_get_contents('http://localhost/haraj/public/my-bids');

// Extract image src attributes
preg_match_all('/<img[^>]+src="([^"]+)"[^>]*>/i', $html, $matches);

echo "<h2>Image Sources in My Bids Page</h2>";
echo "<p>Found " . count($matches[1]) . " images</p>";

foreach ($matches[1] as $src) {
    echo "<p><strong>Image src:</strong> " . htmlspecialchars($src) . "</p>";
}

// Show first 2000 chars of HTML around first image
if (count($matches[0]) > 0) {
    $pos = strpos($html, $matches[0][0]);
    $start = max(0, $pos - 500);
    $snippet = substr($html, $start, 2000);
    echo "<h3>HTML Snippet:</h3>";
    echo "<pre>" . htmlspecialchars($snippet) . "</pre>";
}
