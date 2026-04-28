<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // API token Fonnte (di-encrypt). Kalau kosong, fitur kirim WA otomatis di-skip.
            $table->text('fonnte_api_key')->nullable();
            // Master toggle: kirim kredensial via WA setelah PAID?
            $table->boolean('fonnte_auto_send_credentials')->default(false);
            // Template pesan kredensial (opsional). Placeholder yang didukung:
            // {{order_code}} {{product}} {{variant}} {{email}} {{password}} {{additional_info}}
            $table->text('fonnte_credentials_template')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['fonnte_api_key', 'fonnte_auto_send_credentials', 'fonnte_credentials_template']);
        });
    }
};
