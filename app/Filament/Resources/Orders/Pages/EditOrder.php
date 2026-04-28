<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\OrderFulfillment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Saat admin manual mengubah status order ke "paid" lewat form Filament,
     * routekan ke OrderFulfillment supaya stok otomatis di-assign + audit log
     * tertulis. Juga menjalankan rescue assignment saat order sudah paid tapi
     * stock_id-nya masih null (kasus: status sudah di-update di rilis sebelumnya).
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Order $record */
        $incomingStatus = $data['status'] ?? $record->status;

        // Transisi pertama ke PAID — jalankan fulfillment + assign stok.
        $becomingPaid = $incomingStatus === Order::STATUS_PAID
            && $record->status !== Order::STATUS_PAID;

        // Order sudah PAID, tidak ada perubahan status, tapi stock_id masih null
        // (rescue case: stok ditambahkan setelah pembayaran). Hanya rescue saat
        // admin tetap mempertahankan status PAID — kalau admin justru mengubah
        // status ke refunded/cancelled/dll, kita HARUS hormati perubahan itu
        // dan jalankan flow normal supaya status tidak hilang diam-diam.
        $rescuePaidWithoutStock = $incomingStatus === Order::STATUS_PAID
            && $record->isPaid()
            && ! $record->stock_id;

        if (! $becomingPaid && ! $rescuePaidWithoutStock) {
            return parent::handleRecordUpdate($record, $data);
        }

        // Untuk transisi ke paid: jangan biarkan parent overwrite status secara
        // mentah. Simpan field non-status saja, lalu serahkan transisi status
        // + stock assignment ke OrderFulfillment supaya counter & audit ikut.
        $dataWithoutStatus = $data;
        unset($dataWithoutStatus['status'], $dataWithoutStatus['paid_at']);
        $record->fill($dataWithoutStatus);
        $record->save();

        app(OrderFulfillment::class)->markPaidAndAssignStock($record, [
            'source' => 'admin_manual',
            'admin_user_id' => auth()->id(),
        ]);
        $record->refresh();

        if ($record->stock_id) {
            Notification::make()
                ->title('Stok berhasil di-assign ke order ini.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Order ditandai PAID, tapi tidak ada stok tersedia untuk varian ini.')
                ->body('Tambahkan stok untuk varian terkait, lalu simpan ulang order untuk memicu auto-assign.')
                ->warning()
                ->send();
        }

        return $record;
    }
}
