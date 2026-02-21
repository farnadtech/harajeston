<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_comments', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('content')->comment('1-5 stars, only for comments');
            
            $table->index(['listing_id', 'rating']);
        });
        
        // Add rating fields to listings table
        Schema::table('listings', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 2)->default(0)->after('shares');
            $table->unsignedInteger('rating_count')->default(0)->after('average_rating');
            
            $table->index('average_rating');
        });
    }

    public function down(): void
    {
        Schema::table('listing_comments', function (Blueprint $table) {
            $table->dropIndex(['listing_id', 'rating']);
            $table->dropColumn('rating');
        });
        
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['average_rating']);
            $table->dropColumn(['average_rating', 'rating_count']);
        });
    }
};
