<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kategori')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    // Kita hapus tulisan "Set" di depan $set supaya tidak bentrok versi
                    ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug (Otomatis)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->readOnly(),
            ]);
    }
}