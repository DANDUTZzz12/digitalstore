<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Kredensial terenkripsi Laravel (AES-256-CBC) bisa cukup panjang, jadi kita
// perbesar kolom yang semula VARCHAR menjadi TEXT.
return new class extends Migration
{
    public function up(): void
    {
        // SQLite tidak punya ALTER COLUMN TYPE. Kita skip kalau driver = sqlite
        // karena VARCHAR di SQLite dinamis (TYPE AFFINITY) dan sudah muat TEXT.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('stocks', function (Blueprint $table) {
            $table->text('email_or_phone')->nullable()->change();
            $table->text('password')->nullable()->change();
            // additional_info sudah TEXT di migrasi sebelumnya
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('stocks', function (Blueprint $table) {
            $table->string('email_or_phone')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }
};
