<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Branding
            $table->string('store_name')->default('Akhpremium Store');
            $table->string('tagline')->default('Akun Premium Legal, Harga Ramah.');
            $table->string('logo_path')->nullable();
            $table->string('brand_color', 16)->default('#7c3aed');   // primary (purple-600)
            $table->string('accent_color', 16)->default('#06b6d4');  // accent (cyan-500)

            // Hero
            $table->string('hero_title')->default('Akun Premium Legal, Harga Ramah.');
            $table->text('hero_subtitle')->nullable();

            // Kontak / live chat
            $table->string('contact_email')->nullable();
            $table->string('wa_number', 32)->nullable();      // tanpa +, contoh: 6281234567890
            $table->text('wa_default_message')->nullable();   // pesan otomatis click-to-chat

            // Social
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('telegram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('whatsapp_channel_url')->nullable();

            // Konten halaman info (HTML)
            $table->longText('how_to_order_html')->nullable();
            $table->longText('terms_html')->nullable();
            $table->longText('about_html')->nullable();

            // Footer
            $table->text('footer_about')->nullable();
            $table->string('support_hours')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
