<?php

namespace App\Filament\Resources\Flashsales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FlashsalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variant.product.name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('variant.name')
                    ->label('Paket'),
                TextColumn::make('flash_price')
                    ->label('Harga Flash')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('quota')
                    ->label('Kuota')
                    ->numeric(),
                TextColumn::make('sold')
                    ->label('Terjual')
                    ->numeric()
                    ->color('warning'),
                TextColumn::make('start_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('end_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('end_at', 'asc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
