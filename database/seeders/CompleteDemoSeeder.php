<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Listing, User, Bid, ListingComment, Order, OrderItem, AuctionParticipation, WalletTransaction};
use Carbon\Carbon;

class CompleteDemoSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->where('seller_status', 'none')->get();
        $listings = Listing::where('status', 'active')->get();

        if ($buyers->isEmpty() || $listings->isEmpty()) {
            $this->command->error('ابتدا QuickSeeder و FullDemoSeeder را اجرا کنید!');
            return;
        }

        // ایجاد مشارکت‌ها و پیشنهادها
        $this->createBidsAndParticipations($buyers, $listings);

        // ایجاد نظرات
        $this->createComments($buyers, $listings);

        // ایجاد سفارشات
        $this->createOrders($buyers, $listings);

        $this->command->info('✓ داده‌های نمونه کامل شد!');
    }

    private function createBidsAndParticipations($buyers, $listings)
    {
        foreach ($listings->take(7) as $listing) {
            // تخطی اگر قبلا مشارکت ایجاد شده
            if ($listing->participations()->count() > 0) {
                continue;
            }

            // انتخاب 2-4 خریدار برای هر حراجی
            $participants = $buyers->random(rand(2, 4));
            
            foreach ($participants as $buyer) {
                // ایجاد مشارکت
                AuctionParticipation::create([
                    'listing_id' => $listing->id,
                    'user_id' => $buyer->id,
                    'deposit_status' => 'paid',
                    'deposit_amount' => $listing->deposit_amount,
                ]);

                // ایجاد 1-3 پیشنهاد برای هر خریدار
                $bidCount = rand(1, 3);
                $currentPrice = $listing->starting_price;
                
                for ($i = 0; $i < $bidCount; $i++) {
                    $currentPrice += $listing->bid_increment;
                    
                    Bid::create([
                        'listing_id' => $listing->id,
                        'user_id' => $buyer->id,
                        'amount' => $currentPrice,
                        'created_at' => Carbon::now()->subHours(rand(1, 48)),
                    ]);
                }
            }

            // بروزرسانی قیمت فعلی حراجی
            $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
            if ($highestBid) {
                $listing->update([
                    'current_price' => $highestBid->amount,
                    'current_winner_id' => $highestBid->user_id,
                ]);
            }
        }

        $this->command->info('✓ پیشنهادها و مشارکت‌ها ایجاد شد');
    }

    private function createComments($buyers, $listings)
    {
        $comments = [
            'محصول عالی است، قیمت مناسب',
            'آیا امکان بازدید حضوری وجود دارد؟',
            'گارانتی دارد؟',
            'چه زمانی ارسال می‌شود؟',
            'آیا تخفیف دارد؟',
            'محصول اصل است؟',
            'عکس‌های بیشتری بگذارید لطفا',
            'قیمت نهایی چقدر است؟',
        ];

        foreach ($listings as $listing) {
            // 0-3 نظر برای هر آگهی
            $commentCount = rand(0, 3);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $buyer = $buyers->random();
                
                ListingComment::create([
                    'listing_id' => $listing->id,
                    'user_id' => $buyer->id,
                    'content' => $comments[array_rand($comments)],
                    'type' => rand(0, 1) ? 'comment' : 'question',
                    'status' => rand(0, 10) > 2 ? 'approved' : 'pending', // 80% تایید شده
                    'created_at' => Carbon::now()->subHours(rand(1, 72)),
                ]);
            }
        }

        $this->command->info('✓ نظرات ایجاد شد');
    }

    private function createOrders($buyers, $listings)
    {
        // ایجاد 3-5 سفارش تکمیل شده
        $orderCount = rand(3, 5);
        
        for ($i = 0; $i < $orderCount; $i++) {
            $buyer = $buyers->random();
            $listing = $listings->random();
            $seller = $listing->seller;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'status' => 'delivered',
                'subtotal' => $listing->buy_now_price ?? $listing->current_price,
                'shipping_cost' => 50000,
                'total' => ($listing->buy_now_price ?? $listing->current_price) + 50000,
                'shipping_method_id' => $listing->shippingMethods->first()->id ?? null,
                'shipping_address' => 'تهران، خیابان ولیعصر، پلاک 123',
                'tracking_number' => 'TRK-' . rand(100000, 999999),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'listing_id' => $listing->id,
                'quantity' => 1,
                'price_snapshot' => $listing->buy_now_price ?? $listing->current_price,
                'subtotal' => $listing->buy_now_price ?? $listing->current_price,
            ]);

            // ایجاد تراکنش کیف پول
            $wallet = $buyer->wallet;
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'purchase',
                'amount' => $order->total,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance - $order->total,
                'frozen_before' => $wallet->frozen,
                'frozen_after' => $wallet->frozen,
                'description' => "پرداخت سفارش {$order->order_number}",
                'reference_type' => 'App\Models\Order',
                'reference_id' => $order->id,
            ]);
        }

        $this->command->info('✓ سفارشات ایجاد شد');
    }
}
