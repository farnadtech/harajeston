<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('fixed_amount', 15, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->timestamps();
            
            $table->unique('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_commissions');
    }
};
