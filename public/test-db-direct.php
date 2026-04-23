<?php
echo "Testing DB connection...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=iranian_auction', 'root', '', [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "PDO Connected OK!\n";
    $result = $pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
    echo "site_settings count: $result\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
