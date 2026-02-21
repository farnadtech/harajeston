<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Listing, Category, User, ShippingMethod};
use Carbon\Carbon;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        // دریافت فروشندگان (کاربرانی که seller_status فعال دارند)
        $sellers = User::where('seller_status', 'active')->get();
        
        if ($sellers->isEmpty()) {
            $this->command->error('هیچ فروشنده‌ای وجود ندارد! ابتدا QuickSeeder را اجرا کنید.');
            return;
        }

        // دریافت دسته‌بندی‌های سطح آخر (که فرزند ندارند)
        $categories = Category::whereDoesntHave('children')->get();
        
        if ($categories->isEmpty()) {
            $this->command->error('هیچ دسته‌بندی‌ای وجود ندارد!');
            return;
        }

        // ایجاد روش‌های ارسال
        $this->createShippingMethods();

        // آگهی‌های نمونه
        $listings = [
            [
                'title' => 'گوشی سامسونگ گلکسی S23 Ultra',
                'description' => 'گوشی در حالت عالی، 256 گیگابایت حافظه، رنگ مشکی، با جعبه و لوازم جانبی کامل',
                'starting_price' => 35000000,
                'buy_now_price' => 40000000,
            ],
            [
                'title' => 'لپ‌تاپ ایسوس ROG Strix G15',
                'description' => 'لپ‌تاپ گیمینگ، پردازنده i7، 16GB RAM، RTX 3060، حالت نو',
                'starting_price' => 45000000,
                'buy_now_price' => 50000000,
            ],
            [
                'title' => 'آیفون 14 Pro Max',
                'description' => '256 گیگابایت، رنگ بنفش، بدون خط و خش، با گارانتی',
                'starting_price' => 48000000,
                'buy_now_price' => 52000000,
            ],
            [
                'title' => 'تبلت آیپد پرو 12.9 اینچ',
                'description' => 'نسل پنجم، 256GB، با قلم اپل و کیبورد مجیک',
                'starting_price' => 32000000,
                'buy_now_price' => 36000000,
            ],
            [
                'title' => 'هدفون سونی WH-1000XM5',
                'description' => 'هدفون بی‌سیم با نویز کنسلینگ، در حد نو',
                'starting_price' => 8500000,
                'buy_now_price' => 9500000,
            ],
            [
                'title' => 'ساعت هوشمند اپل واچ سری 8',
                'description' => '45 میلی‌متر، GPS + Cellular، بدنه استیل',
                'starting_price' => 15000000,
                'buy_now_price' => 17000000,
            ],
            [
                'title' => 'کنسول PS5 دیجیتال',
                'description' => 'پلی‌استیشن 5 نسخه دیجیتال، با دو دسته و 3 بازی',
                'starting_price' => 22000000,
                'buy_now_price' => 25000000,
            ],
            [
                'title' => 'دوربین کانن EOS R6',
                'description' => 'دوربین فول فریم میرورلس، با لنز 24-105mm',
                'starting_price' => 95000000,
                'buy_now_price' => 105000000,
            ],
            [
                'title' => 'مانیتور گیمینگ ایسوس 27 اینچ',
                'description' => '144Hz، 1ms، QHD، G-Sync، حالت عالی',
                'starting_price' => 12000000,
                'buy_now_price' => 14000000,
            ],
            [
                'title' => 'کیبورد مکانیکی لاجیتک G915',
                'description' => 'کیبورد بی‌سیم، سوییچ تاکتایل، RGB',
                'starting_price' => 6500000,
                'buy_now_price' => 7500000,
            ],
        ];

        $category = $categories->first();
        $shippingMethods = ShippingMethod::all();

        foreach ($listings as $index => $data) {
            $seller = $sellers->random();
            
            $listing = Listing::create([
                'seller_id' => $seller->id,
                'title' => $data['title'],
                'slug' => \Str::slug($data['title']),
                'description' => $data['description'],
                'category_id' => $category->id,
                'starting_price' => $data['starting_price'],
                'current_price' => $data['starting_price'],
                'buy_now_price' => $data['buy_now_price'],
                'bid_increment' => 500000,
                'deposit_amount' => $data['starting_price'] * 0.1,
                'starts_at' => Carbon::now()->subHours(rand(1, 48)),
                'ends_at' => Carbon::now()->addHours(rand(24, 168)),
                'status' => 'active',
                'views' => rand(50, 500),
            ]);

            // اضافه کردن روش‌های ارسال
            if ($shippingMethods->isNotEmpty()) {
                $listing->shippingMethods()->attach(
                    $shippingMethods->random(rand(1, 2))->pluck('id')
                );
            }

            $this->command->info("آگهی ایجاد شد: {$data['title']}");
        }

        $this->command->info('✓ ' . count($listings) . ' آگهی با موفقیت ایجاد شد!');
    }

    private function createShippingMethods()
    {
        if (ShippingMethod::count() > 0) {
            return;
        }

        $methods = [
            ['name' => 'پست پیشتاز', 'base_cost' => 50000, 'estimated_days' => 3],
            ['name' => 'پست سفارشی', 'base_cost' => 30000, 'estimated_days' => 7],
            ['name' => 'تیپاکس', 'base_cost' => 80000, 'estimated_days' => 2],
            ['name' => 'پیک موتوری', 'base_cost' => 100000, 'estimated_days' => 1],
        ];

        foreach ($methods as $method) {
            ShippingMethod::create($method);
        }

        $this->command->info('✓ روش‌های ارسال ایجاد شد');
    }
}
