<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Foreign key to users table');
            $table->decimal('balance', 15, 2)->default(0.00)->comment('Available balance');
            $table->decimal('frozen', 15, 2)->default(0.00)->comment('Frozen balance for auction deposits');
            $table->timestamps();
            
            // Indexes - user_id already indexed by foreignId
        });
        
        // Add check constraints for non-negative values using raw SQL (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE wallets ADD CONSTRAINT chk_balance_positive CHECK (balance >= 0)');
            DB::statement('ALTER TABLE wallets ADD CONSTRAINT chk_frozen_positive CHECK (frozen >= 0)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
