<?php

namespace App\Services;

use App\Models\Flashsale;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Support\Audit;
use Illuminate\Support\Facades\DB;

/**
 * Mengubah Order menjadi PAID secara atomik + assign stok yang available.
 * Semua pemanggil WAJIB lewat sini agar idempotent dan race-free.
 */
class OrderFulfillment
{
    /**
     * @param  array<string,mixed>  $context  Detail untuk audit log (metode bayar,
     *                                        amount asli dari gateway, source, dll).
     * @param  bool  $allowFromTerminalStates  Bila true, izinkan transisi dari status
     *                                         non-pending/non-paid (cancelled, refunded,
     *                                         expired, failed) ke PAID. Hanya boleh
     *                                         dipakai dari admin EditOrder yang merupakan
     *                                         override eksplisit.
     * @return bool true jika order jadi PAID (termasuk kasus stok habis),
     *              false jika order tidak bisa diproses.
     */
    public function markPaidAndAssignStock(
        Order $order,
        array $context = [],
        bool $allowFromTerminalStates = false,
    ): bool {
        $assignedStock = false;
        $result = DB::transaction(function () use ($order, $context, $allowFromTerminalStates, &$assignedStock) {
            /** @var Order $locked */
            $locked = Order::lockForUpdate()->find($order->id);
            if (! $locked) {
                return false;
            }

            $wasAlreadyPaid = $locked->isPaid();

            // Idempotent: kalau sudah paid DAN stok sudah ter-assign, tidak ada yang perlu dikerjakan.
            if ($wasAlreadyPaid && $locked->stock_id) {
                return true;
            }

            // Guard race condition (TOCTOU): pemanggil mengecek status di luar
            // transaction — bisa berubah saat verifikasi via API berlangsung
            // (Pakasir sampai ~10 detik). Setelah lock, tolak transisi dari
            // status terminal (cancelled/refunded/expired/failed) kecuali admin
            // memberi override eksplisit lewat parameter $allowFromTerminalStates.
            if (! $locked->isPending() && ! $wasAlreadyPaid && ! $allowFromTerminalStates) {
                Audit::log('order.transition_blocked', $locked, array_merge($context, [
                    'current_status' => $locked->status,
                    'reason' => 'not_pending_or_paid',
                ]));

                return false;
            }

            // Pilih stok available paling lama (FIFO) dan kunci baris-nya.
            // Jalan walaupun order sudah paid (kasus: admin manual mark paid duluan,
            // baru menambahkan stok — ini "rescue" agar stok tetap auto-assigned).
            $stock = Stock::where('product_variant_id', $locked->product_variant_id)
                ->where('is_sold', false)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $wasAlreadyPaid) {
                $locked->status = Order::STATUS_PAID;
                $locked->paid_at = now();
            }
            if (! empty($context['payment_method'])) {
                $locked->payment_method = (string) $context['payment_method'];
            }

            if ($stock) {
                $stock->is_sold = true;
                $stock->sold_at = now();
                $stock->save();

                $locked->stock_id = $stock->id;

                Audit::log('stock.delivered', $locked, [
                    'stock_id' => $stock->id,
                    'variant_id' => $locked->product_variant_id,
                ]);
            } else {
                // Order tetap PAID — admin perlu deliver manual.
                Audit::log('stock.out_of_stock', $locked, [
                    'variant_id' => $locked->product_variant_id,
                    'note' => 'Pembayaran sukses tapi stok otomatis kosong.',
                ]);
            }

            $locked->save();
            $assignedStock = (bool) $stock;

            // Counter flashsale + sold_count produk hanya di-increment sekali,
            // saat transisi pertama kali ke PAID. Hindari double-count saat
            // re-run untuk rescue stock assignment.
            if (! $wasAlreadyPaid) {
                if ($locked->product_variant_id) {
                    $fs = Flashsale::active()
                        ->where('product_variant_id', $locked->product_variant_id)
                        ->lockForUpdate()
                        ->first();
                    if ($fs && $locked->amount === (int) $fs->flash_price) {
                        $fs->increment('sold');
                    }
                }
                if ($locked->product_id) {
                    Product::whereKey($locked->product_id)->increment('sold_count');
                }

                Audit::log('order.paid', $locked, $context);
            }

            return true;
        });

        // Auto-kirim kredensial via Fonnte WA — di luar transaction supaya HTTP call
        // tidak block lock DB. Service handle exception sendiri (return false, gak throw).
        if ($result && $assignedStock) {
            app(FonnteWhatsApp::class)->sendCredentials($order->fresh(['stock', 'product', 'variant']));
        }

        return $result;
    }
}
