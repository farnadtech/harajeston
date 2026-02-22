<?php
echo "<h1>Apache Module Test</h1>";

// Check if mod_rewrite is loaded
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color:green;'>✓ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p style='color:red;'>✗ mod_rewrite is DISABLED</p>";
        echo "<p>You need to enable mod_rewrite in Apache configuration</p>";
    }
    
    echo "<h2>All Loaded Modules:</h2>";
    echo "<ul>";
    foreach ($modules as $module) {
        echo "<li>$module</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange;'>⚠ Cannot detect Apache modules (might be running on different server)</p>";
}

echo "<h2>Server Info:</h2>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

echo "<h2>.htaccess Test:</h2>";
if (file_exists(__DIR__ . '/.htaccess')) {
    echo "<p style='color:green;'>✓ .htaccess file exists</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/.htaccess')) . "</pre>";
} else {
    echo "<p style='color:red;'>✗ .htaccess file NOT found</p>";
}
?>
