<?php

namespace App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyCreationTasks extends ListRecords
{
    protected static string $resource = CompanyCreationTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('kanban_view')
                ->label('Vue Kanban')
                ->icon('heroicon-o-view-columns')
                ->url(CompanyCreationTaskResource::getUrl('kanban'))
                ->color('gray'),

            Actions\CreateAction::make(),
        ];
    }
}
