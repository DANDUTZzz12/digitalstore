<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_description')
                    ->label('Deskripsi Singkat (tampil di kartu produk)')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi Lengkap')
                    ->rows(5)
                    ->columnSpanFull(),
                RichEditor::make('terms_html')
                    ->label('Syarat & Ketentuan (SNK)')
                    ->helperText('Tampil di halaman detail produk di bawah deskripsi. Boleh kosongkan kalau tidak ada SNK khusus.')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike', 'link',
                        'bulletList', 'orderedList', 'h2', 'h3', 'blockquote',
                        'codeBlock', 'undo', 'redo',
                    ])
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Foto Produk')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('produk-images')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->label('Harga Utama (display)'),

                Toggle::make('is_auto_send')
                    ->label('Auto-Delivery (kirim akun otomatis dari stok)')
                    ->default(true),
                Toggle::make('is_best_seller')
                    ->label('Tandai sebagai Best Seller')
                    ->default(false),

                Repeater::make('variants')
                    ->label('Paket / Varian')
                    ->relationship()
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
                    ->columns(2)
                    ->columnSpanFull()
                    ->createItemButtonLabel('Tambah Varian Baru'),
            ]);
    }
}
