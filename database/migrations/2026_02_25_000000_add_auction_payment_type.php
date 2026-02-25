<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify enum to add 'auction_payment'
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
            'refund'
        ) NOT NULL");
    }
};
