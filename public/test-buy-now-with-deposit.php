<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\User;
use App\Models\AuctionParticipation;

echo "<h1>تست خرید فوری با سپرده</h1>";
echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:right;} th{background:#f2f2f2;}</style>";

// پیدا کردن یک حراجی فعال با خرید فوری
$listing = Listing::where('status', 'active')
    ->whereNotNull('buy_now_price')
    ->where('buy_now_price', '>', 0)
    ->with(['bids' => function($q) {
        $q->orderBy('amount', 'desc');
    }])
    ->first();

if (!$listing) {
    echo "<p class='error'>هیچ حراجی فعالی با خرید فوری یافت نشد.</p>";
    exit;
}

echo "<h2>حراجی: {$listing->title}</h2>";
echo "<p>ID: {$listing->id}</p>";
echo "<p>قیمت فعلی: " . number_format($listing->current_price) . " تومان</p>";
echo "<p>قیمت خرید فوری: " . number_format($listing->buy_now_price) . " تومان</p>";

echo "<hr>";

// پیدا کردن کاربرانی که در این حراجی شرکت کرده‌اند
$participations = AuctionParticipation::where('listing_id', $listing->id)
    ->where('deposit_status', 'paid')
    ->with('user.wallet')
    ->get();

if ($participations->isEmpty()) {
    echo "<p class='info'>هیچ کاربری در این حراجی شرکت نکرده است.</p>";
} else {
    echo "<h3>کاربران شرکت‌کننده</h3>";
    echo "<table>";
    echo "<tr><th>نام کاربر</th><th>سپرده بلاک شده</th><th>موجودی فعلی</th><th>موجودی بلاک شده</th><th>مبلغ مورد نیاز برای خرید فوری</th><th>آیا می‌تواند خرید کند؟</th></tr>";
    
    foreach ($participations as $participation) {
        $user = $participation->user;
        $wallet = $user->wallet;
        $depositAmount = $participation->deposit_amount;
        $amountNeeded = $listing->buy_now_price - $depositAmount;
        $canBuy = $wallet && $wallet->balance >= $amountNeeded;
        
        echo "<tr>";
        echo "<td>{$user->name}</td>";
        echo "<td>" . number_format($depositAmount) . " تومان</td>";
        echo "<td>" . number_format($wallet ? $wallet->balance : 0) . " تومان</td>";
        echo "<td>" . number_format($wallet ? $wallet->frozen : 0) . " تومان</td>";
        echo "<td class='info'>" . number_format($amountNeeded) . " تومان</td>";
        echo "<td class='" . ($canBuy ? 'success' : 'error') . "'>" . ($canBuy ? '✓ بله' : '✗ خیر') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr>";

// پیدا کردن کاربرانی که شرکت نکرده‌اند
$nonParticipants = User::whereNotIn('id', $participations->pluck('user_id'))
    ->where('role', '!=', 'admin')
    ->with('wallet')
    ->limit(5)
    ->get();

if ($nonParticipants->isNotEmpty()) {
    echo "<h3>کاربران غیر شرکت‌کننده (نمونه)</h3>";
    echo "<table>";
    echo "<tr><th>نام کاربر</th><th>موجودی فعلی</th><th>مبلغ مورد نیاز برای خرید فوری</th><th>آیا می‌تواند خرید کند؟</th></tr>";
    
    foreach ($nonParticipants as $user) {
        $wallet = $user->wallet;
        $amountNeeded = $listing->buy_now_price;
        $canBuy = $wallet && $wallet->balance >= $amountNeeded;
        
        echo "<tr>";
        echo "<td>{$user->name}</td>";
        echo "<td>" . number_format($wallet ? $wallet->balance : 0) . " تومان</td>";
        echo "<td class='info'>" . number_format($amountNeeded) . " تومان</td>";
        echo "<td class='" . ($canBuy ? 'success' : 'error') . "'>" . ($canBuy ? '✓ بله' : '✗ خیر') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr>";
echo "<h3>توضیحات</h3>";
echo "<ul>";
echo "<li><strong>کاربران شرکت‌کننده:</strong> فقط باید اختلاف (قیمت خرید فوری - سپرده) را پرداخت کنند</li>";
echo "<li><strong>کاربران غیر شرکت‌کننده:</strong> باید کل مبلغ خرید فوری را پرداخت کنند</li>";
echo "<li><strong>سپرده بلاک شده:</strong> این مبلغ قبلاً از موجودی کسر و بلاک شده است</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='/haraj/public/listings/{$listing->id}'>مشاهده آگهی و تست خرید فوری</a></p>";
