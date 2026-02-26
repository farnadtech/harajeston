<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cancelled_by')->nullable()->after('status'); // 'buyer' or 'seller'
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            $table->decimal('cancellation_penalty', 15, 2)->default(0)->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancellation_penalty');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancelled_by', 'cancelled_at', 'cancellation_penalty', 'cancellation_reason']);
        });
    }
};
