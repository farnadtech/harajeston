<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * اجرای تمام سیدرها به ترتیب
     */
    public function run(): void
    {
        $this->command->info('شروع پر کردن دیتابیس با داده‌های نمونه...');
        
        // 1. دسته‌بندی‌ها
        $this->call(CategorySeeder::class);
        
        // 2. کاربران (ادمین، فروشندگان، خریداران)
        $this->call(QuickSeeder::class);
        
        // 3. آگهی‌ها و روش‌های ارسال
        $this->call(FullDemoSeeder::class);
        
        // 4. پیشنهادها، نظرات، سفارشات
        $this->call(CompleteDemoSeeder::class);
        
        $this->command->info('✓ تمام داده‌های نمونه با موفقیت ایجاد شد!');
        $this->command->info('');
        $this->command->info('اطلاعات ورود:');
        $this->command->info('ادمین: admin@haraj.test / password');
        $this->command->info('فروشندگان: seller1@test.com تا seller5@test.com / password');
        $this->command->info('خریداران: buyer1@test.com تا buyer10@test.com / password');
    }
}
