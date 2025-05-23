<?php

namespace App\Filament\Cabinet\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;

class CabinetStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $cabinet = Filament::getTenant();

        if (!$cabinet) {
            return [];
        }

        // Sociétés
        $totalCompanies = $cabinet->companies()->count();
        $activeCompanies = $cabinet->companies()
            ->where('statut', 'active')
            ->count();
        $newCompaniesThisMonth = $cabinet->companies()
            ->whereMonth('created_at', now()->month)
            ->count();

        // Utilisateurs
        $totalUsers = $cabinet->users()->count();
        $activeUsers = $cabinet->users()
            ->where('statut', 'actif')
            ->count();

        // Limites
        $companiesLimit = $cabinet->limite_societes;
        $usersLimit = $cabinet->limite_utilisateurs;
        $companiesUsagePercent = ($totalCompanies / $companiesLimit) * 100;
        $usersUsagePercent = ($totalUsers / $usersLimit) * 100;

        return [
            Stat::make('Sociétés', $totalCompanies . ' / ' . $companiesLimit)
                ->description($activeCompanies . ' actives')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color($companiesUsagePercent >= 90 ? 'danger' : ($companiesUsagePercent >= 70 ? 'warning' : 'success'))
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Utilisateurs', $totalUsers . ' / ' . $usersLimit)
                ->description($activeUsers . ' actifs')
                ->descriptionIcon('heroicon-m-users')
                ->color($usersUsagePercent >= 90 ? 'danger' : ($usersUsagePercent >= 70 ? 'warning' : 'success'))
                ->chart([3, 8, 5, 2, 12, 4, 12]),

            Stat::make('Nouvelles ce mois', $newCompaniesThisMonth)
                ->description('Sociétés créées')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),
        ];
    }
}
