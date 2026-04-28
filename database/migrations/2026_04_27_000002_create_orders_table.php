<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 40)->unique();

            // Relasi produk/varian
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();

            // Stok yang sudah di-assign ketika order sukses (nullable sampai PAID)
            $table->foreignId('stock_id')->nullable()->constrained()->nullOnDelete();

            // Data pembeli (tanpa login — minimal kontak)
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Nominal
            $table->unsignedInteger('amount');
            $table->unsignedInteger('fee')->default(0);
            $table->unsignedInteger('total_payment');

            // Payment
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_ref', 100)->nullable()->index();

            // Status: pending, paid, failed, expired, cancelled, refunded
            $table->string('status', 20)->default('pending')->index();

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
