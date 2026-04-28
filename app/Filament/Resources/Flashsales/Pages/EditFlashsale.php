<?php

namespace App\Filament\Resources\Flashsales\Pages;

use App\Filament\Resources\Flashsales\FlashsaleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFlashsale extends EditRecord
{
    protected static string $resource = FlashsaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
