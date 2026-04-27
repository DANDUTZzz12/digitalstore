<?php

namespace App\Filament\Resources\Stocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variant.product.name')->label('Produk')->searchable(),
                TextColumn::make('variant.name')->label('Varian')->badge(),

                // Kredensial di-mask di list view. Untuk melihat / meng-copy nilai
                // plaintext, admin harus buka halaman Edit (yang akan men-decrypt
                // otomatis via cast 'encrypted' di model).
                TextColumn::make('email_or_phone')
                    ->label('Email/No HP')
                    ->formatStateUsing(fn ($state) => static::mask((string) $state))
                    ->copyable(false),

                TextColumn::make('password')
                    ->label('Password')
                    ->formatStateUsing(fn () => '••••••••')
                    ->copyable(false),

                IconColumn::make('is_sold')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle'),

                TextColumn::make('sold_at')->label('Terjual Pada')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Ditambahkan')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function mask(string $value): string
    {
        if ($value === '') {
            return '';
        }
        $length = mb_strlen($value);
        if ($length <= 4) {
            return str_repeat('•', $length);
        }

        return mb_substr($value, 0, 2).str_repeat('•', max(0, $length - 4)).mb_substr($value, -2);
    }
}
