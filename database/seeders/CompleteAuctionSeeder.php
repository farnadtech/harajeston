<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteAuctionSeeder extends Seeder
{
    public function run(): void
    {
        // ایجاد فروشنده
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'فروشنده نمونه',
                'username' => 'seller_demo',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'email_verified_at' => now(),
            ]
        );

        // ایجاد فروشگاه
        $store = Store::firstOrCreate(
            ['user_id' => $seller->id],
            [
                'store_name' => 'فروشگاه دیجیتال پرو',
                'slug' => 'digital-pro-store',
                'description' => 'فروشگاه تخصصی محصولات دیجیتال با بهترین کیفیت',
                'is_active' => true,
            ]
        );

        // ایجاد خریداران
        $buyers = [];
        for ($i = 1; $i <= 5; $i++) {
            $buyers[] = User::firstOrCreate(
                ['email' => "buyer{$i}@example.com"],
                [
                    'name' => "خریدار {$i}",
                    'username' => "buyer_{$i}",
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'email_verified_at' => now(),
                ]
            );
        }

        // ایجاد حراج کامل با تمام جزئیات
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'آیفون ۱۴ پرو مکس ۲۵۶ گیگابایت - کاملاً نو و پلمپ',
            'description' => 'آیفون ۱۴ پرو مکس با ظرفیت ۲۵۶ گیگابایت، رنگ دیپ پرپل، کاملاً نو و پلمپ با گارانتی اپل. 
            
مشخصات:
- صفحه نمایش: Super Retina XDR 6.7 اینچ
- پردازنده: A16 Bionic
- دوربین: سیستم سه دوربینه 48 مگاپیکسل
- باتری: تا 29 ساعت پخش ویدیو
- مقاومت در برابر آب و گرد و خاک: IP68

محتویات جعبه:
✓ گوشی آیفون ۱۴ پرو مکس
✓ کابل USB-C به لایتنینگ
✓ مستندات و برچسب SIM
✓ گارانتی رسمی اپل یک ساله',
            'category' => 'موبایل',
            'starting_price' => 45000000, // 45 میلیون تومان
            'current_price' => 52500000, // 52.5 میلیون تومان
            'buy_now_price' => 65000000, // 65 میلیون تومان - خرید فوری
            'required_deposit' => 5000000, // 5 میلیون تومان
            'highest_bidder_id' => $buyers[4]->id,
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->addDays(3),
            'status' => 'active',
        ]);

        // ثبت شرکت‌کنندگان در حراج
        foreach ($buyers as $buyer) {
            AuctionParticipation::create([
                'user_id' => $buyer->id,
                'listing_id' => $listing->id,
                'deposit_amount' => 5000000,
                'deposit_status' => 'frozen',
            ]);
        }

        // ثبت پیشنهادات
        $bidAmounts = [
            45500000, // خریدار 1
            46000000, // خریدار 2
            47000000, // خریدار 1
            48000000, // خریدار 3
            49000000, // خریدار 2
            50000000, // خریدار 4
            51000000, // خریدار 1
            52000000, // خریدار 5
            52500000, // خریدار 5 - بالاترین پیشنهاد
        ];

        $bidders = [
            $buyers[0], $buyers[1], $buyers[0], $buyers[2], 
            $buyers[1], $buyers[3], $buyers[0], $buyers[4], $buyers[4]
        ];

        foreach ($bidAmounts as $index => $amount) {
            Bid::create([
                'listing_id' => $listing->id,
                'user_id' => $bidders[$index]->id,
                'amount' => $amount,
                'created_at' => now()->subDays(2)->addHours($index * 2),
            ]);
        }

        // ایجاد چند حراج دیگر
        $otherListings = [
            [
                'title' => 'لپ‌تاپ MacBook Pro M2 - 16GB RAM',
                'description' => 'مک‌بوک پرو با چیپ M2، رم 16 گیگابایت، حافظه 512 گیگابایت SSD',
                'category' => 'لپ‌تاپ',
                'starting_price' => 55000000,
                'current_price' => 58000000,
                'buy_now_price' => 70000000,
                'required_deposit' => 6000000,
            ],
            [
                'title' => 'ساعت هوشمند Apple Watch Series 8',
                'description' => 'اپل واچ سری 8 با بدنه استیل، 45 میلی‌متر، بند اسپورت',
                'category' => 'ساعت هوشمند',
                'starting_price' => 12000000,
                'current_price' => 13500000,
                'buy_now_price' => null, // بدون خرید فوری
                'required_deposit' => 1500000,
            ],
            [
                'title' => 'تبلت iPad Pro 12.9 اینچ',
                'description' => 'آیپد پرو 12.9 اینچ با چیپ M2، 256 گیگابایت، Wi-Fi + Cellular',
                'category' => 'تبلت',
                'starting_price' => 28000000,
                'current_price' => 30000000,
                'buy_now_price' => 38000000,
                'required_deposit' => 3000000,
            ],
        ];

        foreach ($otherListings as $data) {
            Listing::create([
                'seller_id' => $seller->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'starting_price' => $data['starting_price'],
                'current_price' => $data['current_price'],
                'buy_now_price' => $data['buy_now_price'],
                'required_deposit' => $data['required_deposit'],
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(rand(2, 7)),
                'status' => 'active',
            ]);
        }

        $this->command->info('✅ داده‌های نمونه کامل با موفقیت ایجاد شد!');
        $this->command->info('📧 فروشنده: seller@example.com');
        $this->command->info('📧 خریداران: buyer1@example.com تا buyer5@example.com');
        $this->command->info('🔑 رمز عبور همه: password');
    }
}
