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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Foreign key to users table - each seller has one store');
            $table->string('store_name', 255)->comment('Display name of the store');
            $table->string('slug', 255)->unique()->comment('Unique URL-friendly identifier for storefront');
            $table->text('description')->nullable()->comment('Store description (max 1000 characters)');
            $table->string('banner_image', 500)->nullable()->comment('Path to banner image (1920x400px recommended)');
            $table->string('logo_image', 500)->nullable()->comment('Path to logo image (300x300px recommended)');
            $table->boolean('is_active')->default(true)->comment('Whether the store is active and visible');
            $table->timestamps();
            
            // Indexes
            $table->index('slug');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
