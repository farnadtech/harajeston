<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 0)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('wallet_transactions', 'final_amount')) {
                $table->decimal('final_amount', 15, 0)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('wallet_transactions', 'gateway')) {
                $table->string('gateway')->nullable()->after('final_amount');
            }
            if (!Schema::hasColumn('wallet_transactions', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('gateway');
            }
            if (!Schema::hasColumn('wallet_transactions', 'reference_id')) {
                $table->string('reference_id')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('wallet_transactions', 'status')) {
                $table->string('status')->default('pending')->after('reference_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'tax_amount',
                'final_amount',
                'gateway',
                'transaction_id',
                'reference_id',
                'status',
            ]);
        });
    }
};
