<?php
// app/Filament/Admin/Resources/CabinetResource/Pages/ViewCabinet.php

namespace App\Filament\Admin\Resources\CabinetResource\Pages;

use App\Filament\Admin\Resources\CabinetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;

class ViewCabinet extends ViewRecord
{
    protected static string $resource = CabinetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->icon('heroicon-m-pencil-square'),

            Actions\Action::make('toggle_status')
                ->label(function () {
                    return $this->record->statut === 'actif' ? 'Suspendre' : 'Activer';
                })
                ->icon(function () {
                    return $this->record->statut === 'actif' ? 'heroicon-m-pause-circle' : 'heroicon-m-check-circle';
                })
                ->color(function () {
                    return $this->record->statut === 'actif' ? 'warning' : 'success';
                })
                ->action(function () {
                    $newStatus = $this->record->statut === 'actif' ? 'suspendu' : 'actif';
                    $this->record->update(['statut' => $newStatus]);

                    Notification::make()
                        ->title('Statut mis à jour')
                        ->body('Le cabinet a été ' . ($newStatus === 'actif' ? 'activé' : 'suspendu') . ' avec succès.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['statut']);
                })
                ->requiresConfirmation()
                ->modalHeading(function () {
                    return $this->record->statut === 'actif' ? 'Suspendre le cabinet' : 'Activer le cabinet';
                })
                ->modalDescription(function () {
                    return $this->record->statut === 'actif'
                        ? 'Êtes-vous sûr de vouloir suspendre ce cabinet ? Cela affectera l\'accès de tous ses utilisateurs.'
                        : 'Êtes-vous sûr de vouloir activer ce cabinet ?';
                }),

            Actions\Action::make('extend_expiration')
                ->label('Prolonger')
                ->icon('heroicon-m-calendar-days')
                ->color('info')
                ->visible(function () {
                    return $this->record->date_expiration !== null;
                })
                ->form([
                    \Filament\Forms\Components\DatePicker::make('new_expiration_date')
                        ->label('Nouvelle date d\'expiration')
                        ->required()
                        ->native(false)
                        ->default(function () {
                            return $this->record->date_expiration?->addYear();
                        })
                        ->minDate(now()),
                ])
                ->action(function (array $data) {
                    $this->record->update(['date_expiration' => $data['new_expiration_date']]);

                    Notification::make()
                        ->title('Expiration prolongée')
                        ->body('Le cabinet a été prolongé jusqu\'au ' . \Carbon\Carbon::parse($data['new_expiration_date'])->format('d/m/Y'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['date_expiration']);
                }),

            Actions\DeleteAction::make()
                ->color('danger')
                ->icon('heroicon-m-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations Générales')
                    ->description('Informations de base du cabinet')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('nom')
                                ->label('Nom du Cabinet')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->icon('heroicon-m-building-office')
                                ->copyable()
                                ->copyMessage('Nom copié!')
                                ->color('primary'),

                            TextEntry::make('raison_sociale')
                                ->label('Raison Sociale')
                                ->copyable()
                                ->copyMessage('Raison sociale copiée!'),

                            TextEntry::make('statut')
                                ->label('Statut')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'actif' => 'success',
                                    'suspendu' => 'warning',
                                    'inactif' => 'danger',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    'actif' => 'heroicon-m-check-circle',
                                    'suspendu' => 'heroicon-m-pause-circle',
                                    'inactif' => 'heroicon-m-x-circle',
                                }),
                        ]),

                        Grid::make(3)->schema([
                            TextEntry::make('forme_juridique')
                                ->label('Forme Juridique')
                                ->badge()
                                ->color('info')
                                ->placeholder('Non définie'),

                            TextEntry::make('registre_commerce')
                                ->label('Registre de Commerce')
                                ->copyable()
                                ->placeholder('Non défini'),

                            TextEntry::make('identifiant_fiscal')
                                ->label('Identifiant Fiscal')
                                ->copyable()
                                ->placeholder('Non défini'),
                        ]),

                        Grid::make(3)->schema([
                            TextEntry::make('ice')
                                ->label('ICE')
                                ->copyable()
                                ->copyMessage('ICE copié!')
                                ->placeholder('Non défini')
                                ->icon('heroicon-m-identification'),

                            TextEntry::make('patente')
                                ->label('Patente')
                                ->copyable()
                                ->placeholder('Non définie'),

                            TextEntry::make('cnss')
                                ->label('CNSS')
                                ->copyable()
                                ->placeholder('Non défini'),
                        ]),
                    ]),

                Section::make('Adresse et Contact')
                    ->description('Coordonnées du cabinet')
                    ->icon('heroicon-m-map-pin')
                    ->schema([
                        TextEntry::make('adresse_complete')
                            ->label('Adresse Complète')
                            ->getStateUsing(function ($record) {
                                return $record->adresse . ', ' . $record->ville .
                                    ($record->code_postal ? ' ' . $record->code_postal : '') .
                                    ', ' . $record->pays;
                            })
                            ->icon('heroicon-m-map-pin')
                            ->copyable()
                            ->copyMessage('Adresse copiée!')
                            ->columnSpanFull(),

                        Grid::make(3)->schema([
                            TextEntry::make('ville')
                                ->label('Ville')
                                ->badge()
                                ->color('info')
                                ->icon('heroicon-m-building-office-2'),

                            TextEntry::make('code_postal')
                                ->label('Code Postal')
                                ->placeholder('Non défini'),

                            TextEntry::make('pays')
                                ->label('Pays')
                                ->default('Maroc')
                                ->badge()
                                ->color('success'),
                        ]),

                        Grid::make(3)->schema([
                            TextEntry::make('telephone')
                                ->label('Téléphone')
                                ->icon('heroicon-m-phone')
                                ->copyable()
                                ->copyMessage('Téléphone copié!')
                                ->placeholder('Non défini')
                                ->url(function ($state) {
                                    return $state ? 'tel:' . $state : null;
                                }),

                            TextEntry::make('email')
                                ->label('Email')
                                ->icon('heroicon-m-envelope')
                                ->copyable()
                                ->copyMessage('Email copié!')
                                ->placeholder('Non défini')
                                ->url(function ($state) {
                                    return $state ? 'mailto:' . $state : null;
                                }),

                            TextEntry::make('site_web')
                                ->label('Site Web')
                                ->icon('heroicon-m-globe-alt')
                                ->url(function ($state) {
                                    return $state;
                                })
                                ->openUrlInNewTab()
                                ->placeholder('Non défini'),
                        ]),
                    ]),

                Section::make('Expert-Comptable Responsable')
                    ->description('Informations sur l\'expert-comptable responsable')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('expert_comptable_nom')
                                ->label('Nom de l\'Expert-Comptable')
                                ->weight(FontWeight::Bold)
                                ->placeholder('Non défini')
                                ->icon('heroicon-m-user'),

                            TextEntry::make('expert_comptable_numero')
                                ->label('Numéro d\'Inscription')
                                ->copyable()
                                ->copyMessage('Numéro copié!')
                                ->placeholder('Non défini')
                                ->icon('heroicon-m-identification'),

                            TextEntry::make('expert_comptable_email')
                                ->label('Email Expert-Comptable')
                                ->icon('heroicon-m-envelope')
                                ->copyable()
                                ->copyMessage('Email copié!')
                                ->placeholder('Non défini')
                                ->url(function ($state) {
                                    return $state ? 'mailto:' . $state : null;
                                }),
                        ]),
                    ]),

                Section::make('Paramètres et Limites')
                    ->description('Configuration du cabinet')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('limite_societes')
                                ->label('Limite Sociétés')
                                ->badge()
                                ->color('warning')
                                ->suffix(' sociétés max')
                                ->icon('heroicon-m-building-storefront'),

                            TextEntry::make('limite_utilisateurs')
                                ->label('Limite Utilisateurs')
                                ->badge()
                                ->color('info')
                                ->suffix(' utilisateurs max')
                                ->icon('heroicon-m-users'),

                            TextEntry::make('companies_count')
                                ->label('Sociétés Actuelles')
                                ->getStateUsing(function ($record) {
                                    return $record->companies()->count();
                                })
                                ->badge()
                                ->color(function ($record) {
                                    return $record->companies()->count() >= $record->limite_societes ? 'danger' : 'success';
                                })
                                ->icon('heroicon-m-chart-bar'),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('date_creation')
                                ->label('Date de Création')
                                ->date('d/m/Y')
                                ->icon('heroicon-m-calendar')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('date_expiration')
                                ->label('Date d\'Expiration')
                                ->date('d/m/Y')
                                ->icon('heroicon-m-calendar')
                                ->badge()
                                ->color(function ($record) {
                                    if (!$record->date_expiration) return 'success';
                                    $daysLeft = now()->diffInDays($record->date_expiration, false);
                                    if ($daysLeft < 0) return 'danger';
                                    if ($daysLeft <= 30) return 'warning';
                                    return 'success';
                                })
                                ->formatStateUsing(function ($state, $record) {
                                    if (!$state) return 'Pas de limite';
                                    $daysLeft = now()->diffInDays($record->date_expiration, false);
                                    if ($daysLeft < 0) return $state . ' (Expiré)';
                                    if ($daysLeft <= 30) return $state . ' (' . $daysLeft . ' jours restants)';
                                    return $state;
                                }),
                        ]),

                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('Aucune note')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Section::make('Statistiques d\'Utilisation')
                    ->description('Statistiques et métriques du cabinet')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('users_count')
                                ->label('Utilisateurs')
                                ->getStateUsing(function ($record) {
                                    return $record->users()->count();
                                })
                                ->badge()
                                ->color('primary')
                                ->icon('heroicon-m-users'),

                            TextEntry::make('active_companies')
                                ->label('Sociétés Actives')
                                ->getStateUsing(function ($record) {
                                    return $record->companies()->where('statut', 'active')->count();
                                })
                                ->badge()
                                ->color('success')
                                ->icon('heroicon-m-building-storefront'),

                            TextEntry::make('usage_percentage')
                                ->label('Utilisation')
                                ->getStateUsing(function ($record) {
                                    $usage = ($record->companies()->count() / $record->limite_societes) * 100;
                                    return round($usage, 1) . '%';
                                })
                                ->badge()
                                ->color(function ($record) {
                                    $percentage = ($record->companies()->count() / $record->limite_societes) * 100;
                                    return $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                                })
                                ->icon('heroicon-m-chart-pie'),

                            TextEntry::make('last_activity')
                                ->label('Dernière Activité')
                                ->getStateUsing(function ($record) {
                                    return $record->updated_at->diffForHumans();
                                })
                                ->badge()
                                ->color('gray')
                                ->icon('heroicon-m-clock'),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Créé le')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-m-plus-circle'),

                            TextEntry::make('updated_at')
                                ->label('Dernière modification')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-m-pencil'),
                        ]),
                    ]),

                Section::make('Actions Rapides')
                    ->description('Raccourcis vers les actions courantes')
                    ->icon('heroicon-m-bolt')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('quick_add_company')
                                ->label('Ajouter une Société')
                                ->getStateUsing(function () {
                                    return 'Créer une nouvelle société pour ce cabinet';
                                })
                                ->url(function ($record) {
                                    return route('filament.admin.resources.companies.create', ['cabinet_id' => $record->id]);
                                })
                                ->icon('heroicon-m-plus')
                                ->color('success'),

                            TextEntry::make('quick_add_user')
                                ->label('Ajouter un Utilisateur')
                                ->getStateUsing(function () {
                                    return 'Créer un nouvel utilisateur pour ce cabinet';
                                })
                                ->url(function ($record) {
                                    return route('filament.admin.resources.users.create', ['cabinet_id' => $record->id]);
                                })
                                ->icon('heroicon-m-user-plus')
                                ->color('info'),

                            TextEntry::make('view_companies')
                                ->label('Voir les Sociétés')
                                ->getStateUsing(function ($record) {
                                    return $record->companies()->count() . ' société(s)';
                                })
                                ->url(function ($record) {
                                    return route('filament.admin.resources.companies.index', ['tableFilters[cabinet_id][values][]' => $record->id]);
                                })
                                ->icon('heroicon-m-building-storefront')
                                ->color('warning'),
                        ]),
                    ]),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Vous pouvez ajouter des widgets spécifiques à cette page ici
        ];
    }
}
