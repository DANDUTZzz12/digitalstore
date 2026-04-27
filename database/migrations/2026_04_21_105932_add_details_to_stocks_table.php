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
        Schema::table('stocks', function (Blueprint $table) {
            // Tambahkan kolom yang belum ada
            if (! Schema::hasColumn('stocks', 'product_variant_id')) {
                $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('stocks', 'email_or_phone')) {
                $table->string('email_or_phone')->nullable();
            }
            if (! Schema::hasColumn('stocks', 'password')) {
                $table->string('password')->nullable();
            }
            if (! Schema::hasColumn('stocks', 'additional_info')) {
                $table->text('additional_info')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            //
        });
    }
};
