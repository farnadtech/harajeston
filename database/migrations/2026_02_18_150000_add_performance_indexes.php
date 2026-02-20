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
        // Add composite index for common queries on listings
        Schema::table('listings', function (Blueprint $table) {
            // For filtering active listings by type
            $table->index(['status', 'type'], 'idx_status_type');
            
            // For seller's listings queries
            $table->index(['seller_id', 'status'], 'idx_seller_status');
            
            // For auction ending queries
            $table->index(['status', 'end_time'], 'idx_status_end_time');
        });
        
        // Add composite index for bids queries
        Schema::table('bids', function (Blueprint $table) {
            // For getting user's bids on active listings
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
        
        // Add index for orders queries
        Schema::table('orders', function (Blueprint $table) {
            // For buyer's orders
            $table->index(['buyer_id', 'status'], 'idx_buyer_status_orders');
            
            // For seller's orders
            $table->index(['seller_id', 'status'], 'idx_seller_status_orders');
            
            // For date range queries
            $table->index('created_at', 'idx_orders_created_at');
        });
        
        // Add index for wallet transactions date queries
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // For transaction history with filters
            $table->index(['wallet_id', 'type', 'created_at'], 'idx_wallet_type_date');
        });
        
        // Add index for cart items
        Schema::table('cart_items', function (Blueprint $table) {
            // For cart queries
            $table->index(['cart_id', 'listing_id'], 'idx_cart_listing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('idx_status_type');
            $table->dropIndex('idx_seller_status');
            $table->dropIndex('idx_status_end_time');
        });
        
        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('idx_user_created');
        });
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_buyer_status_orders');
            $table->dropIndex('idx_seller_status_orders');
            $table->dropIndex('idx_orders_created_at');
        });
        
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_wallet_type_date');
        });
        
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('idx_cart_listing');
        });
    }
};
