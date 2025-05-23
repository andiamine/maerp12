<?php

namespace App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyCreationTask extends EditRecord
{
    protected static string $resource = CompanyCreationTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
