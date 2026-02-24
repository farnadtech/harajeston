<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if payment_gateways table exists
        if (!Schema::hasTable('payment_gateways')) {
            Schema::create('payment_gateways', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // mellat, saman, zarinpal, etc.
                $table->string('display_name');
                $table->boolean('is_active')->default(false);
                $table->json('credentials')->nullable(); // merchant_id, terminal_id, etc.
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Check if wallet_transactions table exists
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // charge, withdraw, commission, refund
                $table->decimal('amount', 15, 0);
                $table->decimal('tax_amount', 15, 0)->default(0);
                $table->decimal('final_amount', 15, 0);
                $table->string('gateway')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('reference_id')->nullable();
                $table->string('status')->default('pending'); // pending, completed, failed
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            // Add new columns if table exists
            Schema::table('wallet_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('wallet_transactions', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 0)->default(0)->after('amount');
                }
                if (!Schema::hasColumn('wallet_transactions', 'final_amount')) {
                    $table->decimal('final_amount', 15, 0)->after('tax_amount');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('payment_gateways');
    }
};
