<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use App\Models\ProductVariant;
use App\Models\Stock;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    // =========================================================================
    // 1. TAMBAHAN MESIN PENANGKAP SINYAL DARI CHECKBOX HTML (HACKER WAY)
    // =========================================================================
    protected function getListeners(): array
    {
        return array_merge(parent::getListeners(), [
            'hapusCentang' => 'prosesHapusCentang',
        ]);
    }

    public function prosesHapusCentang($ids = [])
    {
        // Menangkap ID baris yang dikirim dari tabel
        if (is_array($ids) && isset($ids['ids'])) {
            $ids = $ids['ids'];
        }

        if (empty($ids)) {
            return;
        }

        // 👇 PERUBAHAN UTAMA: Sekarang kita hapus berdasarkan 'id', BUKAN 'email_or_phone'
        $count = Stock::whereIn('id', $ids)->delete();

        Notification::make()
            ->success()
            ->title('Hapus Terpilih Sukses!')
            ->body("$count baris data berhasil dihapus dengan aman.")
            ->send();
    }
    // =========================================================================

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah 1 Stok'),

            // 1. TOMBOL BARU UNTUK CEK DOUBLE DROP
            Actions\Action::make('cek_duplikat')
                ->label('Cek Akun Double')
                ->color('warning')
                ->icon('heroicon-o-magnifying-glass-circle')
                ->form(function () {
                    // Karena kolom email_or_phone disimpan ter-enkripsi (AES-256-CBC
                    // dengan IV acak), ciphertext untuk plaintext yang sama selalu
                    // berbeda — GROUP BY di SQL tidak akan pernah ketemu duplikat.
                    // Solusi: load semua row, decrypt di PHP via Eloquent cast,
                    // lalu group di Collection.
                    $duplicates = Stock::query()
                        ->get(['id', 'email_or_phone'])
                        ->groupBy('email_or_phone')
                        ->filter(fn ($group) => $group->count() > 1)
                        ->map(fn ($group, $email) => [
                            'email_or_phone' => $email,
                            'total' => $group->count(),
                        ])
                        ->values();

                    if ($duplicates->isEmpty()) {
                        return [
                            Placeholder::make('aman')
                                ->label('Status Pengecekan')
                                ->content('✅ Aman! Tidak ditemukan akun yang double (Double Drop).'),
                        ];
                    }

                    // Susun daftar teks email yang double
                    $list = $duplicates->map(fn ($item) => "- {$item['email_or_phone']} (Ada {$item['total']} data)")->implode("\n");

                    return [
                        Textarea::make('hasil_duplikat')
                            ->label('Daftar Akun yang Double')
                            ->default($list)
                            ->rows(10)
                            ->disabled()
                            ->helperText('Silakan cari email di atas pada tabel, lalu hapus salah satunya jika itu tidak sengaja terinput.'),
                    ];
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup Panel'),

            // 2. TOMBOL BULK ADD (Versi Normal tanpa Auto-Blokir)
            Actions\Action::make('bulk_add')
                ->label('Bulk Tambah Stok')
                ->color('success')
                ->icon('heroicon-o-document-plus')
                ->form([
                    Select::make('product_variant_id')
                        ->label('Pilih Varian Produk')
                        ->options(
                            ProductVariant::with('product')->get()->mapWithKeys(function ($variant) {
                                return [$variant->id => "{$variant->product->name} - {$variant->name}"];
                            })
                        )
                        ->searchable()
                        ->required(),

                    Textarea::make('bulk_data')
                        ->label('Data Akun (Format: Email|Password|Info)')
                        ->rows(10)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $lines = explode("\n", str_replace("\r", '', $data['bulk_data']));
                    $count = 0;

                    DB::beginTransaction();
                    try {
                        foreach ($lines as $line) {
                            if (empty(trim($line))) {
                                continue;
                            }
                            $parts = explode('|', $line);

                            if (count($parts) >= 2) {
                                Stock::create([
                                    'product_variant_id' => $data['product_variant_id'],
                                    'email_or_phone' => trim($parts[0]),
                                    'password' => trim($parts[1]),
                                    'additional_info' => isset($parts[2]) ? trim($parts[2]) : null,
                                    'is_sold' => false,
                                ]);
                                $count++;
                            }
                        }
                        DB::commit();

                        Notification::make()->success()->title('Berhasil!')->body("Sukses tambah {$count} akun.")->send();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Notification::make()->danger()->title('Gagal!')->body('Terjadi kesalahan format.')->send();
                    }
                }),

            // 3. TOMBOL HAPUS MASSAL SAKTI (Bypass error tabel)
            Actions\Action::make('hapus_massal_custom')
                ->label('Hapus Massal')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->form([
                    Textarea::make('emails_to_delete')
                        ->label('Masukkan Email/Data yang mau dihapus')
                        ->helperText('Pisahkan dengan enter. Ketik "MANUAL" (tanpa tanda kutip) jika ingin menghapus semua akun bernama MANUAL. Ketik "HAPUS_SEMUA" jika ingin mereset seluruh brankas.')
                        ->rows(5)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $input = trim($data['emails_to_delete']);

                    // Fitur Rahasia: Hapus semua data.
                    // Pakai delete() bukan truncate() — truncate gagal di MySQL/InnoDB
                    // karena tabel orders punya FK stock_id; delete() respect FK
                    // ON DELETE SET NULL sehingga aman.
                    if ($input === 'HAPUS_SEMUA') {
                        $count = Stock::count();
                        Stock::query()->delete();
                        Notification::make()->success()->title('Reset Total!')->body("{$count} data berhasil disapu bersih.")->send();

                        return;
                    }

                    // Fitur Normal: Hapus berdasarkan kata/email yang diketik.
                    // Kolom email_or_phone ter-enkripsi (IV acak), jadi WHERE IN di
                    // SQL tidak akan pernah match plaintext. Decrypt via Eloquent,
                    // collect ID yang cocok, lalu hapus by ID.
                    $emails = collect(explode("\n", str_replace("\r", '', $input)))
                        ->map(fn ($e) => trim($e))
                        ->filter()
                        ->values();

                    $matchedIds = Stock::query()
                        ->get(['id', 'email_or_phone'])
                        ->filter(fn ($s) => $emails->contains($s->email_or_phone))
                        ->pluck('id')
                        ->all();

                    $deleted = $matchedIds === []
                        ? 0
                        : Stock::whereIn('id', $matchedIds)->delete();

                    Notification::make()
                        ->success()
                        ->title('Berhasil!')
                        ->body("Sukses menghapus {$deleted} akun dari database.")
                        ->send();
                }),
        ];
    }
}
