<?php
namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Cabinet;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Cabinets
        $totalCabinets = Cabinet::count();
        $activeCabinets = Cabinet::where('statut', 'actif')->count();
        $newCabinetsThisMonth = Cabinet::whereMonth('created_at', now()->month)->count();

        // Sociétés
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('statut', 'active')->count();
        $newCompaniesThisMonth = Company::whereMonth('created_at', now()->month)->count();

        // Utilisateurs
        $totalUsers = User::count();
        $activeUsers = User::where('statut', 'actif')->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // Cabinets expirés
        $expiredCabinets = Cabinet::where('date_expiration', '<', now())->count();

        return [
            Stat::make('Cabinets Totaux', $totalCabinets)
                ->description($activeCabinets . ' actifs')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($activeCabinets === $totalCabinets ? 'success' : 'info')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('filament.admin.resources.cabinets.index')),

            Stat::make('Sociétés Totales', $totalCompanies)
                ->description($activeCompanies . ' actives')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color($activeCompanies === $totalCompanies ? 'success' : 'warning')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('filament.admin.resources.companies.index')),

            Stat::make('Utilisateurs Totaux', $totalUsers)
                ->description($activeUsers . ' actifs')
                ->descriptionIcon('heroicon-m-users')
                ->color($activeUsers === $totalUsers ? 'success' : 'danger')
                ->chart([3, 8, 5, 2, 12, 4, 12])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ])
                ->url(route('filament.admin.resources.users.index')),

            Stat::make('Nouveaux ce mois', $newCabinetsThisMonth)
                ->description('Cabinets créés')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Nouvelles ce mois', $newCompaniesThisMonth)
                ->description('Sociétés créées')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Cabinets Expirés', $expiredCabinets)
                ->description('Nécessitent renouvellement')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiredCabinets > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
