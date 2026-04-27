<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'store_name',
        'tagline',
        'logo_path',
        'brand_color',
        'accent_color',
        'hero_title',
        'hero_subtitle',
        'contact_email',
        'wa_number',
        'wa_default_message',
        'instagram_url',
        'tiktok_url',
        'telegram_url',
        'facebook_url',
        'whatsapp_channel_url',
        'how_to_order_html',
        'terms_html',
        'about_html',
        'footer_about',
        'support_hours',
    ];

    /** Singleton pattern: ambil row pertama, atau buat default. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'store_name' => config('app.name', 'Akhpremium Store'),
        ]);
    }

    public function waLink(?string $message = null): ?string
    {
        if (! $this->wa_number) {
            return null;
        }
        $msg = $message ?? $this->wa_default_message ?? 'Halo admin, saya butuh bantuan.';

        return 'https://wa.me/'.$this->wa_number.'?text='.rawurlencode($msg);
    }
}
