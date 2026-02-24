<?php
$html = file_get_contents('http://localhost/haraj/public/my-bids');
preg_match_all('/src="([^"]+storage[^"]+)"/', $html, $matches);

echo "Found " . count($matches[1]) . " storage images:\n\n";
foreach ($matches[1] as $src) {
    echo $src . "\n";
}
