<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // فقط اگر ستون وجود نداشته باشد اضافه می‌کنیم
            if (!Schema::hasColumn('listings', 'current_highest_bid')) {
                $table->decimal('current_highest_bid', 15, 0)->nullable()->after('required_deposit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'current_highest_bid')) {
                $table->dropColumn('current_highest_bid');
            }
        });
    }
};
