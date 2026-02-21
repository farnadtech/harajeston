<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->text('comment');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['seller_id', 'status']);
            $table->index(['buyer_id', 'order_id']);
            $table->unique(['buyer_id', 'order_id']); // One review per order
        });
        
        // Add rating fields to users table (for sellers)
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('seller_rating', 3, 2)->default(0)->after('role');
            $table->unsignedInteger('seller_rating_count')->default(0)->after('seller_rating');
            
            $table->index('seller_rating');
        });
        
        // Update listing_comments to remove rating and only keep questions
        Schema::table('listing_comments', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
        
        // Remove rating from listings
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['average_rating']);
            $table->dropColumn(['average_rating', 'rating_count']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['seller_rating']);
            $table->dropColumn(['seller_rating', 'seller_rating_count']);
        });
        
        Schema::table('listing_comments', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('content');
        });
        
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->index('average_rating');
        });
        
        Schema::dropIfExists('seller_reviews');
    }
};
