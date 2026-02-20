<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the status enum to include 'suspended' and 'cancelled'
        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `status` ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock', 'suspended', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `status` ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock') NOT NULL DEFAULT 'pending'");
    }
};
