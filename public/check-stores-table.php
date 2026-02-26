<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== ستون‌های جدول stores ===\n\n";

$columns = Schema::getColumnListing('stores');

echo "ستون‌های موجود:\n";
foreach ($columns as $column) {
    $type = DB::select("SHOW COLUMNS FROM stores WHERE Field = ?", [$column])[0]->Type ?? 'unknown';
    echo "  - {$column} ({$type})\n";
}

echo "\n=== بررسی فیلدهای مورد نیاز ===\n\n";

$requiredFields = ['address', 'phone', 'email', 'description'];

foreach ($requiredFields as $field) {
    $exists = in_array($field, $columns);
    echo "{$field}: " . ($exists ? '✓ وجود دارد' : '✗ وجود ندارد') . "\n";
}

echo "\n=== نمونه داده از stores ===\n\n";

$store = DB::table('stores')->where('slug', 'froshgah-frzad')->first();

if ($store) {
    echo "ID: {$store->id}\n";
    echo "User ID: {$store->user_id}\n";
    echo "Store Name: " . ($store->store_name ?? 'null') . "\n";
    echo "Description: " . ($store->description ?? 'null') . "\n";
    echo "Address: " . (isset($store->address) ? ($store->address ?? 'null') : 'فیلد وجود ندارد') . "\n";
    echo "Phone: " . (isset($store->phone) ? ($store->phone ?? 'null') : 'فیلد وجود ندارد') . "\n";
    echo "Email: " . (isset($store->email) ? ($store->email ?? 'null') : 'فیلد وجود ندارد') . "\n";
}
