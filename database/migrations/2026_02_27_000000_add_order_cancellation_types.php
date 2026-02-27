<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add order cancellation related types to wallet_transactions enum
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
            'deposit',
            'withdrawal',
            'freeze_deposit',
            'release_deposit',
            'deduct_frozen',
            'transfer_in',
            'transfer_out',
            'forfeit',
            'purchase',
            'refund',
            'auction_payment',
            'order_cancellation_penalty',
            'order_cancellation_penalty_revenue',
            'unfreeze_refund'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
            'deposit',
            'withdrawal',
            'freeze_deposit',
            'release_deposit',
            'deduct_frozen',
            'transfer_in',
            'transfer_out',
            'forfeit',
            'purchase',
            'refund',
            'auction_payment'
        ) NOT NULL");
    }
};
