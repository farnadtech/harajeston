<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $count = DB::table('notifications')->count();
    DB::table('notifications')->truncate();
    
    echo "✓ تعداد {$count} نوتیفیکیشن پاک شد";
} catch (Exception $e) {
    echo "خطا: " . $e->getMessage();
}
