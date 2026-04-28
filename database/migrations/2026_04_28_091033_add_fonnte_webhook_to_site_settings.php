<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Nomor admin yg menerima notifikasi setiap ada order PAID.
            // Bisa diisi 0xxx atau 62xxx — service auto-normalize.
            $table->string('fonnte_admin_number', 32)->nullable();
            // Shared secret yang harus cocok dengan header X-Fonnte-Token di
            // request webhook masuk dari Fonnte. Kalau kosong, endpoint webhook
            // di-disable supaya tidak bisa dipanggil sembarangan.
            $table->string('fonnte_webhook_secret', 128)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['fonnte_admin_number', 'fonnte_webhook_secret']);
        });
    }
};
