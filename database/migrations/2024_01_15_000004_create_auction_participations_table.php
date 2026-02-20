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
        Schema::create('auction_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('deposit_amount', 15, 2);
            $table->enum('deposit_status', ['paid', 'frozen', 'released', 'forfeited', 'applied'])->default('paid');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate participation
            $table->unique(['listing_id', 'user_id'], 'unique_participation');
            
            // Indexes
            $table->index(['listing_id', 'user_id'], 'idx_listing_user');
            $table->index('deposit_status', 'idx_deposit_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_participations');
    }
};
