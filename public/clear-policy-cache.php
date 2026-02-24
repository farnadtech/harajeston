<?php

// Clear Laravel caches
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear',
];

echo "<h2>Clearing Laravel Caches...</h2>";

foreach ($commands as $command) {
    echo "<p>Running: <code>$command</code></p>";
    $output = shell_exec("cd .. && $command 2>&1");
    echo "<pre>$output</pre>";
}

echo "<h3 style='color: green;'>✓ All caches cleared!</h3>";
echo "<p><a href='/haraj/public'>بازگشت به صفحه اصلی</a></p>";
