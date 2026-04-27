<?php

namespace App\Filament\Resources\Stocks;

use App\Filament\Resources\Stocks\Pages\CreateStock;
use App\Filament\Resources\Stocks\Pages\EditStock;
use App\Filament\Resources\Stocks\Pages\ListStocks;
use App\Filament\Resources\Stocks\Schemas\StockForm;
use App\Filament\Resources\Stocks\Tables\StocksTable;
use App\Models\Stock;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;   // <-- Ini ditambahkan agar PHP 8.4 tidak protes
use BackedEnum; // <-- Ini juga ditambahkan

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    // Tipe datanya kita tulis lengkap persis seperti permintaan PHP 8.4
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Stok';

    protected static ?string $navigationLabel = 'Stok Akun';

    public static function form(Schema $schema): Schema
    {
        return StockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStocks::route('/'),
            'create' => CreateStock::route('/create'),
            'edit' => EditStock::route('/{record}/edit'),
        ];
    }
}