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
        // Add 'rejected', 'suspended', 'cancelled' to listings status enum
        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `status` ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock', 'rejected', 'suspended', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'rejected', 'suspended', 'cancelled' from listings status enum
        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `status` ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock') NOT NULL DEFAULT 'pending'");
    }
};
