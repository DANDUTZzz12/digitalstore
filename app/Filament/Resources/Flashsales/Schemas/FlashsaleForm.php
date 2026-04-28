<?php

namespace App\Filament\Resources\Flashsales\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FlashsaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Promo (opsional)')
                    ->placeholder('Promo Akhir Bulan')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('product_variant_id')
                    ->label('Produk / Varian')
                    ->relationship(
                        name: 'variant',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->with('product'),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => ($record->product?->name ?? '—').' — '.$record->name.' (Rp '.number_format($record->price, 0, ',', '.').')')
                    ->searchable(['name'])
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('flash_price')
                    ->label('Harga Flash Sale (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                TextInput::make('quota')
                    ->label('Kuota (jumlah pcs)')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                DateTimePicker::make('start_at')
                    ->label('Mulai')
                    ->seconds(false)
                    ->required(),

                DateTimePicker::make('end_at')
                    ->label('Berakhir')
                    ->seconds(false)
                    ->required()
                    ->after('start_at'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
