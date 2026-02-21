<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // وضعیت فروشندگی: none (هیچ), pending (در انتظار تایید), active (فعال), suspended (معلق), rejected (رد شده)
            $table->enum('seller_status', ['none', 'pending', 'active', 'suspended', 'rejected'])
                ->default('none')
                ->after('role');
            
            // تاریخ درخواست فروشندگی
            $table->timestamp('seller_requested_at')->nullable()->after('seller_status');
            
            // تاریخ تایید/رد فروشندگی
            $table->timestamp('seller_approved_at')->nullable()->after('seller_requested_at');
            
            // دلیل رد یا تعلیق
            $table->text('seller_rejection_reason')->nullable()->after('seller_approved_at');
            
            // اطلاعات درخواست فروشندگی (JSON)
            $table->json('seller_request_data')->nullable()->after('seller_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'seller_status',
                'seller_requested_at',
                'seller_approved_at',
                'seller_rejection_reason',
                'seller_request_data'
            ]);
        });
    }
};
