<?php

namespace App\Services;

use App\Models\Order;
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
     * @return bool true jika order jadi PAID (termasuk kasus stok habis),
     *              false jika order tidak bisa diproses.
     */
    public function markPaidAndAssignStock(Order $order, array $context = []): bool
    {
        return DB::transaction(function () use ($order, $context) {
            /** @var Order $locked */
            $locked = Order::lockForUpdate()->find($order->id);
            if (! $locked) {
                return false;
            }

            // Idempotent: jika sudah paid, tidak ada yang perlu dikerjakan.
            if ($locked->isPaid()) {
                return true;
            }

            // Pilih stok available paling lama (FIFO) dan kunci baris-nya.
            $stock = Stock::where('product_variant_id', $locked->product_variant_id)
                ->where('is_sold', false)
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            $locked->status = Order::STATUS_PAID;
            $locked->paid_at = now();
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

            Audit::log('order.paid', $locked, $context);

            return true;
        });
    }
}
