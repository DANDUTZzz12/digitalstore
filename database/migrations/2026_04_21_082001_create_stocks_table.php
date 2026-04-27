<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
        
        // Kolom detail stok
        $table->string('email_or_phone'); 
        $table->string('password');
        $table->text('additional_info')->nullable(); // Untuk keterangan tambahan
        
        $table->boolean('is_sold')->default(false); 
        $table->timestamp('sold_at')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};