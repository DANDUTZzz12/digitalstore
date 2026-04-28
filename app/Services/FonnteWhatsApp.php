<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use App\Support\Audit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Wrapper API Fonnte (https://fonnte.com) untuk auto-kirim pesan WA ke customer.
 * - Token diambil dari SiteSetting.fonnte_api_key (encrypted).
 * - Kalau token kosong / toggle off, kirim di-skip diam-diam (graceful).
 * - Setiap pengiriman ter-audit (sukses/gagal) supaya admin bisa trace.
 */
class FonnteWhatsApp
{
    public const ENDPOINT = 'https://api.fonnte.com/send';

    public const DEFAULT_TEMPLATE = "Halo, terima kasih sudah berbelanja di kami!\n\n".
        "Berikut detail akun untuk order *{{order_code}}*:\n".
        "Produk: {{product}}\n".
        "Paket: {{variant}}\n".
        "Email/No HP: {{email}}\n".
        "Password: {{password}}\n".
        "Info Tambahan: {{additional_info}}\n\n".
        'Harap simpan kredensial ini dan jangan dibagikan ke siapapun. '.
        'Jika ada kendala silakan balas pesan ini.';

    /**
     * Kirim kredensial akun untuk satu order ke nomor customer.
     * Return true jika request berhasil dikirim ke Fonnte (status 200 + status response = success).
     * Return false untuk semua kasus skip/gagal (toggle off, no token, no phone, no stock, http error).
     */
    public function sendCredentials(Order $order): bool
    {
        $site = SiteSetting::current();

        if (! $site->fonnte_auto_send_credentials || empty($site->fonnte_api_key)) {
            return false;
        }

        $phone = $this->normalizePhone((string) ($order->customer_phone ?? ''));
        if ($phone === '') {
            return false;
        }

        $stock = $order->stock; // Encrypted email/password/info accessible via cast.
        if (! $stock) {
            return false;
        }

        $template = trim((string) ($site->fonnte_credentials_template ?? '')) ?: self::DEFAULT_TEMPLATE;
        $message = strtr($template, [
            '{{order_code}}' => (string) $order->order_code,
            '{{product}}' => (string) optional($order->product)->name,
            '{{variant}}' => (string) optional($order->variant)->name,
            '{{email}}' => (string) $stock->email_or_phone,
            '{{password}}' => (string) $stock->password,
            '{{additional_info}}' => (string) ($stock->additional_info ?? '-'),
        ]);

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $site->fonnte_api_key])
                ->timeout(15)
                ->post(self::ENDPOINT, [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $body = $response->json() ?? [];
            $ok = $response->successful() && (($body['status'] ?? false) === true);

            Audit::log($ok ? 'whatsapp.sent' : 'whatsapp.failed', $order, [
                'channel' => 'fonnte',
                'phone' => $phone,
                'http_status' => $response->status(),
                'response' => $body,
            ]);

            return $ok;
        } catch (Throwable $e) {
            Log::error('Fonnte send failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            Audit::log('whatsapp.failed', $order, [
                'channel' => 'fonnte',
                'phone' => $phone,
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /** Normalisasi: 0xxx → 62xxx, +62xxx → 62xxx, kosongkan karakter selain digit. */
    public function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/[^0-9]/', '', $raw) ?? '';
        if ($digits === '') {
            return '';
        }
        if ($digits[0] === '0') {
            return '62'.substr($digits, 1);
        }

        return $digits;
    }
}
