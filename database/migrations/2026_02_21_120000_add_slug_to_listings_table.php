<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // اضافه کردن ستون slug به صورت nullable
        Schema::table('listings', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // ساخت slug برای آگهی‌های موجود
        $listings = DB::table('listings')->get();
        foreach ($listings as $listing) {
            $slug = \Str::slug($listing->title);
            $originalSlug = $slug;
            $counter = 1;
            
            // اگر slug تکراری بود، عدد اضافه کن
            while (DB::table('listings')->where('slug', $slug)->where('id', '!=', $listing->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('listings')->where('id', $listing->id)->update(['slug' => $slug]);
        }

        // حالا slug رو unique و not null می‌کنیم
        Schema::table('listings', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
