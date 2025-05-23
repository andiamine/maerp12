<?php
// app/Filament/Cabinet/Pages/EditCabinetProfile.php

namespace App\Filament\Cabinet\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;

class EditCabinetProfile extends EditTenantProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.tenancy.edit-tenant-profile';

    public static function getLabel(): string
    {
        return 'Paramètres du Cabinet';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Générales')
                    ->description('Informations de base du cabinet')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nom')
                                ->label('Nom du Cabinet')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('raison_sociale')
                                ->label('Raison Sociale')
                                ->required()
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('forme_juridique')
                                ->label('Forme Juridique')
                                ->options([
                                    'SARL' => 'SARL',
                                    'SA' => 'SA',
                                    'SAS' => 'SAS',
                                    'SNC' => 'SNC',
                                    'Entreprise individuelle' => 'Entreprise individuelle'
                                ])
                                ->searchable(),
                            TextInput::make('registre_commerce')
                                ->label('Registre de Commerce')
                                ->maxLength(50),
                            TextInput::make('identifiant_fiscal')
                                ->label('Identifiant Fiscal')
                                ->maxLength(50),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('ice')
                                ->label('ICE')
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            TextInput::make('patente')
                                ->label('Patente')
                                ->maxLength(50),
                            TextInput::make('cnss')
                                ->label('CNSS')
                                ->maxLength(50),
                        ]),
                    ]),

                Section::make('Adresse et Contact')
                    ->description('Coordonnées du cabinet')
                    ->icon('heroicon-m-map-pin')
                    ->schema([
                        TextInput::make('adresse')
                            ->label('Adresse')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('ville')
                                ->label('Ville')
                                ->required()
                                ->datalist([
                                    'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
                                    'Agadir', 'Meknès', 'Oujda', 'Kenitra', 'Tétouan'
                                ]),
                            TextInput::make('code_postal')
                                ->label('Code Postal')
                                ->maxLength(10),
                            TextInput::make('pays')
                                ->label('Pays')
                                ->default('Maroc'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('telephone')
                                ->label('Téléphone')
                                ->tel()
                                ->maxLength(20),
                            TextInput::make('fax')
                                ->label('Fax')
                                ->maxLength(20),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                        ]),
                        TextInput::make('site_web')
                            ->label('Site Web')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),
                    ]),

                Section::make('Expert-Comptable Responsable')
                    ->description('Informations sur l\'expert-comptable responsable')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('expert_comptable_nom')
                                ->label('Nom de l\'Expert-Comptable')
                                ->maxLength(255),
                            TextInput::make('expert_comptable_numero')
                                ->label('Numéro d\'Inscription')
                                ->maxLength(50),
                            TextInput::make('expert_comptable_email')
                                ->label('Email Expert-Comptable')
                                ->email()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ]),
            ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cabinet mis à jour')
            ->body('Les informations du cabinet ont été mises à jour avec succès.');
    }
}
