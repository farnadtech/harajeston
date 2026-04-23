<?php
echo "<pre style='font-size:13px;padding:15px'>";

// تست 1: curl به google
echo "=== تست curl ===\n";
$ch = curl_init('https://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);
echo $err ? "✗ curl خطا: $err\n" : "✓ curl به google وصل شد\n";

// تست 2: file_get_contents
echo "\n=== تست file_get_contents ===\n";
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$r = @file_get_contents('http://api.payamak-panel.com', false, $ctx);
echo $r !== false ? "✓ file_get_contents کار می‌کند\n" : "✗ file_get_contents بلاک شده\n";

// تست 3: DNS
echo "\n=== تست DNS ===\n";
$ip = gethostbyname('api.payamak-panel.com');
echo $ip !== 'api.payamak-panel.com' ? "✓ DNS: $ip\n" : "✗ DNS resolve نشد\n";

// تست 4: socket
echo "\n=== تست socket ===\n";
$sock = @fsockopen('api.payamak-panel.com', 80, $errno, $errstr, 5);
echo $sock ? "✓ socket port 80 باز است\n" : "✗ socket خطا: $errstr ($errno)\n";
if ($sock) fclose($sock);

echo "</pre>";
?>
