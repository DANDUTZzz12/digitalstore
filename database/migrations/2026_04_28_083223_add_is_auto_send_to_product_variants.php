<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Override per-varian. NULL = ikut setting product->is_auto_send.
            // Pakai nullable boolean supaya backward-compatible — varian lama
            // tetap pakai default produk tanpa migrasi data.
            $table->boolean('is_auto_send')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('is_auto_send');
        });
    }
};
