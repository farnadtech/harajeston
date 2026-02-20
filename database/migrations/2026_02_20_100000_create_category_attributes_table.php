<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول ویژگی‌های دسته‌بندی
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // نام ویژگی (مثل "رم")
            $table->string('type')->default('select'); // select, text, number
            $table->json('options')->nullable(); // گزینه‌ها برای select (مثل ["4GB", "8GB", "16GB"])
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(true); // آیا در فیلتر نمایش داده شود
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // جدول مقادیر ویژگی‌ها برای حراجی‌ها
        Schema::create('listing_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_attribute_id')->constrained()->onDelete('cascade');
            $table->text('value'); // مقدار ویژگی
            $table->timestamps();
            
            $table->unique(['listing_id', 'category_attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_attribute_values');
        Schema::dropIfExists('category_attributes');
    }
};
