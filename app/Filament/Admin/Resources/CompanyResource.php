<?php
// app/Filament/Admin/Resources/CompanyResource.php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Filament\Admin\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use App\Models\Cabinet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Sociétés';
    protected static ?string $modelLabel = 'Société';
    protected static ?string $pluralModelLabel = 'Sociétés';
    protected static ?string $navigationGroup = 'Gestion des Cabinets';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cabinet')
                    ->description('Sélection du cabinet propriétaire')
                    ->schema([
                        Select::make('cabinet_id')
                            ->label('Cabinet')
                            ->relationship('cabinet', 'nom')
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('nom')
                                    ->required(),
                                TextInput::make('raison_sociale')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Informations Générales')
                    ->description('Informations de base de la société')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('raison_sociale')
                                ->label('Raison Sociale')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('nom_commercial')
                                ->label('Nom Commercial')
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('sigle')
                                ->label('Sigle')
                                ->maxLength(10),
                            Select::make('forme_juridique')
                                ->label('Forme Juridique')
                                ->options([
                                    'SA' => 'SA - Société Anonyme',
                                    'SARL' => 'SARL - Société à Responsabilité Limitée',
                                    'SAS' => 'SAS - Société par Actions Simplifiée',
                                    'SASU' => 'SASU - Société par Actions Simplifiée Unipersonnelle',
                                    'SNC' => 'SNC - Société en Nom Collectif',
                                    'SCS' => 'SCS - Société en Commandite Simple',
                                    'SCA' => 'SCA - Société en Commandite par Actions',
                                    'Auto-entrepreneur' => 'Auto-entrepreneur',
                                    'Entreprise individuelle' => 'Entreprise individuelle',
                                    'Association' => 'Association'
                                ])
                                ->required(),
                            TextInput::make('activite_principale')
                                ->label('Activité Principale')
                                ->required(),
                        ]),
                    ]),

                Section::make('Identifiants Officiels')
                    ->description('Numéros d\'identification officiels')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('identifiant_fiscal')
                                ->label('Identifiant Fiscal')
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('ice')
                                ->label('ICE')
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('registre_commerce')
                                ->label('Registre de Commerce'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('patente')
                                ->label('Patente'),
                            TextInput::make('cnss')
                                ->label('CNSS'),
                        ]),
                    ]),

                Section::make('Capital et Secteur')
                    ->description('Informations financières et sectorielles')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('capital_social')
                                ->label('Capital Social')
                                ->numeric()
                                ->step(0.01),
                            Select::make('devise_capital')
                                ->label('Devise')
                                ->options([
                                    'MAD' => 'MAD - Dirham Marocain',
                                    'EUR' => 'EUR - Euro',
                                    'USD' => 'USD - Dollar US'
                                ])
                                ->default('MAD'),
                            TextInput::make('secteur_activite')
                                ->label('Secteur d\'Activité'),
                        ]),
                    ]),

                Section::make('Adresse du Siège')
                    ->description('Adresse du siège social')
                    ->schema([
                        TextInput::make('adresse_siege')
                            ->label('Adresse')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('ville_siege')
                                ->label('Ville')
                                ->required(),
                            TextInput::make('code_postal_siege')
                                ->label('Code Postal'),
                            TextInput::make('pays_siege')
                                ->label('Pays')
                                ->default('Maroc'),
                        ]),
                    ]),

                Section::make('Contact')
                    ->description('Coordonnées de contact')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('telephone')
                                ->label('Téléphone')
                                ->tel(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('fax')
                                ->label('Fax'),
                            TextInput::make('site_web')
                                ->label('Site Web')
                                ->url()
                                ->prefix('https://'),
                        ]),
                    ]),

                Section::make('Représentant Légal')
                    ->description('Informations sur le représentant légal')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('representant_prenom')
                                ->label('Prénom')
                                ->required(),
                            TextInput::make('representant_nom')
                                ->label('Nom')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('representant_qualite')
                                ->label('Qualité')
                                ->placeholder('Gérant, PDG, Directeur Général...')
                                ->required(),
                            TextInput::make('representant_cin')
                                ->label('CIN'),
                        ]),
                    ]),

                Section::make('Régimes Fiscaux')
                    ->description('Configuration fiscale')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('regime_tva')
                                ->label('Régime TVA')
                                ->options([
                                    'encaissement' => 'Encaissement',
                                    'debit' => 'Débit',
                                    'franchise' => 'Franchise',
                                    'forfaitaire' => 'Forfaitaire'
                                ])
                                ->default('debit')
                                ->required(),
                            Select::make('regime_is')
                                ->label('Régime IS')
                                ->options([
                                    'normal' => 'Normal',
                                    'simplifie' => 'Simplifié',
                                    'forfaitaire' => 'Forfaitaire'
                                ])
                                ->default('normal')
                                ->required(),
                            Toggle::make('assujetti_taxe_professionnelle')
                                ->label('Assujetti Taxe Professionnelle')
                                ->default(true),
                        ]),
                    ]),

                Section::make('Exercice Comptable')
                    ->description('Période comptable')
                    ->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('debut_exercice')
                                ->label('Début d\'Exercice')
                                ->required(),
                            DatePicker::make('fin_exercice')
                                ->label('Fin d\'Exercice')
                                ->required()
                                ->after('debut_exercice'),
                            TextInput::make('duree_exercice')
                                ->label('Durée (mois)')
                                ->numeric()
                                ->default(12),
                        ]),
                    ]),

                Section::make('Dates Importantes')
                    ->description('Dates clés de la société')
                    ->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('date_constitution')
                                ->label('Date de Constitution')
                                ->required(),
                            DatePicker::make('date_debut_activite')
                                ->label('Début d\'Activité'),
                            DatePicker::make('date_immatriculation_rc')
                                ->label('Immatriculation RC'),
                        ]),
                    ]),

                Section::make('Statut et Paramètres')
                    ->description('Configuration générale')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('statut')
                                ->label('Statut')
                                ->options([
                                    'active' => 'Active',
                                    'suspendue' => 'Suspendue',
                                    'en_liquidation' => 'En Liquidation',
                                    'radiee' => 'Radiée'
                                ])
                                ->default('active')
                                ->required(),
                            Select::make('monnaie_tenue_compte')
                                ->label('Monnaie de Tenue de Compte')
                                ->options([
                                    'MAD' => 'MAD - Dirham Marocain',
                                    'EUR' => 'EUR - Euro',
                                    'USD' => 'USD - Dollar US'
                                ])
                                ->default('MAD'),
                        ]),
                        Textarea::make('observations')
                            ->label('Observations')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cabinet.nom')
                    ->label('Cabinet')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('forme_juridique')
                    ->label('Forme')
                    ->badge()
                    ->searchable(),

                TextColumn::make('ice')
                    ->label('ICE')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ville_siege')
                    ->label('Ville')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspendue' => 'warning',
                        'en_liquidation' => 'danger',
                        'radiee' => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('regime_tva')
                    ->label('Régime TVA')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('date_constitution')
                    ->label('Constitution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('cabinet_id')
                    ->label('Cabinet')
                    ->relationship('cabinet', 'nom'),
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'active' => 'Active',
                        'suspendue' => 'Suspendue',
                        'en_liquidation' => 'En Liquidation',
                        'radiee' => 'Radiée'
                    ]),
                SelectFilter::make('forme_juridique')
                    ->label('Forme Juridique')
                    ->options([
                        'SA' => 'SA',
                        'SARL' => 'SARL',
                        'SAS' => 'SAS',
                        'Auto-entrepreneur' => 'Auto-entrepreneur'
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
