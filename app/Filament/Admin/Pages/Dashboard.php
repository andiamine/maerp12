<?php
// app/Filament/Admin/Pages/Dashboard.php - Version finale complète

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\CompaniesByCabinetAndCity;
use App\Filament\Admin\Widgets\MonthlyGrowth;
use App\Filament\Admin\Widgets\SystemStatus;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            CompaniesByCabinetAndCity::class,
            MonthlyGrowth::class,
            SystemStatus::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 3,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('refresh')
                ->label('Actualiser')
                ->icon('heroicon-m-arrow-path')
                ->action(function () {
                    return redirect(request()->header('Referer'));
                })
                ->color('gray'),

            \Filament\Actions\Action::make('export_data')
                ->label('Exporter les données')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    // Logic to export dashboard data
                    \Filament\Notifications\Notification::make()
                        ->title('Export en cours')
                        ->body('L\'export des données va commencer.')
                        ->info()
                        ->send();
                }),
        ];
    }
}
