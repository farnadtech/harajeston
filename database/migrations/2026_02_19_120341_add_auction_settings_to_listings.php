<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'reserve_price')) {
                $table->decimal('reserve_price', 15, 2)->nullable()->after('starting_price')
                    ->comment('قیمت رزرو - حداقل قیمتی که فروشنده حاضر به فروش است');
            }
            
            if (!Schema::hasColumn('listings', 'bid_increment')) {
                $table->decimal('bid_increment', 15, 2)->default(0)->after('reserve_price')
                    ->comment('گام افزایش پیشنهاد - حداقل مبلغ افزایش در هر پیشنهاد');
            }
            
            if (!Schema::hasColumn('listings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 15, 2)->default(0)->after('required_deposit')
                    ->comment('مبلغ سپرده برای شرکت در حراج');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['reserve_price', 'bid_increment', 'deposit_amount']);
        });
    }
};
