<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('title')->nullable()->after('type');
            $table->text('message')->nullable()->after('title');
            $table->string('icon')->nullable()->after('message');
            $table->string('color')->default('blue')->after('icon');
            $table->string('link')->nullable()->after('color');
            $table->boolean('is_read')->default(false)->after('read_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_read']);
            $table->dropColumn(['user_id', 'title', 'message', 'icon', 'color', 'link', 'is_read']);
        });
    }
};
