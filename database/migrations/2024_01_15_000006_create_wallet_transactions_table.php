<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->enum('type', [
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
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->decimal('frozen_before', 15, 2);
            $table->decimal('frozen_after', 15, 2);
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes (wallet_id already indexed by foreignId, created_at by timestamps)
            $table->index('type', 'idx_type');
            $table->index(['reference_type', 'reference_id'], 'idx_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
