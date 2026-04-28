<?php

namespace App\Filament\Resources\Flashsales;

use App\Filament\Resources\Flashsales\Pages\CreateFlashsale;
use App\Filament\Resources\Flashsales\Pages\EditFlashsale;
use App\Filament\Resources\Flashsales\Pages\ListFlashsales;
use App\Filament\Resources\Flashsales\Schemas\FlashsaleForm;
use App\Filament\Resources\Flashsales\Tables\FlashsalesTable;
use App\Models\Flashsale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FlashsaleResource extends Resource
{
    protected static ?string $model = Flashsale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Flash Sale';

    protected static ?string $modelLabel = 'Flash Sale';

    protected static ?string $pluralModelLabel = 'Flash Sales';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FlashsaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FlashsalesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFlashsales::route('/'),
            'create' => CreateFlashsale::route('/create'),
            'edit' => EditFlashsale::route('/{record}/edit'),
        ];
    }
}
