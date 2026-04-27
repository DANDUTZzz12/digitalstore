<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            if (! Schema::hasColumn('products', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_auto_send');
            }
            if (! Schema::hasColumn('products', 'sold_count')) {
                $table->unsignedInteger('sold_count')->default(0)->after('is_best_seller');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'short_description')) {
                $table->dropColumn('short_description');
            }
            if (Schema::hasColumn('products', 'is_best_seller')) {
                $table->dropColumn('is_best_seller');
            }
            if (Schema::hasColumn('products', 'sold_count')) {
                $table->dropColumn('sold_count');
            }
        });
    }
};
