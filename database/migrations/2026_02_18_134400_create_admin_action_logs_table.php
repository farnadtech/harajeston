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
        Schema::create('admin_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // e.g., 'cancel_auction', 'release_deposit', 'ban_user'
            $table->string('target_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('context')->nullable(); // Additional data about the action
            $table->text('reason')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('admin_id');
            $table->index(['target_type', 'target_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_action_logs');
    }
};
