<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput; // Kita hanya pakai Repeater untuk varian
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->directory('produk-images')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Utama'),
                Toggle::make('is_auto_send')
                    ->required(),

                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name') // Mengambil data dari tabel categories
                    ->searchable()
                    ->preload()
                    ->required(),

                // --- INI FITUR VARIANNYA ---
                Repeater::make('variants')
                    ->relationship() // Menyambung otomatis ke tabel product_variants
                    ->schema([
                        TextInput::make('name')
                            ->label('Durasi / Paket (Contoh: 1 Bulan)')
                            ->required(),
                        TextInput::make('price')
                            ->label('Harga Paket')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->columns(2) // Agar input nama dan harga bersebelahan
                    ->columnSpanFull()
                    ->createItemButtonLabel('Tambah Varian Baru'),
            ]);
    }
}
