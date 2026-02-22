<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('show_before_start')->default(true)->after('auto_extend')
                ->comment('آیا حراجی قبل از شروع در لیست‌ها نمایش داده شود؟');
        });
    }

    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('show_before_start');
        });
    }
};
