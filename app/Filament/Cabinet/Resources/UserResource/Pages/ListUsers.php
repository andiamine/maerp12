<?php

namespace App\Filament\Cabinet\Resources\UserResource\Pages;

use App\Filament\Cabinet\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->before(function () {
                    // Vérifier la limite d'utilisateurs
                    $cabinet = auth()->user()->cabinet;
                    if ($cabinet->users()->count() >= $cabinet->limite_utilisateurs) {
                        Notification::make()
                            ->title('Limite atteinte')
                            ->body('Vous avez atteint la limite d\'utilisateurs pour votre cabinet.')
                            ->danger()
                            ->send();

                        return false;
                    }
                }),
        ];
    }
}
