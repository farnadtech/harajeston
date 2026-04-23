<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Listing;
use App\Models\AuctionParticipation;
use App\Models\Bid;

echo "تست کامل فرآیند خرید فوری با سپرده...\n\n";

// پیدا کردن یا ایجاد کاربر تست
$buyer = User::where('email', 'buyer1@example.com')->first();
if (!$buyer) {
    echo "❌ کاربر خریدار یافت نشد!\n";
    exit;
}

$seller = User::where('role', 'seller')->where('seller_status', 'active')->first();
if (!$seller) {
    echo "❌ فروشنده فعال یافت نشد!\n";
    exit;
}

echo "خریدار: {$buyer->name} (ID: {$buyer->id})\n";
echo "موجودی اولیه: " . number_format($buyer->wallet->balance) . " تومان\n";
echo "مبلغ بلوک شده اولیه: " . number_format($buyer->wallet->frozen) . " تومان\n\n";

// ایجاد حراجی تست با سپرده و خرید فوری
$listing = Listing::create([
    'seller_id' => $seller->id,
    'title' => 'تست خرید فوری با سپرده - ' . now()->format('H:i:s'),
    'description' => 'این یک حراجی تست برای بررسی خرید فوری با سپرده است',
    'category_id' => 1,
    'starting_price' => 50000,
    'current_price' => 50000,
    'buy_now_price' => 100000,
    'bid_increment' => 5000,
    'deposit_amount' => 10000,
    'status' => 'active',
    'starts_at' => now()->subHour(),
    'ends_at' => now()->addDay(),
    'auto_extend' => false,
]);

echo "✓ حراجی ایجاد شد: {$listing->title} (ID: {$listing->id})\n";
echo "  قیمت شروع: " . number_format($listing->starting_price) . " تومان\n";
echo "  قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان\n";
echo "  مبلغ سپرده: " . number_format($listing->deposit_amount) . " تومان\n\n";

// مرحله 1: کاربر پیشنهاد می‌دهد (سپرده بلاک می‌شود)
echo "مرحله 1: ثبت پیشنهاد و بلاک سپرده...\n";
echo str_repeat("-", 80) . "\n";

try {
    $bidService = app(\App\Services\BidService::class);
    $bid = $bidService->placeBid($buyer, $listing, 55000);
    
    echo "✓ پیشنهاد ثبت شد: " . number_format($bid->amount) . " تومان\n";
    
    // بررسی wallet
    $buyer->wallet->refresh();
    echo "موجودی بعد از پیشنهاد: " . number_format($buyer->wallet->balance) . " تومان\n";
    echo "مبلغ بلوک شده: " . number_format($buyer->wallet->frozen) . " تومان\n";
    
    // بررسی participation
    $participation = AuctionParticipation::where('listing_id', $listing->id)
        ->where('user_id', $buyer->id)
        ->first();
    
    if ($participation) {
        echo "✓ رکورد participation ثبت شد\n";
        echo "  مبلغ سپرده: " . number_format($participation->deposit_amount) . " تومان\n";
    } else {
        echo "❌ رکورد participation ثبت نشد!\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطا در ثبت پیشنهاد: " . $e->getMessage() . "\n";
    exit;
}

echo "\n";

// مرحله 2: کاربر خرید فوری می‌کند
echo "مرحله 2: خرید فوری...\n";
echo str_repeat("-", 80) . "\n";

$balanceBefore = $buyer->wallet->balance;
$frozenBefore = $buyer->wallet->frozen;

echo "موجودی قبل از خرید فوری: " . number_format($balanceBefore) . " تومان\n";
echo "مبلغ بلوک شده قبل: " . number_format($frozenBefore) . " تومان\n";
echo "مبلغ مورد انتظار برای کسر: " . number_format($listing->buy_now_price - $listing->deposit_amount) . " تومان\n\n";

// شبیه‌سازی درخواست خرید فوری
try {
    \DB::transaction(function() use ($listing, $buyer) {
        $participation = AuctionParticipation::where('listing_id', $listing->id)
            ->where('user_id', $buyer->id)
            ->where('deposit_status', 'paid')
            ->first();
        
        $wallet = $buyer->wallet;
        $buyNowPrice = $listing->buy_now_price;
        
        if ($participation) {
            $depositAmount = $participation->deposit_amount;
            $amountToPay = $buyNowPrice - $depositAmount;
            
            echo "✓ کاربر قبلاً شرکت کرده (سپرده: " . number_format($depositAmount) . " تومان)\n";
            echo "مبلغ قابل پرداخت: " . number_format($amountToPay) . " تومان\n\n";
            
            // Check balance
            if ($wallet->balance < $amountToPay) {
                throw new \Exception('موجودی کافی نیست');
            }
            
            // Unfreeze deposit
            $wallet->frozen -= $depositAmount;
            $wallet->balance += $depositAmount;
            
            // Freeze buy_now_price
            $wallet->balance -= $buyNowPrice;
            $wallet->frozen += $buyNowPrice;
            $wallet->save();
            
            // Record transactions
            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $buyer->id,
                'type' => 'release_deposit',
                'amount' => $depositAmount,
                'final_amount' => $depositAmount,
                'balance_before' => $wallet->balance + $buyNowPrice - $depositAmount,
                'balance_after' => $wallet->balance + $buyNowPrice,
                'frozen_before' => $wallet->frozen - $buyNowPrice + $depositAmount,
                'frozen_after' => $wallet->frozen - $buyNowPrice,
                'reference_type' => \App\Models\Listing::class,
                'reference_id' => $listing->id,
                'status' => 'completed',
                'description' => sprintf('آزادسازی سپرده برای خرید فوری: %s', $listing->title),
            ]);
            
            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $buyer->id,
                'type' => 'freeze_deposit',
                'amount' => $buyNowPrice,
                'final_amount' => $buyNowPrice,
                'balance_before' => $wallet->balance + $buyNowPrice,
                'balance_after' => $wallet->balance,
                'frozen_before' => $wallet->frozen - $buyNowPrice,
                'frozen_after' => $wallet->frozen,
                'reference_type' => \App\Models\Listing::class,
                'reference_id' => $listing->id,
                'status' => 'completed',
                'description' => sprintf('بلاک مبلغ خرید فوری: %s', $listing->title),
            ]);
            
            echo "✓ تراکنش‌ها ثبت شد\n";
        }
        
        // Create order
        $order = \App\Models\Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'seller_id' => $listing->seller_id,
            'status' => 'processing',
            'subtotal' => $buyNowPrice,
            'shipping_cost' => 0,
            'total' => $buyNowPrice,
        ]);
        
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'listing_id' => $listing->id,
            'quantity' => 1,
            'price_snapshot' => $buyNowPrice,
            'subtotal' => $buyNowPrice,
        ]);
        
        $listing->status = 'completed';
        $listing->save();
        
        echo "✓ سفارش ایجاد شد: {$order->order_number}\n";
    });
    
    // بررسی نهایی
    $buyer->wallet->refresh();
    $balanceAfter = $buyer->wallet->balance;
    $frozenAfter = $buyer->wallet->frozen;
    
    echo "\nنتیجه نهایی:\n";
    echo "موجودی بعد: " . number_format($balanceAfter) . " تومان\n";
    echo "مبلغ بلوک شده بعد: " . number_format($frozenAfter) . " تومان\n";
    echo "تغییر موجودی: " . number_format($balanceAfter - $balanceBefore) . " تومان\n";
    echo "تغییر بلوک شده: " . number_format($frozenAfter - $frozenBefore) . " تومان\n";
    
    $expectedBalanceChange = -($listing->buy_now_price - $listing->deposit_amount);
    $actualBalanceChange = $balanceAfter - $balanceBefore;
    
    if ($actualBalanceChange == $expectedBalanceChange) {
        echo "\n✓ محاسبات صحیح است! فقط اختلاف از موجودی کسر شد.\n";
    } else {
        echo "\n❌ محاسبات اشتباه است!\n";
        echo "تغییر مورد انتظار: " . number_format($expectedBalanceChange) . " تومان\n";
        echo "تغییر واقعی: " . number_format($actualBalanceChange) . " تومان\n";
    }
    
} catch (\Exception $e) {
    echo "❌ خطا در خرید فوری: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
