<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Order')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_code')->disabled(),
                        Select::make('status')
                            ->options([
                                Order::STATUS_PENDING => 'Pending',
                                Order::STATUS_PAID => 'Paid',
                                Order::STATUS_FAILED => 'Failed',
                                Order::STATUS_EXPIRED => 'Expired',
                                Order::STATUS_CANCELLED => 'Cancelled',
                                Order::STATUS_REFUNDED => 'Refunded',
                            ])
                            ->required(),
                        TextInput::make('customer_email')->email()->disabled(),
                        TextInput::make('customer_phone')->disabled(),
                        TextInput::make('total_payment')->prefix('Rp')->disabled(),
                        TextInput::make('payment_method')->disabled(),
                        TextInput::make('payment_ref')->disabled(),
                        DateTimePicker::make('paid_at')->disabled(),
                        DateTimePicker::make('expired_at')->disabled(),
                    ]),

                // Section "Akun Terkirim" — muncul kalau order sudah punya stock_id.
                // Tampilkan kredensial yang ter-assign supaya admin bisa lihat & follow-up.
                Section::make('Akun Terkirim')
                    ->description('Kredensial yang sudah ter-assign ke order ini. Ter-encrypt di DB.')
                    ->icon('heroicon-o-key')
                    ->visible(fn ($record) => $record && $record->stock_id)
                    ->columns(2)
                    ->schema([
                        TextInput::make('stock_email')
                            ->label('Email / No HP Akun')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(fn (TextInput $component, $record) => $component->state(optional($record?->stock)->email_or_phone)),
                        TextInput::make('stock_password')
                            ->label('Password Akun')
                            ->disabled()
                            ->dehydrated(false)
                            ->revealable()
                            ->password()
                            ->afterStateHydrated(fn (TextInput $component, $record) => $component->state(optional($record?->stock)->password)),
                        Textarea::make('stock_additional_info')
                            ->label('Informasi Tambahan')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(3)
                            ->columnSpanFull()
                            ->afterStateHydrated(fn (Textarea $component, $record) => $component->state(optional($record?->stock)->additional_info)),
                        Placeholder::make('stock_meta')
                            ->label('Tanggal Dikirim')
                            ->columnSpanFull()
                            ->content(fn ($record): string|Htmlable|null => $record?->stock?->sold_at
                                ? $record->stock->sold_at->translatedFormat('d M Y H:i').' WIB · stock #'.$record->stock_id
                                : null),
                    ]),

                // Section "Kirim Akun Manual" — INLINE form, muncul saat order PAID
                // tapi belum ada stock_id. Lebih obvious daripada hanya tombol header.
                Section::make('Kirim Akun Manual ke Pembeli')
                    ->description('Order ini PAID tapi belum ada akun yang ter-assign. Isi form ini → kredensial otomatis tampil di invoice + dikirim ke WA customer (kalau Fonnte aktif).')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn ($record) => $record && $record->isPaid() && ! $record->stock_id)
                    ->columns(2)
                    ->schema([
                        Placeholder::make('manual_delivery_hint')
                            ->label('')
                            ->columnSpanFull()
                            ->content(new HtmlString(
                                '<div class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">'.
                                'Klik tombol <strong>"Kirim Akun Manual"</strong> di pojok kanan atas halaman ini untuk membuka form input kredensial.'.
                                '</div>'
                            )),
                    ]),
            ]);
    }
}
