<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // اگر دسته‌بندی وجود داره، اجرا نکن
        if (Category::count() > 0) {
            $this->command->info('دسته‌بندی‌ها قبلا ایجاد شده‌اند.');
            return;
        }

        $categories = [
            // دیجیتال و الکترونیک
            [
                'name' => 'دیجیتال و الکترونیک',
                'slug' => 'digital-electronics',
                'icon' => 'devices',
                'children' => [
                    ['name' => 'موبایل و تبلت', 'slug' => 'mobile-tablet', 'icon' => 'smartphone'],
                    ['name' => 'لپ‌تاپ و کامپیوتر', 'slug' => 'laptop-computer', 'icon' => 'computer'],
                    ['name' => 'لوازم جانبی', 'slug' => 'accessories', 'icon' => 'headphones'],
                    ['name' => 'دوربین و عکاسی', 'slug' => 'camera-photography', 'icon' => 'photo_camera'],
                    ['name' => 'کنسول و بازی', 'slug' => 'gaming', 'icon' => 'sports_esports'],
                ]
            ],
            
            // خانه و آشپزخانه
            [
                'name' => 'خانه و آشپزخانه',
                'slug' => 'home-kitchen',
                'icon' => 'home',
                'children' => [
                    ['name' => 'لوازم برقی آشپزخانه', 'slug' => 'kitchen-appliances', 'icon' => 'kitchen'],
                    ['name' => 'مبلمان و دکوراسیون', 'slug' => 'furniture-decor', 'icon' => 'chair'],
                    ['name' => 'ابزار و تجهیزات', 'slug' => 'tools-equipment', 'icon' => 'construction'],
                ]
            ],
            
            // مد و پوشاک
            [
                'name' => 'مد و پوشاک',
                'slug' => 'fashion-clothing',
                'icon' => 'checkroom',
                'children' => [
                    ['name' => 'پوشاک مردانه', 'slug' => 'mens-clothing', 'icon' => 'man'],
                    ['name' => 'پوشاک زنانه', 'slug' => 'womens-clothing', 'icon' => 'woman'],
                    ['name' => 'کیف و کفش', 'slug' => 'bags-shoes', 'icon' => 'shopping_bag'],
                    ['name' => 'ساعت و زیورآلات', 'slug' => 'watches-jewelry', 'icon' => 'watch'],
                ]
            ],
            
            // ورزش و سرگرمی
            [
                'name' => 'ورزش و سرگرمی',
                'slug' => 'sports-entertainment',
                'icon' => 'sports_soccer',
                'children' => [
                    ['name' => 'لوازم ورزشی', 'slug' => 'sports-equipment', 'icon' => 'fitness_center'],
                    ['name' => 'کتاب و مجله', 'slug' => 'books-magazines', 'icon' => 'menu_book'],
                    ['name' => 'موسیقی و سرگرمی', 'slug' => 'music-entertainment', 'icon' => 'music_note'],
                ]
            ],
            
            // خودرو و وسایل نقلیه
            [
                'name' => 'خودرو و وسایل نقلیه',
                'slug' => 'vehicles',
                'icon' => 'directions_car',
                'children' => [
                    ['name' => 'لوازم جانبی خودرو', 'slug' => 'car-accessories', 'icon' => 'car_repair'],
                    ['name' => 'موتورسیکلت', 'slug' => 'motorcycle', 'icon' => 'two_wheeler'],
                ]
            ],
            
            // زیبایی و سلامت
            [
                'name' => 'زیبایی و سلامت',
                'slug' => 'beauty-health',
                'icon' => 'spa',
                'children' => [
                    ['name' => 'آرایشی و بهداشتی', 'slug' => 'cosmetics-hygiene', 'icon' => 'face'],
                    ['name' => 'عطر و ادکلن', 'slug' => 'perfume', 'icon' => 'local_florist'],
                ]
            ],
            
            // کودک و نوزاد
            [
                'name' => 'کودک و نوزاد',
                'slug' => 'baby-kids',
                'icon' => 'child_care',
                'children' => [
                    ['name' => 'لوازم نوزاد', 'slug' => 'baby-products', 'icon' => 'baby_changing_station'],
                    ['name' => 'اسباب بازی', 'slug' => 'toys', 'icon' => 'toys'],
                ]
            ],
            
            // هنر و صنایع دستی
            [
                'name' => 'هنر و صنایع دستی',
                'slug' => 'art-handicrafts',
                'icon' => 'palette',
                'children' => [
                    ['name' => 'تابلو و نقاشی', 'slug' => 'paintings', 'icon' => 'brush'],
                    ['name' => 'صنایع دستی', 'slug' => 'handicrafts', 'icon' => 'handyman'],
                ]
            ],
        ];

        $order = 1;
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $parent = Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'icon' => $categoryData['icon'],
                'order' => $order++,
                'is_active' => true,
            ]);

            $childOrder = 1;
            foreach ($children as $childData) {
                Category::create([
                    'name' => $childData['name'],
                    'slug' => $childData['slug'],
                    'icon' => $childData['icon'],
                    'parent_id' => $parent->id,
                    'order' => $childOrder++,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('دسته‌بندی‌ها با موفقیت ایجاد شدند.');
    }
}
