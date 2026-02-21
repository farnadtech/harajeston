<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Listing, ShippingMethod};

class AttachShippingMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $shippingMethods = ShippingMethod::all();
        
        if ($shippingMethods->isEmpty()) {
            $this->command->error('هیچ روش ارسالی وجود ندارد!');
            return;
        }

        $listings = Listing::all();
        
        foreach ($listings as $listing) {
            // انتخاب 1-3 روش ارسال به صورت رندوم
            $randomMethods = $shippingMethods->random(rand(1, min(3, $shippingMethods->count())));
            
            // پاک کردن روش‌های قبلی
            $listing->shippingMethods()->detach();
            
            // اضافه کردن روش‌های جدید
            foreach ($randomMethods as $method) {
                $listing->shippingMethods()->attach($method->id, [
                    'custom_cost_adjustment' => rand(-10000, 20000) // تنظیم قیمت سفارشی
                ]);
            }
        }

        $this->command->info('✓ روش‌های ارسال به ' . $listings->count() . ' حراجی اضافه شد!');
    }
}
