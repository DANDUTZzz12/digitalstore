<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Models\Order;
use App\Models\Stock;
use App\Services\FonnteWhatsApp;
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
                // Tombol header LEGACY untuk order single-item lama (tanpa OrderItem).
                // Multi-item: form per-item di OrderForm sudah meng-cover manual delivery.
                ->visible(fn () => $this->getRecord()->isPaid()
                    && ! $this->getRecord()->stock_id
                    && ! $this->getRecord()->items()->exists())
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

                    // Auto-kirim ke WA customer kalau Fonnte aktif. Skip diam-diam
                    // kalau toggle off / API key kosong / no phone.
                    $waSent = app(FonnteWhatsApp::class)->sendCredentials(
                        $order->fresh(['stock', 'product', 'variant'])
                    );

                    Notification::make()
                        ->title('Akun berhasil dikirim ke pembeli.')
                        ->body($waSent
                            ? 'Kredensial sudah tampil di invoice publik dan dikirim ke WhatsApp customer via Fonnte.'
                            : 'Kredensial sudah tampil di invoice publik. (Auto-kirim WA dilewati — cek Site Settings → Fonnte kalau ingin aktifkan.)'
                        )
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
        // Ekstrak state Repeater "items" SEBELUM parent save: kita butuh field
        // manual_email_or_phone / manual_password / manual_additional_info untuk
        // bikin Stock baru. Field-field tsb tidak fillable di OrderItem jadi tidak
        // berpengaruh ke save relationship.
        $itemsState = $data['items'] ?? [];
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

        // Default path (tanpa transisi PAID) — jalan parent dulu, lalu commit
        // manual delivery per item kalau admin mengisi kredensial di Repeater.
        if (! $becomingPaid && ! $rescuePaidWithoutStock) {
            $updated = parent::handleRecordUpdate($record, $data);
            $this->commitItemManualDeliveries($updated, $itemsState);

            return $updated;
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

        // Setelah fulfillment, juga commit kredensial manual yang admin input
        // di Repeater (kasus: order multi-item yang stoknya kosong, admin
        // langsung isi kredensial di form yang sama).
        $this->commitItemManualDeliveries($record, $itemsState);
        $record->refresh();

        $hasItems = $record->items()->exists();
        $allDelivered = $hasItems
            ? $record->items()->whereNull('stock_id')->doesntExist()
            : (bool) $record->stock_id;

        if ($allDelivered) {
            Notification::make()
                ->title('Stok berhasil di-assign ke order ini.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Order ditandai PAID, tapi sebagian item belum punya akun.')
                ->body('Isi kolom "Email/Password" pada item yang masih "Belum Dikirim" lalu klik Save lagi.')
                ->warning()
                ->send();
        }

        return $record;
    }

    /**
     * Loop state Repeater items, untuk setiap row yang ada manual_email_or_phone +
     * manual_password, buat Stock baru terenkripsi + assign ke OrderItem.stock_id,
     * lalu kirim WA via Fonnte (kalau diaktifkan).
     */
    protected function commitItemManualDeliveries(Order $order, array $itemsState): void
    {
        $assigned = OrderForm::commitManualDeliveries($order, $itemsState);
        if ($assigned <= 0) {
            return;
        }

        Audit::log('order.manual_delivery', $order, [
            'admin_user_id' => auth()->id(),
            'items_assigned' => $assigned,
        ]);

        $waSent = app(FonnteWhatsApp::class)->sendCredentials(
            $order->fresh(['items.stock', 'items.product', 'items.variant', 'product', 'variant', 'stock'])
        );

        Notification::make()
            ->title($assigned.' akun berhasil dikirim ke pembeli.')
            ->body($waSent
                ? 'Kredensial sudah tampil di invoice publik dan dikirim ke WhatsApp customer via Fonnte.'
                : 'Kredensial sudah tampil di invoice publik. (Auto-kirim WA dilewati — cek Site Settings → Fonnte kalau ingin aktifkan.)'
            )
            ->success()
            ->send();
    }
}
