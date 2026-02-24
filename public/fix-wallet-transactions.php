<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "درحال بررسی جدول wallet_transactions...\n\n";

// Check if user_id column exists
$columns = DB::select("SHOW COLUMNS FROM wallet_transactions WHERE Field = 'user_id'");

if (!empty($columns)) {
    echo "ستون user_id وجود دارد. درحال حذف...\n";
    
    try {
        // Drop foreign key first if exists
        DB::statement("ALTER TABLE wallet_transactions DROP FOREIGN KEY IF EXISTS wallet_transactions_user_id_foreign");
        echo "✓ Foreign key حذف شد\n";
    } catch (\Exception $e) {
        echo "! Foreign key وجود نداشت\n";
    }
    
    try {
        // Drop the column
        DB::statement("ALTER TABLE wallet_transactions DROP COLUMN user_id");
        echo "✓ ستون user_id حذف شد\n";
    } catch (\Exception $e) {
        echo "✗ خطا در حذف ستون: " . $e->getMessage() . "\n";
    }
} else {
    echo "✓ ستون user_id وجود ندارد\n";
}

echo "\nبررسی ستون‌های مورد نیاز...\n";

$requiredColumns = [
    'wallet_id' => 'bigint(20) unsigned',
    'type' => 'enum',
    'amount' => 'decimal',
    'tax_amount' => 'decimal',
    'final_amount' => 'decimal',
    'gateway' => 'varchar',
    'transaction_id' => 'varchar',
    'reference_id' => 'varchar',
    'status' => 'varchar',
    'description' => 'text',
    'balance_before' => 'decimal',
    'balance_after' => 'decimal',
    'frozen_before' => 'decimal',
    'frozen_after' => 'decimal',
];

$existingColumns = DB::select("SHOW COLUMNS FROM wallet_transactions");
$existingColumnNames = array_column($existingColumns, 'Field');

foreach ($requiredColumns as $column => $type) {
    if (in_array($column, $existingColumnNames)) {
        echo "✓ {$column}\n";
    } else {
        echo "✗ {$column} - وجود ندارد\n";
    }
}

echo "\nتمام!\n";
