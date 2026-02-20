<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // ویژگی‌های دسته "موبایل و تبلت"
        $mobileCategory = Category::where('slug', 'mobile-tablet')->first();
        if ($mobileCategory) {
            CategoryAttribute::create([
                'category_id' => $mobileCategory->id,
                'name' => 'رم',
                'type' => 'select',
                'options' => ['4GB', '6GB', '8GB', '12GB', '16GB', '32GB'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 1,
            ]);

            CategoryAttribute::create([
                'category_id' => $mobileCategory->id,
                'name' => 'حافظه داخلی',
                'type' => 'select',
                'options' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 2,
            ]);

            CategoryAttribute::create([
                'category_id' => $mobileCategory->id,
                'name' => 'رنگ',
                'type' => 'select',
                'options' => ['مشکی', 'سفید', 'آبی', 'قرمز', 'طلایی', 'نقره‌ای', 'سبز'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 3,
            ]);

            CategoryAttribute::create([
                'category_id' => $mobileCategory->id,
                'name' => 'برند',
                'type' => 'select',
                'options' => ['سامسونگ', 'اپل', 'شیائومی', 'هوآوی', 'نوکیا', 'ال‌جی'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 4,
            ]);
        }

        // ویژگی‌های دسته "لپ‌تاپ و کامپیوتر"
        $laptopCategory = Category::where('slug', 'laptop-computer')->first();
        if ($laptopCategory) {
            CategoryAttribute::create([
                'category_id' => $laptopCategory->id,
                'name' => 'پردازنده',
                'type' => 'select',
                'options' => ['Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 1,
            ]);

            CategoryAttribute::create([
                'category_id' => $laptopCategory->id,
                'name' => 'رم',
                'type' => 'select',
                'options' => ['4GB', '8GB', '16GB', '32GB', '64GB'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 2,
            ]);

            CategoryAttribute::create([
                'category_id' => $laptopCategory->id,
                'name' => 'نوع هارد',
                'type' => 'select',
                'options' => ['HDD', 'SSD', 'HDD + SSD'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 3,
            ]);

            CategoryAttribute::create([
                'category_id' => $laptopCategory->id,
                'name' => 'سایز صفحه نمایش',
                'type' => 'select',
                'options' => ['13 اینچ', '14 اینچ', '15.6 اینچ', '17 اینچ'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 4,
            ]);
        }

        // ویژگی‌های دسته "لوازم جانبی"
        $accessoriesCategory = Category::where('slug', 'accessories')->first();
        if ($accessoriesCategory) {
            CategoryAttribute::create([
                'category_id' => $accessoriesCategory->id,
                'name' => 'نوع محصول',
                'type' => 'select',
                'options' => ['کابل', 'شارژر', 'هندزفری', 'پاوربانک', 'کیف', 'محافظ صفحه', 'قاب'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 1,
            ]);

            CategoryAttribute::create([
                'category_id' => $accessoriesCategory->id,
                'name' => 'برند',
                'type' => 'select',
                'options' => ['اورجینال', 'انکر', 'بیسوس', 'سامسونگ', 'اپل', 'شیائومی'],
                'is_required' => false,
                'is_filterable' => true,
                'order' => 2,
            ]);
        }
    }
}
