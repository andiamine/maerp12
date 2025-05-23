<?php

namespace App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;

class CreateCompanyCreationTask extends CreateRecord
{
    protected static string $resource = CompanyCreationTaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        $data['position'] = 0;

        // Set progress percentage based on completed steps
        $data['progress_percentage'] = 0;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
