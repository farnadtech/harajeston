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
        // به‌روزرسانی فروشنده‌های موجود
        DB::table('users')
            ->where('role', 'seller')
            ->where(function($q) {
                $q->whereNull('seller_status')
                  ->orWhere('seller_status', 'none');
            })
            ->update([
                'seller_status' => 'active',
                'seller_approved_at' => DB::raw('created_at'),
                'seller_requested_at' => DB::raw('created_at'),
            ]);

        // به‌روزرسانی خریدارهای موجود
        DB::table('users')
            ->where('role', 'buyer')
            ->where(function($q) {
                $q->whereNull('seller_status')
                  ->orWhere('seller_status', '!=', 'none');
            })
            ->update([
                'seller_status' => 'none',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
