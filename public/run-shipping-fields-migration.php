<?php
// Run the shipping fields migration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running migration to add detailed shipping fields to orders table...\n";
echo "=================================================================\n\n";

try {
    // Run the specific migration
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_02_28_000000_add_detailed_shipping_fields_to_orders.php',
        '--force' => true
    ]);
    
    echo Artisan::output();
    echo "\n✓ Migration completed successfully!\n\n";
    
    // Check the table structure
    echo "Checking orders table structure:\n";
    $columns = DB::select("SHOW COLUMNS FROM orders");
    
    echo "\nShipping-related columns:\n";
    foreach ($columns as $column) {
        if (str_contains($column->Field, 'shipping')) {
            echo "- {$column->Field} ({$column->Type}) " . ($column->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
