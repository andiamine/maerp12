<?php

namespace App\Filament\Cabinet\Resources\CompanyCreationTaskResource\Pages;

use App\Filament\Cabinet\Resources\CompanyCreationTaskResource;
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

class ViewCompanyCreationTask extends ViewRecord
{
    protected static string $resource = CompanyCreationTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->icon('heroicon-m-pencil-square'),

            Actions\Action::make('advance_stage')
                ->label('Étape Suivante')
                ->icon('heroicon-m-arrow-right')
                ->color('success')
                ->action(function () {
                    if ($this->record->moveToNextStage()) {
                        Notification::make()
                            ->title('Étape avancée')
                            ->body('La tâche est passée à l\'étape: ' . $this->record->stage_name)
                            ->success()
                            ->send();

                        $this->refreshFormData(['stage', 'progress_percentage']);
                    } else {
                        Notification::make()
                            ->title('Dernière étape atteinte')
                            ->body('Cette tâche est déjà à la dernière étape.')
                            ->warning()
                            ->send();
                    }
                })
                ->visible(fn () => $this->record->status === 'in_progress'),

            Actions\DeleteAction::make()
                ->color('danger')
                ->icon('heroicon-m-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations de la Société')
                    ->description('Détails de la société à créer')
                    ->icon('heroicon-m-building-storefront')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('company_name')
                                ->label('Nom de la Société')
                                ->weight(FontWeight::Bold)
                                ->size(TextEntry\TextEntrySize::Large)
                                ->icon('heroicon-m-building-office')
                                ->color('primary'),

                            TextEntry::make('company_type_name')
                                ->label('Type de Société')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('capital_social')
                                ->label('Capital Social')
                                ->money('MAD')
                                ->placeholder('Non défini'),
                        ]),

                        TextEntry::make('activity_description')
                            ->label('Activité Principale')
                            ->columnSpanFull()
                            ->placeholder('Non définie'),
                    ]),

                Section::make('Client')
                    ->description('Informations du demandeur')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('client_name')
                                ->label('Nom du Client')
                                ->weight(FontWeight::Bold)
                                ->icon('heroicon-m-user'),

                            TextEntry::make('client_phone')
                                ->label('Téléphone')
                                ->icon('heroicon-m-phone')
                                ->url(fn ($state) => $state ? 'tel:' . $state : null)
                                ->placeholder('Non défini'),

                            TextEntry::make('client_email')
                                ->label('Email')
                                ->icon('heroicon-m-envelope')
                                ->url(fn ($state) => $state ? 'mailto:' . $state : null)
                                ->placeholder('Non défini'),
                        ]),
                    ]),

                Section::make('État d\'Avancement')
                    ->description('Progression et statut actuel')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('status')
                                ->label('Statut')
                                ->badge()
                                ->formatStateUsing(fn ($state): string => match($state) {
                                    'draft' => 'Brouillon',
                                    'in_progress' => 'En cours',
                                    'waiting_client' => 'Attente client',
                                    'waiting_admin' => 'Attente admin',
                                    'completed' => 'Terminé',
                                    'cancelled' => 'Annulé',
                                    default => $state
                                })
                                ->color(fn (string $state): string => match($state) {
                                    'draft' => 'gray',
                                    'in_progress' => 'info',
                                    'waiting_client' => 'warning',
                                    'waiting_admin' => 'orange',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'gray'
                                }),

                            TextEntry::make('stage_name')
                                ->label('Étape Actuelle')
                                ->badge()
                                ->color(fn () => $this->record->stage_color),

                            TextEntry::make('progress_percentage')
                                ->label('Progression')
                                ->formatStateUsing(fn ($state): string => $state . '%')
                                ->badge()
                                ->color(fn ($state): string => match(true) {
                                    $state < 25 => 'danger',
                                    $state < 50 => 'warning',
                                    $state < 75 => 'info',
                                    $state < 100 => 'primary',
                                    default => 'success'
                                }),
                        ]),

                        Grid::make(2)->schema([
                            TextEntry::make('target_completion_date')
                                ->label('Date Cible')
                                ->date('d/m/Y')
                                ->icon('heroicon-m-calendar')
                                ->badge()
                                ->color(fn () => $this->record->isOverdue() ? 'danger' : 'success')
                                ->formatStateUsing(function ($state) {
                                    if (!$state) return 'Non définie';
                                    $daysRemaining = $this->record->days_remaining;
                                    if ($daysRemaining === null) return $state;
                                    if ($daysRemaining < 0) return $state . ' (En retard de ' . abs($daysRemaining) . ' jours)';
                                    return $state . ' (' . $daysRemaining . ' jours restants)';
                                }),

                            TextEntry::make('actual_completion_date')
                                ->label('Date de Finalisation')
                                ->date('d/m/Y')
                                ->placeholder('Non terminé')
                                ->icon('heroicon-m-check-circle'),
                        ]),
                    ]),

                Section::make('Documents et Validations')
                    ->description('État des documents et validations')
                    ->icon('heroicon-m-document-check')
                    ->schema([
                        Grid::make(2)->schema([
                            // Certificat Négatif
                            IconEntry::make('certificat_negatif_done')
                                ->label('Certificat Négatif')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('certificat_negatif_info')
                                ->label('Détails Certificat Négatif')
                                ->getStateUsing(function ($record) {
                                    if (!$record->certificat_negatif_done) return 'Non obtenu';
                                    $info = [];
                                    if ($record->certificat_negatif_number) $info[] = 'N°: ' . $record->certificat_negatif_number;
                                    if ($record->certificat_negatif_date) $info[] = 'Date: ' . $record->certificat_negatif_date->format('d/m/Y');
                                    return implode(' | ', $info) ?: 'Obtenu';
                                }),

                            // Statuts
                            IconEntry::make('statuts_done')
                                ->label('Statuts')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('statuts_date')
                                ->label('Date des Statuts')
                                ->date('d/m/Y')
                                ->placeholder('Non définie'),

                            // Capital
                            IconEntry::make('capital_deposited')
                                ->label('Dépôt du Capital')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('capital_info')
                                ->label('Détails Dépôt Capital')
                                ->getStateUsing(function ($record) {
                                    if (!$record->capital_deposited) return 'Non déposé';
                                    $info = [];
                                    if ($record->bank_name) $info[] = 'Banque: ' . $record->bank_name;
                                    if ($record->capital_deposit_date) $info[] = 'Date: ' . $record->capital_deposit_date->format('d/m/Y');
                                    return implode(' | ', $info) ?: 'Déposé';
                                }),
                        ]),

                        Grid::make(2)->schema([
                            // Enregistrement
                            IconEntry::make('enregistrement_done')
                                ->label('Enregistrement')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('enregistrement_date')
                                ->label('Date d\'Enregistrement')
                                ->date('d/m/Y')
                                ->placeholder('Non définie'),

                            // Patente
                            IconEntry::make('patente_done')
                                ->label('Taxe Professionnelle')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('patente_info')
                                ->label('Détails Patente')
                                ->getStateUsing(function ($record) {
                                    if (!$record->patente_done) return 'Non obtenue';
                                    $info = [];
                                    if ($record->patente_number) $info[] = 'N°: ' . $record->patente_number;
                                    if ($record->identifiant_fiscal) $info[] = 'IF: ' . $record->identifiant_fiscal;
                                    return implode(' | ', $info) ?: 'Obtenue';
                                }),

                            // RC
                            IconEntry::make('rc_done')
                                ->label('Registre de Commerce')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('rc_info')
                                ->label('Détails RC')
                                ->getStateUsing(function ($record) {
                                    if (!$record->rc_done) return 'Non obtenu';
                                    $info = [];
                                    if ($record->rc_number) $info[] = 'N°: ' . $record->rc_number;
                                    if ($record->rc_date) $info[] = 'Date: ' . $record->rc_date->format('d/m/Y');
                                    return implode(' | ', $info) ?: 'Obtenu';
                                }),

                            // CNSS
                            IconEntry::make('cnss_done')
                                ->label('Affiliation CNSS')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('cnss_info')
                                ->label('Détails CNSS')
                                ->getStateUsing(function ($record) {
                                    if (!$record->cnss_done) return 'Non obtenue';
                                    $info = [];
                                    if ($record->cnss_number) $info[] = 'N°: ' . $record->cnss_number;
                                    if ($record->cnss_date) $info[] = 'Date: ' . $record->cnss_date->format('d/m/Y');
                                    return implode(' | ', $info) ?: 'Obtenue';
                                }),

                            // Publication
                            IconEntry::make('publication_done')
                                ->label('Publication JAL/BO')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('publication_info')
                                ->label('Détails Publication')
                                ->getStateUsing(function ($record) {
                                    if (!$record->publication_done) return 'Non publiée';
                                    $info = [];
                                    if ($record->publication_jal_date) $info[] = 'JAL: ' . $record->publication_jal_date->format('d/m/Y');
                                    if ($record->publication_bo_date) $info[] = 'BO: ' . $record->publication_bo_date->format('d/m/Y');
                                    return implode(' | ', $info) ?: 'Publiée';
                                }),
                        ]),
                    ]),

                Section::make('Adresse du Siège')
                    ->description('Localisation de la future société')
                    ->icon('heroicon-m-map-pin')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('domiciliation_type')
                                ->label('Type de Domiciliation')
                                ->badge()
                                ->formatStateUsing(fn ($state): string => match($state) {
                                    'domiciliation' => 'Centre de Domiciliation',
                                    'bail' => 'Bail Commercial',
                                    'propriete' => 'Propriété',
                                    default => 'Non défini'
                                })
                                ->color('info'),

                            TextEntry::make('siege_city')
                                ->label('Ville')
                                ->badge()
                                ->placeholder('Non définie'),

                            TextEntry::make('siege_address')
                                ->label('Adresse')
                                ->placeholder('Non définie'),
                        ]),
                    ]),

                Section::make('Documents Requis')
                    ->description('Liste des documents nécessaires pour l\'étape actuelle')
                    ->icon('heroicon-m-document')
                    ->schema([
                        TextEntry::make('required_documents')
                            ->label('')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->columnSpanFull(),
                    ]),

                Section::make('Notes et Observations')
                    ->description('Informations complémentaires')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Aucune note'),
                    ]),

                Section::make('Informations Système')
                    ->description('Métadonnées')
                    ->icon('heroicon-m-information-circle')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('user.name')
                                ->label('Responsable')
                                ->icon('heroicon-m-user')
                                ->placeholder('Non assigné'),

                            TextEntry::make('created_at')
                                ->label('Créé le')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-m-clock'),

                            TextEntry::make('updated_at')
                                ->label('Dernière modification')
                                ->dateTime('d/m/Y à H:i')
                                ->icon('heroicon-m-pencil'),
                        ]),
                    ]),
            ]);
    }
}
