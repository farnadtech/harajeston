<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // حذف ستون category قدیمی (string)
            if (Schema::hasColumn('listings', 'category')) {
                $table->dropColumn('category');
            }
            
            // اضافه کردن category_id جدید (foreign key)
            $table->foreignId('category_id')->nullable()->after('description')->constrained('categories')->onDelete('set null');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            // بازگرداندن ستون قدیمی
            $table->string('category')->nullable();
        });
    }
};
