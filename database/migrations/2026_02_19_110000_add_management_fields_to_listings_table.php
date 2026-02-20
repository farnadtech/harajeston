<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
            if (!Schema::hasColumn('listings', 'condition')) {
                $table->string('condition')->nullable()->after('category');
            }
            if (!Schema::hasColumn('listings', 'tags')) {
                $table->json('tags')->nullable()->after('condition');
            }
            if (!Schema::hasColumn('listings', 'suspension_reason')) {
                $table->text('suspension_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('listings', 'auto_extend')) {
                $table->boolean('auto_extend')->default(true)->after('ends_at');
            }
            if (!Schema::hasColumn('listings', 'views')) {
                $table->integer('views')->default(0)->after('auto_extend');
            }
            if (!Schema::hasColumn('listings', 'favorites')) {
                $table->integer('favorites')->default(0)->after('views');
            }
            if (!Schema::hasColumn('listings', 'shares')) {
                $table->integer('shares')->default(0)->after('favorites');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'condition',
                'tags',
                'suspension_reason',
                'auto_extend',
                'views',
                'favorites',
                'shares'
            ]);
        });
    }
};
