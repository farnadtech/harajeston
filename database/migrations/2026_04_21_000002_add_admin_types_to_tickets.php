<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: تغییر enum برای اضافه کردن نوع‌های ادمین
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('buyer_to_seller','buyer_to_admin','seller_to_buyer','seller_to_admin','admin_to_buyer','admin_to_seller') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN type ENUM('buyer_to_seller','buyer_to_admin','seller_to_buyer','seller_to_admin') NOT NULL");
    }
};
