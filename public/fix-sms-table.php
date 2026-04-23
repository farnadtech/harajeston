<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "<pre style='font-family:monospace;font-size:14px;padding:20px'>";

// 1. حذف رکورد migration قدیمی تا دوباره اجرا بشه
$migrationFile = '2026_04_20_000001_create_sms_gateways_table';
$deleted = DB::table('migrations')->where('migration', $migrationFile)->delete();
echo $deleted ? "✓ رکورد migration قدیمی حذف شد\n" : "○ رکورد migration وجود نداشت\n";

// 2. حذف جدول sms_gateways اگه وجود داره
if (Schema::hasTable('sms_gateways')) {
    Schema::drop('sms_gateways');
    echo "✓ جدول sms_gateways قدیمی حذف شد\n";
}

// 3. ساخت جدول melipayamak_settings
if (!Schema::hasTable('melipayamak_settings')) {
    Schema::create('melipayamak_settings', function (Blueprint $table) {
        $table->id();
        $table->string('username');
        $table->string('password');
        $table->string('from_number')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    echo "✓ جدول melipayamak_settings ساخته شد\n";
} else {
    echo "○ جدول melipayamak_settings از قبل وجود دارد\n";
}

// 4. ساخت جدول otp_codes
if (!Schema::hasTable('otp_codes')) {
    Schema::create('otp_codes', function (Blueprint $table) {
        $table->id();
        $table->string('phone', 15);
        $table->string('code', 10);
        $table->string('purpose')->default('login');
        $table->timestamp('expires_at');
        $table->boolean('used')->default(false);
        $table->timestamps();
        $table->index(['phone', 'purpose']);
    });
    echo "✓ جدول otp_codes ساخته شد\n";
} else {
    echo "○ جدول otp_codes از قبل وجود دارد\n";
}

// 5. ثبت migration در جدول migrations
DB::table('migrations')->insert([
    'migration' => $migrationFile,
    'batch'     => DB::table('migrations')->max('batch') + 1,
]);
echo "✓ migration در جدول migrations ثبت شد\n";

echo "\n✅ همه چیز آماده است!\n";
echo "</pre>";
echo '<p><a href="/haraj/public/admin/sms-gateways">رفتن به تنظیمات ملی پیامک</a></p>';
?>
