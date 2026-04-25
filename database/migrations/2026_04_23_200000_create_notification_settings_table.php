<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique(); // e.g. order_placed_buyer
            $table->string('event_label');         // نام نمایشی
            $table->string('recipient');           // buyer | seller | both
            $table->boolean('via_database')->default(true);
            $table->boolean('via_sms')->default(false);
            $table->boolean('via_email')->default(false);
            $table->string('sms_pattern_id')->nullable(); // bodyId ملی پیامک
            $table->text('sms_template')->nullable();     // متن پیامک (اگر pattern نداشت)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
