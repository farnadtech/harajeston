<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تنظیمات ملی پیامک
        Schema::create('melipayamak_settings', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('from_number')->nullable(); // شماره فرستنده (خودکار از API گرفته می‌شود)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // OTP codes table
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 15);
            $table->string('code', 10);
            $table->string('purpose')->default('login'); // login, register
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->index(['phone', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('melipayamak_settings');
    }
};
