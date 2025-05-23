<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\MenuItem;
use App\Models\Cabinet;

class CabinetPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('cabinet')
            ->path('cabinet')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Cabinet/Resources'), for: 'App\\Filament\\Cabinet\\Resources')
            ->discoverPages(in: app_path('Filament/Cabinet/Pages'), for: 'App\\Filament\\Cabinet\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Cabinet/Widgets'), for: 'App\\Filament\\Cabinet\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // Multi-tenancy configuration
            ->tenant(Cabinet::class)
            ->tenantRegistration(false) // Users can't create cabinets themselves
            ->tenantProfile(\App\Filament\Cabinet\Pages\EditCabinetProfile::class) // Allow cabinet profile editing
            ->tenantMenuItems([
                MenuItem::make()
                    ->label('Paramètres du Cabinet')
                    ->url(fn (): string => route('filament.cabinet.tenant.profile', ['tenant' => Filament::getTenant()]))
                    ->icon('heroicon-o-cog'),
            ])
            ->brandName(fn () => Filament::getTenant()?->nom ?? 'Cabinet')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion')
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Utilisateurs')
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Rapports')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(true),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Mon Profil')
                    ->url('#')
                    ->icon('heroicon-m-user-circle'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseNotifications()
            ->spa(false);
    }
}
