<?php

namespace App\Filament\Cabinet\Resources\CompanyResource\Pages;

use App\Filament\Cabinet\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->before(function () {
                    // Vérifier la limite de sociétés
                    $cabinet = auth()->user()->cabinet;
                    if ($cabinet->companies()->count() >= $cabinet->limite_societes) {
                        Notification::make()
                            ->title('Limite atteinte')
                            ->body('Vous avez atteint la limite de sociétés pour votre cabinet.')
                            ->danger()
                            ->send();

                        return false;
                    }
                }),
        ];
    }
}
