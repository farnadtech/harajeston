<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, decimal, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('site_settings')->insert([
            // تنظیمات سپرده
            [
                'key' => 'deposit_type',
                'value' => 'percentage',
                'type' => 'string',
                'description' => 'نوع محاسبه سپرده: fixed (مبلغ ثابت) یا percentage (درصد)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'deposit_fixed_amount',
                'value' => '1000000',
                'type' => 'integer',
                'description' => 'مبلغ ثابت سپرده (تومان)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'deposit_percentage',
                'value' => '10',
                'type' => 'decimal',
                'description' => 'درصد سپرده از قیمت پایه',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // تنظیمات کمیسیون
            [
                'key' => 'commission_type',
                'value' => 'percentage',
                'type' => 'string',
                'description' => 'نوع محاسبه کمیسیون: fixed (مبلغ ثابت) یا percentage (درصد)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'commission_fixed_amount',
                'value' => '50000',
                'type' => 'integer',
                'description' => 'مبلغ ثابت کمیسیون (تومان)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'commission_percentage',
                'value' => '5',
                'type' => 'decimal',
                'description' => 'درصد کمیسیون از قیمت نهایی',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'commission_payer',
                'value' => 'buyer',
                'type' => 'string',
                'description' => 'پرداخت کننده کمیسیون: buyer (خریدار), seller (فروشنده), both (هر دو)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'commission_split_percentage',
                'value' => '50',
                'type' => 'decimal',
                'description' => 'درصد تقسیم کمیسیون بین خریدار و فروشنده (وقتی both انتخاب شده)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
