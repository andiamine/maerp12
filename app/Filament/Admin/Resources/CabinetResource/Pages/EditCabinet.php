<?php

namespace App\Filament\Admin\Resources\CabinetResource\Pages;

use App\Filament\Admin\Resources\CabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCabinet extends EditRecord
{
    protected static string $resource = CabinetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
