<?php

namespace App\Filament\Resources\Stocks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class StockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_variant_id')
                    ->label('Varian Produk')
                    ->relationship('variant', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - {$record->name}")
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('email_or_phone')
                    ->label('Email / No HP')
                    ->required(),

                TextInput::make('password')
                    ->label('Password Akun')
                    ->required(),

                Textarea::make('additional_info')
                    ->label('Keterangan / Info Tambahan')
                    ->columnSpanFull(),

                Toggle::make('is_sold')
                    ->label('Status Terjual')
                    ->default(false),
            ]);
    }
}