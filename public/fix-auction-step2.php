<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== مرحله 2: اصلاح مقدار سپرده حراجی‌ها ===\n\n";

$listings = \App\Models\Listing::whereIn('status', ['active', 'ended', 'pending'])
    ->get();

foreach ($listings as $listing) {
    // محاسبه سپرده صحیح (20% از قیمت پایه)
    $correctDeposit = (int) ($listing->starting_price * 0.20);
    
    $oldDeposit = $listing->required_deposit;
    $listing->required_deposit = $correctDeposit;
    $listing->save();
    echo "✓ حراجی #{$listing->id}: سپرده از " . number_format($oldDeposit) . " به " . number_format($correctDeposit) . " تومان\n";
}

echo "\n✓ مرحله 2 کامل شد\n";
