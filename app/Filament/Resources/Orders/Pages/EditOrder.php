<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\Stock;
use App\Services\OrderFulfillment;
use App\Support\Audit;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /**
     * Tombol header: "Kirim Akun Manual" — muncul untuk order PAID yang
     * belum punya stock_id (mis. produk manual delivery, atau auto delivery
     * tapi stoknya kebetulan kosong saat user bayar).
     *
     * Behaviour: buat row baru di tabel stocks dengan kredensial yg di-input,
     * langsung tandai sold + assign ke order ini. Kredensial otomatis
     * tampil di invoice publik karena flow rendering sudah ada.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('manualDelivery')
                ->label('Kirim Akun Manual')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn () => $this->getRecord()->isPaid() && ! $this->getRecord()->stock_id)
                ->modalHeading('Kirim Akun Manual ke Pembeli')
                ->modalDescription('Kredensial akan disimpan terenkripsi di tabel stocks dan otomatis tampil di invoice publik order ini.')
                ->modalSubmitActionLabel('Kirim & Assign ke Order')
                ->form([
                    TextInput::make('email_or_phone')
                        ->label('Email / No HP Akun')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->label('Password Akun')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('additional_info')
                        ->label('Informasi Tambahan (opsional)')
                        ->rows(4)
                        ->helperText('Mis. PIN, profil yang dipakai, link aplikasi, instruksi khusus, dll.'),
                ])
                ->action(function (array $data): void {
                    /** @var Order $order */
                    $order = $this->getRecord();

                    DB::transaction(function () use ($order, $data): void {
                        $stock = Stock::create([
                            'product_variant_id' => $order->product_variant_id,
                            'email_or_phone' => $data['email_or_phone'],
                            'password' => $data['password'],
                            'additional_info' => $data['additional_info'] ?? null,
                            'is_sold' => true,
                            'sold_at' => now(),
                        ]);

                        $order->forceFill([
                            'stock_id' => $stock->id,
                            'paid_at' => $order->paid_at ?? now(),
                        ])->save();
                    });

                    Audit::log('order.manual_delivery', $order, [
                        'admin_user_id' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Akun berhasil dikirim ke pembeli.')
                        ->body('Kredensial sudah tampil di halaman invoice. Pembeli bisa menghubungi admin via tombol WhatsApp di invoice kalau butuh klarifikasi.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['stock_id']);
                }),
        ];
    }

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

        // Admin EditOrder = override eksplisit — boleh transisi dari status
        // terminal (cancelled/refunded/expired/failed) ke PAID.
        app(OrderFulfillment::class)->markPaidAndAssignStock(
            $record,
            [
                'source' => 'admin_manual',
                'admin_user_id' => auth()->id(),
            ],
            allowFromTerminalStates: true,
        );
        $record->refresh();

        if ($record->stock_id) {
            Notification::make()
                ->title('Stok berhasil di-assign ke order ini.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Order ditandai PAID, tapi tidak ada stok tersedia untuk varian ini.')
                ->body('Klik tombol "Kirim Akun Manual" di header untuk input akun langsung, atau tambahkan stok untuk varian terkait lalu simpan ulang order.')
                ->warning()
                ->send();
        }

        return $record;
    }
}
