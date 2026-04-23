<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('name', 'LIKE', '%الهه%')->first();

echo "تراکنش‌های سپرده کاربر {$user->name}:\n\n";

$transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
    ->where('type', 'freeze_deposit')
    ->where('description', 'LIKE', '%بلاک سپرده حراجی%')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($transactions as $t) {
    echo "تراکنش ID: {$t->id}\n";
    echo "  - مبلغ: " . number_format($t->amount) . " تومان\n";
    echo "  - توضیحات: {$t->description}\n";
    echo "  - reference_type: {$t->reference_type}\n";
    echo "  - reference_id: {$t->reference_id}\n";
    echo "  - تاریخ: {$t->created_at}\n";
    
    if ($t->reference_id && $t->reference_type === 'App\Models\Listing') {
        $listing = \App\Models\Listing::find($t->reference_id);
        if ($listing) {
            echo "  - حراجی: {$listing->title} (ID: {$listing->id})\n";
            echo "  - مبلغ سپرده حراجی: " . number_format($listing->deposit_amount) . " تومان\n";
            
            // Check if participation exists
            $participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($participation) {
                echo "  - ✓ رکورد participation وجود دارد\n";
            } else {
                echo "  - ✗ رکورد participation وجود ندارد!\n";
            }
        }
    }
    echo "\n";
}
