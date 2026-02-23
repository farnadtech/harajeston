<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1>Running Migration...</h1>";
echo "<pre>";

try {
    $status = $kernel->call('migrate', [
        '--force' => true,
    ]);
    
    echo "\n✓ Migration completed successfully!\n";
    echo "Status code: " . $status . "\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo '<a href="/haraj/public/login">Go to Login</a>';
?>
