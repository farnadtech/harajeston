<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Store;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\AuctionParticipation;
use App\Models\ShippingMethod;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\DepositService;
use App\Services\BidService;
use App\Services\WalletService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@haraj.test'],
            [
                'name' => 'مدیر سیستم',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        
        if (!$admin->wallet) {
            Wallet::create(['user_id' => $admin->id, 'balance' => 1000000, 'frozen' => 0]);
        }

        // Create sellers
        $sellers = [];
        for ($i = 1; $i <= 5; $i++) {
            $seller = User::create([
                'name' => "فروشنده $i",
                'email' => "seller$i@haraj.test",
                'username' => "seller$i",
                'password' => bcrypt('password'),
                'role' => 'seller',
                'email_verified_at' => now(),
            ]);
            Wallet::create(['user_id' => $seller->id, 'balance' => 500000, 'frozen' => 0]);
            
            Store::create([
                'user_id' => $seller->id,
                'store_name' => "فروشگاه شماره $i",
                'slug' => "seller$i",
                'description' => "توضیحات فروشگاه شماره $i - ما بهترین محصولات را ارائه می‌دهیم.",
                'is_active' => true,
            ]);
            
            $sellers[] = $seller;
        }

        // Create buyers
        $buyers = [];
        for ($i = 1; $i <= 10; $i++) {
            $buyer = User::create([
                'name' => "خریدار $i",
                'email' => "buyer$i@haraj.test",
                'password' => bcrypt('password'),
                'role' => 'buyer',
                'email_verified_at' => now(),
            ]);
            Wallet::create(['user_id' => $buyer->id, 'balance' => 100000, 'frozen' => 0]);
            $buyers[] = $buyer;
        }

        // Create shipping methods
        $shippingMethods = [
            ['name' => 'پست پیشتاز', 'description' => 'ارسال سریع با پست پیشتاز', 'base_cost' => 50000],
            ['name' => 'پست عادی', 'description' => 'ارسال با پست عادی', 'base_cost' => 25000],
            ['name' => 'پیک موتوری', 'description' => 'ارسال با پیک در شهر تهران', 'base_cost' => 75000],
            ['name' => 'باربری', 'description' => 'ارسال با باربری برای کالاهای سنگین', 'base_cost' => 150000],
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::create([
                'name' => $method['name'],
                'description' => $method['description'],
                'base_cost' => $method['base_cost'],
                'is_active' => true,
                'created_by' => $admin->id,
            ]);
        }

        $allShippingMethods = ShippingMethod::all();

        // Create auctions in different states
        $auctionTitles = [
            'لپ‌تاپ ایسوس مدل ROG',
            'گوشی سامسونگ گلکسی S23',
            'دوچرخه کوهستان جاینت',
            'ساعت هوشمند اپل واچ',
            'کنسول بازی پلی‌استیشن 5',
        ];

        foreach ($sellers as $index => $seller) {
            // Active auction
            $activeAuction = Listing::create([
                'seller_id' => $seller->id,
                'type' => 'auction',
                'title' => $auctionTitles[$index] ?? "مزایده شماره " . ($index + 1),
                'description' => 'محصول در حالت عالی، تقریباً نو و بدون خط و خش',
                'category' => 'الکترونیک',
                'base_price' => 10000000 + ($index * 1000000),
                'required_deposit' => 1000000 + ($index * 100000),
                'start_time' => now()->subHours(2),
                'end_time' => now()->addHours(24),
                'status' => 'active',
            ]);
            $activeAuction->shippingMethods()->attach($allShippingMethods->random(2)->pluck('id'));

            // Pending auction
            Listing::create([
                'seller_id' => $seller->id,
                'type' => 'auction',
                'title' => 'تبلت آیپد پرو 12.9 اینچ',
                'description' => 'تبلت حرفه‌ای اپل با قلم و کیبورد',
                'category' => 'الکترونیک',
                'base_price' => 15000000,
                'required_deposit' => 1500000,
                'start_time' => now()->addHours(6),
                'end_time' => now()->addHours(30),
                'status' => 'pending',
            ]);

            // Ended auction
            $endedAuction = Listing::create([
                'seller_id' => $seller->id,
                'type' => 'auction',
                'title' => 'دوربین کانن EOS R5',
                'description' => 'دوربین حرفه‌ای با لنز 24-70',
                'category' => 'الکترونیک',
                'base_price' => 20000000,
                'required_deposit' => 2000000,
                'current_highest_bid' => 25000000,
                'start_time' => now()->subDays(2),
                'end_time' => now()->subHours(1),
                'status' => 'ended',
                'finalization_deadline' => now()->addHours(47),
            ]);
        }

        // Create direct sale listings
        $directSaleTitles = [
            'کتاب برنامه‌نویسی لاراول',
            'هدفون بلوتوثی سونی',
            'کیف لپ‌تاپ چرمی',
            'ماوس گیمینگ لاجیتک',
            'کیبورد مکانیکی',
        ];

        foreach ($sellers as $index => $seller) {
            Listing::create([
                'seller_id' => $seller->id,
                'type' => 'direct_sale',
                'title' => $directSaleTitles[$index] ?? "محصول شماره " . ($index + 1),
                'description' => 'محصول با کیفیت عالی و قیمت مناسب',
                'category' => 'لوازم جانبی',
                'price' => 500000 + ($index * 100000),
                'stock' => 10 + $index,
                'low_stock_threshold' => 3,
                'status' => 'active',
            ]);
        }

        // Create hybrid listings
        foreach (array_slice($sellers, 0, 2) as $index => $seller) {
            Listing::create([
                'seller_id' => $seller->id,
                'type' => 'hybrid',
                'title' => 'گوشی آیفون 14 پرو مکس',
                'description' => 'گوشی در حالت عالی، هم می‌توانید مزایده کنید هم مستقیم خرید کنید',
                'category' => 'موبایل',
                'base_price' => 30000000,
                'required_deposit' => 3000000,
                'price' => 35000000,
                'stock' => 2,
                'low_stock_threshold' => 1,
                'start_time' => now()->subHours(1),
                'end_time' => now()->addHours(48),
                'status' => 'active',
            ]);
        }

        // Add bids to active auctions
        $depositService = new DepositService(new WalletService());
        $bidService = new BidService();

        $activeAuctions = Listing::where('type', 'auction')
            ->where('status', 'active')
            ->get();

        foreach ($activeAuctions as $auction) {
            $participatingBuyers = $buyers->random(min(5, count($buyers)));
            
            foreach ($participatingBuyers as $buyer) {
                try {
                    $depositService->participateInAuction($buyer, $auction);
                    
                    $bidAmount = $auction->base_price + rand(1, 10) * 500000;
                    $bidService->placeBid($buyer, $auction, $bidAmount);
                } catch (\Exception $e) {
                    // Skip if insufficient balance or other errors
                }
            }
        }

        // Create some completed orders
        foreach (array_slice($sellers, 0, 3) as $seller) {
            $directSaleListing = Listing::where('seller_id', $seller->id)
                ->where('type', 'direct_sale')
                ->first();

            if ($directSaleListing) {
                $buyer = $buyers->random();
                
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'status' => 'delivered',
                    'subtotal' => $directSaleListing->price * 2,
                    'shipping_cost' => 50000,
                    'total' => ($directSaleListing->price * 2) + 50000,
                    'shipping_method_id' => $allShippingMethods->first()->id,
                    'shipping_address' => 'تهران، خیابان ولیعصر، پلاک 123',
                    'tracking_number' => 'TRK-' . rand(100000, 999999),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $directSaleListing->id,
                    'quantity' => 2,
                    'price_snapshot' => $directSaleListing->price,
                    'subtotal' => $directSaleListing->price * 2,
                ]);
            }
        }

        $this->command->info('✅ داده‌های نمایشی با موفقیت ایجاد شدند!');
        $this->command->info('📧 ایمیل‌ها: admin@haraj.test, seller1@haraj.test, buyer1@haraj.test');
        $this->command->info('🔑 رمز عبور همه کاربران: password');
    }
}
