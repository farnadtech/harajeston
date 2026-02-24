<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('pending_store_name')->nullable()->after('store_name');
            $table->timestamp('store_name_change_requested_at')->nullable()->after('pending_store_name');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['pending_store_name', 'store_name_change_requested_at']);
        });
    }
};
