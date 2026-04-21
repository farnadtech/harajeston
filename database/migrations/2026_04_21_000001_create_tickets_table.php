<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            // گیرنده: null یعنی ادمین
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            // حراجی مرتبط (اجباری)
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->string('subject');
            // نوع تیکت: buyer_to_seller, buyer_to_admin, seller_to_buyer, seller_to_admin
            $table->enum('type', ['buyer_to_seller', 'buyer_to_admin', 'seller_to_buyer', 'seller_to_admin']);
            $table->enum('status', ['open', 'answered', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index(['creator_id', 'status']);
            $table->index(['recipient_id', 'status']);
            $table->index('listing_id');
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
    }
};
