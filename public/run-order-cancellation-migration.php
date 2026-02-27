<?php
// Run the order cancellation migration
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>Running Order Cancellation Migration</h2>";
echo "<pre>";

try {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_02_27_000000_add_order_cancellation_types.php',
        '--force' => true
    ]);
    echo Artisan::output();
    echo "\n✓ Migration completed successfully!\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";

// Check the enum values
echo "<h3>Current ENUM values for type column:</h3>";
echo "<pre>";
$result = DB::select("SHOW COLUMNS FROM wallet_transactions WHERE Field = 'type'");
print_r($result);
echo "</pre>";
