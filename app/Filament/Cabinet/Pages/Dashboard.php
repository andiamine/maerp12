<?php

namespace App\Filament\Cabinet\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Cabinet\Widgets\CabinetStatsOverview;
use App\Filament\Cabinet\Widgets\RecentCompanies;
use App\Filament\Cabinet\Widgets\CompanyGrowth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Tableau de bord';

    public function getHeaderWidgets(): array
    {
        return [
            CabinetStatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RecentCompanies::class,
            CompanyGrowth::class,
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
}
