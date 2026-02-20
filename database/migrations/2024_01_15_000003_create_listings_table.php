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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['auction', 'direct_sale', 'hybrid'])->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable()->index();
            
            // Auction-specific fields (nullable for direct_sale items)
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('required_deposit', 15, 2)->nullable();
            $table->decimal('current_highest_bid', 15, 2)->nullable();
            $table->foreignId('highest_bidder_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('current_winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('start_time')->nullable()->index();
            $table->timestamp('end_time')->nullable()->index();
            $table->timestamp('finalization_deadline')->nullable()->index();
            
            // Direct sale specific fields (nullable for auction items)
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->integer('low_stock_threshold')->nullable();
            
            $table->enum('status', [
                'pending',
                'active',
                'ended',
                'completed',
                'failed',
                'out_of_stock'
            ])->default('pending')->index();
            
            $table->timestamps();
            
            // Additional indexes for performance (seller_id already indexed by foreignId)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
