<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // به‌روزرسانی لینک‌های نوتیفیکیشن برای کاربران غیر ادمین
    $updated = DB::update("
        UPDATE notifications n
        INNER JOIN users u ON n.user_id = u.id
        SET n.link = REPLACE(n.link, '/admin/listings/', '/listings/')
        WHERE u.role != 'admin' 
        AND n.link LIKE '%/admin/listings/%'
    ");

    echo "✓ تعداد {$updated} نوتیفیکیشن به‌روز شد";
} catch (Exception $e) {
    echo "خطا: " . $e->getMessage();
}
