<?php
// app/Providers/Filament/AdminPanelProvider.php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\MenuItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->brandName('MAERP')
            // ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestion des Cabinets')
                    ->icon('heroicon-o-building-office')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Gestion des Utilisateurs')
                    ->icon('heroicon-o-users')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Rapports et Analyses')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(true),
                NavigationGroup::make()
                    ->label('Configuration Système')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(true),
            ])
            ->navigationItems([
                NavigationItem::make('Documentation')
                    ->url('https://filamentphp.com/docs', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-document-text')
                    ->group('Configuration Système')
                    ->sort(99),
                NavigationItem::make('Support Technique')
                    ->url('mailto:support@comptabilite-maroc.ma')
                    ->icon('heroicon-o-lifebuoy')
                    ->group('Configuration Système')
                    ->sort(98),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Mon Profil')
                    ->url(function (): string {
                        return route('filament.admin.resources.users.edit', auth()->user());
                    })
                    ->icon('heroicon-m-user-circle'),
                'settings' => MenuItem::make()
                    ->label('Paramètres')
                    ->url('#')
                    ->icon('heroicon-m-cog-6-tooth'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->maxContentWidth('full')
            ->topNavigation(false)
            ->breadcrumbs(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->spa(false) // Pas de SPA pour éviter les problèmes avec Livewire
            ->unsavedChangesAlerts();
    }
}
