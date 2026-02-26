<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "=== اجرای Migration ===\n\n";

try {
    Artisan::call('migrate', ['--path' => 'database/migrations/2026_02_26_100000_add_contact_fields_to_stores_table.php']);
    echo Artisan::output();
    echo "\n✓ Migration با موفقیت اجرا شد!\n";
} catch (Exception $e) {
    echo "✗ خطا: " . $e->getMessage() . "\n";
}
