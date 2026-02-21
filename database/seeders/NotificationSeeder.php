<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            $this->command->warn('No admin user found. Skipping notification seeding.');
            return;
        }

        $notifications = [
            [
                'user_id' => $admin->id,
                'type' => 'bid',
                'title' => 'پیشنهاد جدید در مزایده',
                'message' => 'کاربر علی محمدی پیشنهاد ۵,۰۰۰,۰۰۰ تومان ثبت کرد',
                'icon' => 'gavel',
                'color' => 'blue',
                'link' => null,
                'is_read' => false,
                'created_at' => now()->subMinutes(5),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'order',
                'title' => 'سفارش جدید',
                'message' => 'سفارش #۱۲۳۴ ثبت شد',
                'icon' => 'shopping_bag',
                'color' => 'green',
                'link' => null,
                'is_read' => false,
                'created_at' => now()->subMinutes(15),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'auction_ending',
                'title' => 'مزایده در حال پایان',
                'message' => 'مزایده "گوشی آیفون" ۱ ساعت دیگر پایان می‌یابد',
                'icon' => 'warning',
                'color' => 'yellow',
                'link' => null,
                'is_read' => false,
                'created_at' => now()->subMinutes(30),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'payment',
                'title' => 'پرداخت دریافت شد',
                'message' => 'مبلغ ۲,۰۰۰,۰۰۰ تومان به کیف پول شما واریز شد',
                'icon' => 'payments',
                'color' => 'green',
                'link' => null,
                'is_read' => true,
                'created_at' => now()->subHours(2),
            ],
            [
                'user_id' => $admin->id,
                'type' => 'user',
                'title' => 'کاربر جدید',
                'message' => 'کاربر جدیدی با نام "محمد رضایی" ثبت‌نام کرد',
                'icon' => 'person_add',
                'color' => 'blue',
                'link' => null,
                'is_read' => true,
                'created_at' => now()->subHours(5),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
