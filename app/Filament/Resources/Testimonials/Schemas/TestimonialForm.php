<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(120),

                TextInput::make('role')
                    ->label('Peran / Posisi (opsional)')
                    ->maxLength(120)
                    ->placeholder('Pelanggan, Content Creator, dll.'),

                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        5 => '★★★★★ — 5',
                        4 => '★★★★☆ — 4',
                        3 => '★★★☆☆ — 3',
                        2 => '★★☆☆☆ — 2',
                        1 => '★☆☆☆☆ — 1',
                    ])
                    ->default(5)
                    ->required()
                    ->native(false),

                FileUpload::make('avatar')
                    ->label('Foto / Avatar (opsional)')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('testimonials')
                    ->visibility('public')
                    ->maxSize(1024)
                    ->helperText('Kalau dikosongkan akan tampil inisial dari nama.'),

                Textarea::make('content')
                    ->label('Isi Testimoni')
                    ->required()
                    ->rows(4)
                    ->maxLength(500)
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Urutan tampil')
                    ->numeric()
                    ->default(0)
                    ->helperText('Semakin kecil, semakin di atas.'),

                Toggle::make('is_active')
                    ->label('Aktif (tampil di homepage)')
                    ->default(true),
            ]);
    }
}
