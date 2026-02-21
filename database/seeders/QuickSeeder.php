<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class QuickSeeder extends Seeder
{
    public function run(): void
    {
        // ایجاد یا بروزرسانی ادمین
        $admin = User::updateOrCreate(
            ['email' => 'admin@haraj.test'],
            [
                'name' => 'مدیر سیستم',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'seller_status' => 'none',
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->wallet) {
            Wallet::create([
                'user_id' => $admin->id,
                'balance' => 0,
                'frozen' => 0,
            ]);
        }

        // ایجاد چند فروشنده
        for ($i = 1; $i <= 5; $i++) {
            $seller = User::where('username', "seller$i")
                ->orWhere('email', "seller$i@test.com")
                ->first();
            
            if ($seller) {
                // بروزرسانی فروشنده موجود
                $seller->update([
                    'seller_status' => 'active',
                    'seller_requested_at' => $seller->seller_requested_at ?? now(),
                    'seller_approved_at' => $seller->seller_approved_at ?? now(),
                ]);
            } else {
                // ایجاد فروشنده جدید
                $seller = User::create([
                    'name' => "فروشنده $i",
                    'email' => "seller$i@test.com",
                    'username' => "seller$i",
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'seller_status' => 'active',
                    'seller_requested_at' => now(),
                    'seller_approved_at' => now(),
                    'email_verified_at' => now(),
                ]);
            }

            if (!$seller->wallet) {
                Wallet::create([
                    'user_id' => $seller->id,
                    'balance' => 0,
                    'frozen' => 0,
                ]);
            }

            if (!$seller->store) {
                Store::create([
                    'user_id' => $seller->id,
                    'name' => "فروشگاه $i",
                    'slug' => "store-$i",
                    'description' => "توضیحات فروشگاه $i",
                ]);
            }
        }

        // ایجاد چند خریدار
        for ($i = 1; $i <= 10; $i++) {
            $buyer = User::updateOrCreate(
                ['email' => "buyer$i@test.com"],
                [
                    'name' => "خریدار $i",
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'seller_status' => 'none',
                    'email_verified_at' => now(),
                ]
            );

            if (!$buyer->wallet) {
                Wallet::create([
                    'user_id' => $buyer->id,
                    'balance' => 0,
                    'frozen' => 0,
                ]);
            }
        }

        $this->command->info('کاربران با موفقیت ایجاد شدند!');
        $this->command->info('ادمین: admin@haraj.test / password');
        $this->command->info('فروشندگان: seller1@test.com تا seller5@test.com / password');
        $this->command->info('خریداران: buyer1@test.com تا buyer10@test.com / password');
    }
}
