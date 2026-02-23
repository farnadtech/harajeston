<?php
header('Content-Type: text/html; charset=utf-8');

$query = $_GET['q'] ?? 'تست';
$url = "http://localhost/haraj/public/api/listings/search?q=" . urlencode($query);

echo "<h2>تست API جستجو</h2>";
echo "<p>URL: <code>$url</code></p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>HTTP Status: $httpCode</h3>";

if ($error) {
    echo "<p style='color: red;'>خطا: $error</p>";
}

echo "<h3>پاسخ:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; direction: ltr;'>";
echo htmlspecialchars($response);
echo "</pre>";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "<h3>تعداد نتایج: " . count($data) . "</h3>";
    
    if (count($data) > 0) {
        echo "<h3>نتایج:</h3>";
        echo "<ul>";
        foreach ($data as $item) {
            echo "<li>";
            echo "<strong>" . htmlspecialchars($item['title']) . "</strong><br>";
            echo "قیمت: " . number_format($item['price']) . " تومان<br>";
            echo "URL: " . htmlspecialchars($item['url']);
            echo "</li>";
        }
        echo "</ul>";
    }
}

echo "<hr>";
echo "<form method='get'>";
echo "جستجو: <input type='text' name='q' value='" . htmlspecialchars($query) . "'>";
echo "<button type='submit'>جستجو</button>";
echo "</form>";
?>
