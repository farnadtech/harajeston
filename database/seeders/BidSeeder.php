<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use App\Models\Bid;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // پیدا کردن یک مزایده فعال
        $auction = Listing::where('type', 'auction')
            ->where('status', 'active')
            ->first();

        if (!$auction) {
            $this->command->warn('هیچ مزایده فعالی یافت نشد!');
            return;
        }

        // پیدا کردن یا ساختن کاربران خریدار
        $buyers = User::where('role', 'buyer')->take(5)->get();
        
        if ($buyers->count() < 5) {
            // ساخت کاربران اضافی
            for ($i = $buyers->count(); $i < 5; $i++) {
                $buyers->push(User::create([
                    'name' => 'خریدار ' . ($i + 1),
                    'email' => 'buyer' . ($i + 1) . '@test.com',
                    'phone' => '0912000000' . ($i + 1),
                    'password' => bcrypt('password'),
                    'role' => 'buyer',
                ]));
            }
        }

        $this->command->info("ثبت پیشنهادات برای مزایده: {$auction->title}");

        // قیمت شروع
        $currentPrice = $auction->base_price;
        
        // ثبت 10 پیشنهاد با قیمت‌های مختلف
        $bidAmounts = [
            $currentPrice + 100000,
            $currentPrice + 250000,
            $currentPrice + 300000,
            $currentPrice + 450000,
            $currentPrice + 500000,
            $currentPrice + 600000,
            $currentPrice + 800000,
            $currentPrice + 900000,
            $currentPrice + 1000000,
            $currentPrice + 1200000,
        ];

        foreach ($bidAmounts as $index => $amount) {
            $buyer = $buyers->random();
            
            $bid = Bid::create([
                'listing_id' => $auction->id,
                'user_id' => $buyer->id,
                'amount' => $amount,
                'created_at' => now()->subMinutes(50 - ($index * 5)),
                'updated_at' => now()->subMinutes(50 - ($index * 5)),
            ]);

            // به‌روزرسانی قیمت فعلی مزایده
            $auction->update([
                'current_highest_bid' => $amount,
                'highest_bidder_id' => $buyer->id,
            ]);

            $bidNumber = $index + 1;
            $this->command->info("✓ پیشنهاد {$bidNumber}: " . number_format($amount) . " تومان توسط {$buyer->name}");
        }

        $totalBids = count($bidAmounts);
        $this->command->info("\n✅ {$totalBids} پیشنهاد با موفقیت ثبت شد!");
        $this->command->info("🔗 لینک مزایده: /listings/{$auction->id}");
    }
}
