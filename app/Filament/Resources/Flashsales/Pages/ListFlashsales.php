<?php

namespace App\Filament\Resources\Flashsales\Pages;

use App\Filament\Resources\Flashsales\FlashsaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFlashsales extends ListRecords
{
    protected static string $resource = FlashsaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
