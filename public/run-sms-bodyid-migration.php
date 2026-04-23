<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<pre style='font-size:13px;padding:15px'>";

if (!Schema::hasColumn('melipayamak_settings', 'body_id')) {
    Schema::table('melipayamak_settings', function (Blueprint $table) {
        $table->string('body_id')->nullable()->after('from_number');
    });
    echo "✓ ستون body_id اضافه شد\n";
} else {
    echo "○ ستون body_id از قبل وجود دارد\n";
}

echo "✅ آماده است!\n";
echo "</pre>";
echo '<p><a href="/haraj/public/admin/sms-gateways">رفتن به تنظیمات</a></p>';
?>
