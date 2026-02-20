<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_action_logs', function (Blueprint $table) {
            $table->foreignId('listing_id')->nullable()->after('admin_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable()->after('action');
            $table->string('icon', 50)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('admin_action_logs', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropColumn(['listing_id', 'description', 'icon']);
        });
    }
};
