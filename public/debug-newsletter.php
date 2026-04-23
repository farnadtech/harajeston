<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=iranian_auction', 'root', '');
$stmt = $pdo->query("DESCRIBE newsletter_subscribers");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($cols);
$stmt2 = $pdo->query("SELECT * FROM newsletter_subscribers LIMIT 5");
$rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
echo "</pre>";
