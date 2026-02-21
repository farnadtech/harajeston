<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // اضافه کردن دسته‌های سطح سوم
        $thirdLevelCategories = [
            // زیرمجموعه موبایل و تبلت
            ['name' => 'موبایل اپل', 'slug' => 'apple-mobile', 'icon' => 'phone_iphone', 'parent_id' => 2, 'order' => 1],
            ['name' => 'موبایل سامسونگ', 'slug' => 'samsung-mobile', 'icon' => 'smartphone', 'parent_id' => 2, 'order' => 2],
            ['name' => 'موبایل شیائومی', 'slug' => 'xiaomi-mobile', 'icon' => 'smartphone', 'parent_id' => 2, 'order' => 3],
            ['name' => 'تبلت', 'slug' => 'tablet', 'icon' => 'tablet', 'parent_id' => 2, 'order' => 4],
            
            // زیرمجموعه لپ‌تاپ و کامپیوتر
            ['name' => 'لپ‌تاپ', 'slug' => 'laptop', 'icon' => 'laptop', 'parent_id' => 3, 'order' => 1],
            ['name' => 'کامپیوتر دسکتاپ', 'slug' => 'desktop', 'icon' => 'desktop_windows', 'parent_id' => 3, 'order' => 2],
            ['name' => 'مانیتور', 'slug' => 'monitor', 'icon' => 'monitor', 'parent_id' => 3, 'order' => 3],
            ['name' => 'قطعات کامپیوتر', 'slug' => 'computer-parts', 'icon' => 'memory', 'parent_id' => 3, 'order' => 4],
            
            // زیرمجموعه لوازم جانبی
            ['name' => 'هدفون و هندزفری', 'slug' => 'headphones', 'icon' => 'headphones', 'parent_id' => 4, 'order' => 1],
            ['name' => 'کیبورد و موس', 'slug' => 'keyboard-mouse', 'icon' => 'keyboard', 'parent_id' => 4, 'order' => 2],
            ['name' => 'شارژر و کابل', 'slug' => 'charger-cable', 'icon' => 'cable', 'parent_id' => 4, 'order' => 3],
            ['name' => 'پاوربانک', 'slug' => 'powerbank', 'icon' => 'battery_charging_full', 'parent_id' => 4, 'order' => 4],
            
            // زیرمجموعه دوربین و عکاسی
            ['name' => 'دوربین دیجیتال', 'slug' => 'digital-camera', 'icon' => 'photo_camera', 'parent_id' => 5, 'order' => 1],
            ['name' => 'دوربین فیلمبرداری', 'slug' => 'video-camera', 'icon' => 'videocam', 'parent_id' => 5, 'order' => 2],
            ['name' => 'لنز و تجهیزات', 'slug' => 'lens-equipment', 'icon' => 'camera_enhance', 'parent_id' => 5, 'order' => 3],
            
            // زیرمجموعه کنسول و بازی
            ['name' => 'پلی‌استیشن', 'slug' => 'playstation', 'icon' => 'sports_esports', 'parent_id' => 6, 'order' => 1],
            ['name' => 'ایکس‌باکس', 'slug' => 'xbox', 'icon' => 'sports_esports', 'parent_id' => 6, 'order' => 2],
            ['name' => 'بازی‌های کامپیوتری', 'slug' => 'pc-games', 'icon' => 'videogame_asset', 'parent_id' => 6, 'order' => 3],
            
            // زیرمجموعه لوازم برقی آشپزخانه
            ['name' => 'یخچال و فریزر', 'slug' => 'refrigerator', 'icon' => 'kitchen', 'parent_id' => 8, 'order' => 1],
            ['name' => 'ماشین لباسشویی', 'slug' => 'washing-machine', 'icon' => 'local_laundry_service', 'parent_id' => 8, 'order' => 2],
            ['name' => 'ماکروویو و توستر', 'slug' => 'microwave-toaster', 'icon' => 'microwave', 'parent_id' => 8, 'order' => 3],
            ['name' => 'جاروبرقی', 'slug' => 'vacuum-cleaner', 'icon' => 'cleaning_services', 'parent_id' => 8, 'order' => 4],
            
            // زیرمجموعه مبلمان و دکوراسیون
            ['name' => 'مبل و کاناپه', 'slug' => 'sofa', 'icon' => 'weekend', 'parent_id' => 9, 'order' => 1],
            ['name' => 'میز و صندلی', 'slug' => 'table-chair', 'icon' => 'chair', 'parent_id' => 9, 'order' => 2],
            ['name' => 'فرش و موکت', 'slug' => 'carpet', 'icon' => 'texture', 'parent_id' => 9, 'order' => 3],
            ['name' => 'لوستر و چراغ', 'slug' => 'lighting', 'icon' => 'lightbulb', 'parent_id' => 9, 'order' => 4],
        ];

        foreach ($thirdLevelCategories as $category) {
            DB::table('categories')->insert([
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
        // حذف دسته‌های سطح سوم
        $slugs = [
            'apple-mobile', 'samsung-mobile', 'xiaomi-mobile', 'tablet',
            'laptop', 'desktop', 'monitor', 'computer-parts',
            'headphones', 'keyboard-mouse', 'charger-cable', 'powerbank',
            'digital-camera', 'video-camera', 'lens-equipment',
            'playstation', 'xbox', 'pc-games',
            'refrigerator', 'washing-machine', 'microwave-toaster', 'vacuum-cleaner',
            'sofa', 'table-chair', 'carpet', 'lighting',
        ];

        DB::table('categories')->whereIn('slug', $slugs)->delete();
    }
};
