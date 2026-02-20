<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // مرحله 1: تغییر نام فیلدها با CHANGE (سازگار با MariaDB)
        DB::statement('ALTER TABLE listings CHANGE base_price starting_price DECIMAL(15, 2)');
        DB::statement('ALTER TABLE listings CHANGE current_highest_bid current_price DECIMAL(15, 2)');
        DB::statement('ALTER TABLE listings CHANGE start_time starts_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE listings CHANGE end_time ends_at TIMESTAMP NULL');

        Schema::table('listings', function (Blueprint $table) {
            // مرحله 2: اضافه کردن قیمت خرید فوری
            $table->decimal('buy_now_price', 15, 2)->nullable()->after('current_price')
                ->comment('قیمت خرید فوری - اگر خریدار این مبلغ را بپردازد، بلافاصله برنده می‌شود');
        });

        Schema::table('listings', function (Blueprint $table) {
            // مرحله 3: حذف فیلدهای غیرضروری
            $table->dropColumn(['type', 'price', 'stock', 'low_stock_threshold']);
        });

        // مرحله 4: به‌روزرسانی status enum
        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending', 'active', 'ended', 'completed', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // بازگردانی فیلدها
            $table->enum('type', ['auction', 'direct_sale', 'hybrid'])->default('auction');
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->integer('low_stock_threshold')->nullable();
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('buy_now_price');
        });

        // بازگردانی نام فیلدها
        DB::statement('ALTER TABLE listings CHANGE starting_price base_price DECIMAL(15, 2)');
        DB::statement('ALTER TABLE listings CHANGE current_price current_highest_bid DECIMAL(15, 2)');
        DB::statement('ALTER TABLE listings CHANGE starts_at start_time TIMESTAMP NULL');
        DB::statement('ALTER TABLE listings CHANGE ends_at end_time TIMESTAMP NULL');

        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock') DEFAULT 'pending'");
    }
};
