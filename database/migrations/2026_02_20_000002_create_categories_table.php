<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // آیکون Material Icons
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['parent_id', 'is_active']);
            $table->index('slug');
        });

        // درج دسته‌بندی‌های اصلی
        $categories = [
            // دیجیتال و الکترونیک
            ['id' => 1, 'name' => 'دیجیتال و الکترونیک', 'slug' => 'digital-electronics', 'icon' => 'devices', 'parent_id' => null, 'order' => 1],
            ['id' => 2, 'name' => 'موبایل و تبلت', 'slug' => 'mobile-tablet', 'icon' => 'smartphone', 'parent_id' => 1, 'order' => 1],
            ['id' => 3, 'name' => 'لپ‌تاپ و کامپیوتر', 'slug' => 'laptop-computer', 'icon' => 'computer', 'parent_id' => 1, 'order' => 2],
            ['id' => 4, 'name' => 'لوازم جانبی', 'slug' => 'accessories', 'icon' => 'headphones', 'parent_id' => 1, 'order' => 3],
            ['id' => 5, 'name' => 'دوربین و عکاسی', 'slug' => 'camera-photography', 'icon' => 'photo_camera', 'parent_id' => 1, 'order' => 4],
            ['id' => 6, 'name' => 'کنسول و بازی', 'slug' => 'gaming', 'icon' => 'sports_esports', 'parent_id' => 1, 'order' => 5],
            
            // خانه و آشپزخانه
            ['id' => 7, 'name' => 'خانه و آشپزخانه', 'slug' => 'home-kitchen', 'icon' => 'home', 'parent_id' => null, 'order' => 2],
            ['id' => 8, 'name' => 'لوازم برقی آشپزخانه', 'slug' => 'kitchen-appliances', 'icon' => 'kitchen', 'parent_id' => 7, 'order' => 1],
            ['id' => 9, 'name' => 'مبلمان و دکوراسیون', 'slug' => 'furniture-decor', 'icon' => 'chair', 'parent_id' => 7, 'order' => 2],
            ['id' => 10, 'name' => 'ابزار و تجهیزات', 'slug' => 'tools-equipment', 'icon' => 'construction', 'parent_id' => 7, 'order' => 3],
            
            // مد و پوشاک
            ['id' => 11, 'name' => 'مد و پوشاک', 'slug' => 'fashion-clothing', 'icon' => 'checkroom', 'parent_id' => null, 'order' => 3],
            ['id' => 12, 'name' => 'پوشاک مردانه', 'slug' => 'mens-clothing', 'icon' => 'man', 'parent_id' => 11, 'order' => 1],
            ['id' => 13, 'name' => 'پوشاک زنانه', 'slug' => 'womens-clothing', 'icon' => 'woman', 'parent_id' => 11, 'order' => 2],
            ['id' => 14, 'name' => 'کیف و کفش', 'slug' => 'bags-shoes', 'icon' => 'shopping_bag', 'parent_id' => 11, 'order' => 3],
            ['id' => 15, 'name' => 'ساعت و زیورآلات', 'slug' => 'watches-jewelry', 'icon' => 'watch', 'parent_id' => 11, 'order' => 4],
            
            // ورزش و سرگرمی
            ['id' => 16, 'name' => 'ورزش و سرگرمی', 'slug' => 'sports-entertainment', 'icon' => 'sports_soccer', 'parent_id' => null, 'order' => 4],
            ['id' => 17, 'name' => 'لوازم ورزشی', 'slug' => 'sports-equipment', 'icon' => 'fitness_center', 'parent_id' => 16, 'order' => 1],
            ['id' => 18, 'name' => 'کتاب و مجله', 'slug' => 'books-magazines', 'icon' => 'menu_book', 'parent_id' => 16, 'order' => 2],
            ['id' => 19, 'name' => 'موسیقی و سرگرمی', 'slug' => 'music-entertainment', 'icon' => 'music_note', 'parent_id' => 16, 'order' => 3],
            
            // خودرو و وسایل نقلیه
            ['id' => 20, 'name' => 'خودرو و وسایل نقلیه', 'slug' => 'vehicles', 'icon' => 'directions_car', 'parent_id' => null, 'order' => 5],
            ['id' => 21, 'name' => 'لوازم جانبی خودرو', 'slug' => 'car-accessories', 'icon' => 'car_repair', 'parent_id' => 20, 'order' => 1],
            ['id' => 22, 'name' => 'موتورسیکلت', 'slug' => 'motorcycle', 'icon' => 'two_wheeler', 'parent_id' => 20, 'order' => 2],
            
            // زیبایی و سلامت
            ['id' => 23, 'name' => 'زیبایی و سلامت', 'slug' => 'beauty-health', 'icon' => 'spa', 'parent_id' => null, 'order' => 6],
            ['id' => 24, 'name' => 'آرایشی و بهداشتی', 'slug' => 'cosmetics-hygiene', 'icon' => 'face', 'parent_id' => 23, 'order' => 1],
            ['id' => 25, 'name' => 'عطر و ادکلن', 'slug' => 'perfume', 'icon' => 'local_florist', 'parent_id' => 23, 'order' => 2],
            
            // کودک و نوزاد
            ['id' => 26, 'name' => 'کودک و نوزاد', 'slug' => 'baby-kids', 'icon' => 'child_care', 'parent_id' => null, 'order' => 7],
            ['id' => 27, 'name' => 'لوازم نوزاد', 'slug' => 'baby-products', 'icon' => 'baby_changing_station', 'parent_id' => 26, 'order' => 1],
            ['id' => 28, 'name' => 'اسباب بازی', 'slug' => 'toys', 'icon' => 'toys', 'parent_id' => 26, 'order' => 2],
            
            // هنر و صنایع دستی
            ['id' => 29, 'name' => 'هنر و صنایع دستی', 'slug' => 'art-handicrafts', 'icon' => 'palette', 'parent_id' => null, 'order' => 8],
            ['id' => 30, 'name' => 'تابلو و نقاشی', 'slug' => 'paintings', 'icon' => 'brush', 'parent_id' => 29, 'order' => 1],
            ['id' => 31, 'name' => 'صنایع دستی', 'slug' => 'handicrafts', 'icon' => 'handyman', 'parent_id' => 29, 'order' => 2],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug'],
                'icon' => $category['icon'],
                'parent_id' => $category['parent_id'],
                'order' => $category['order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
