<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\FonnteWhatsApp;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook receiver untuk pesan masuk dari customer via Fonnte.
 *
 * Setup di Fonnte dashboard:
 * - URL: https://<domain>/webhooks/fonnte
 * - Method: POST (form-encoded, sesuai default Fonnte)
 * - Tambahkan header `X-Fonnte-Token: <secret>` (sama dengan
 *   SiteSetting.fonnte_webhook_secret) — untuk auth.
 *
 * Behavior:
 * - Validasi shared-secret di header.
 * - Cari Order yang related (dari order_code di pesan, atau by phone).
 * - Audit log message masuk.
 * - Forward ringkasan ke admin number kalau diset.
 */
class FonnteWebhookController extends Controller
{
    public function handle(Request $request, FonnteWhatsApp $wa): JsonResponse
    {
        $site = SiteSetting::current();

        // Disabled jika secret belum diset di Site Settings.
        $expected = trim((string) ($site->fonnte_webhook_secret ?? ''));
        if ($expected === '') {
            return response()->json(['ok' => false, 'reason' => 'webhook_disabled'], 403);
        }

        $provided = (string) $request->header('X-Fonnte-Token', '');
        if (! hash_equals($expected, $provided)) {
            Log::warning('Fonnte webhook: invalid token', ['ip' => $request->ip()]);

            return response()->json(['ok' => false, 'reason' => 'invalid_token'], 401);
        }

        // Fonnte payload (form-encoded): device, sender, message, member, name, etc.
        $sender = (string) $request->input('sender', '');
        $message = (string) $request->input('message', '');
        $name = (string) $request->input('name', '');

        if ($sender === '' || $message === '') {
            return response()->json(['ok' => false, 'reason' => 'invalid_payload'], 422);
        }

        // Coba match order: cari order_code di body pesan (regex AKH-...).
        $order = null;
        if (preg_match('/AKH-[A-Z0-9-]+/i', $message, $m)) {
            $order = Order::where('order_code', strtoupper($m[0]))->first();
        }

        // Fallback: cari order by customer_phone (normalisasi sender).
        if (! $order) {
            $normalized = $wa->normalizePhone($sender);
            $order = Order::where('customer_phone', 'LIKE', '%'.substr($normalized, -10).'%')
                ->latest()
                ->first();
        }

        Audit::log('whatsapp.received', $order, [
            'channel' => 'fonnte',
            'sender' => $sender,
            'name' => $name,
            'message' => mb_substr($message, 0, 500),
        ]);

        // Forward ke admin kalau admin number diset.
        $adminPhone = $wa->normalizePhone((string) ($site->fonnte_admin_number ?? ''));
        if ($adminPhone !== '') {
            $orderRef = $order ? '#'.$order->order_code : '(tidak terdeteksi)';
            $forward = "[Pesan masuk]\nDari: {$name} ({$sender})\nOrder: {$orderRef}\nIsi: {$message}";
            $wa->send($order, $adminPhone, $forward, isAdmin: true);
        }

        return response()->json(['ok' => true]);
    }
}
