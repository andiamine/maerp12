<?php
// app/Filament/Admin/Resources/CompanyResource.php - Version corrigée

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
use Illuminate\Database\Eloquent\Model;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Enums\ActionSize;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Sociétés';
    protected static ?string $modelLabel = 'Société';
    protected static ?string $pluralModelLabel = 'Sociétés';
    protected static ?string $navigationGroup = 'Gestion des Cabinets';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'raison_sociale';

    // Global search
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['cabinet']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['raison_sociale', 'nom_commercial', 'ice', 'identifiant_fiscal', 'cabinet.nom'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Cabinet' => $record->cabinet->nom,
            'Ville' => $record->ville_siege,
            'Forme' => $record->forme_juridique,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cabinet')
                    ->description('Sélection du cabinet propriétaire')
                    ->icon('heroicon-m-building-office')
                    ->schema([
                        Select::make('cabinet_id')
                            ->label('Cabinet')
                            ->relationship('cabinet', 'nom')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nom')
                                    ->required(),
                                TextInput::make('raison_sociale')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Informations Générales')
                    ->description('Informations de base de la société')
                    ->icon('heroicon-m-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('raison_sociale')
                                ->label('Raison Sociale')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set, callable $get) {
                                    if ($context === 'create' && empty($get('nom_commercial'))) {
                                        $set('nom_commercial', $state);
                                    }
                                }),
                            TextInput::make('nom_commercial')
                                ->label('Nom Commercial')
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('sigle')
                                ->label('Sigle')
                                ->maxLength(10)
                                ->placeholder('Ex: ABC'),
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
                                ->required()
                                ->searchable(),
                            TextInput::make('activite_principale')
                                ->label('Activité Principale')
                                ->required()
                                ->placeholder('Ex: Commerce de détail'),
                        ]),
                    ]),

                Section::make('Identifiants Officiels')
                    ->description('Numéros d\'identification officiels')
                    ->icon('heroicon-m-identification')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('identifiant_fiscal')
                                ->label('Identifiant Fiscal')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            TextInput::make('ice')
                                ->label('ICE')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            TextInput::make('registre_commerce')
                                ->label('Registre de Commerce')
                                ->maxLength(50),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('patente')
                                ->label('Patente')
                                ->maxLength(50),
                            TextInput::make('cnss')
                                ->label('CNSS')
                                ->maxLength(50),
                        ]),
                    ]),

                Section::make('Capital et Secteur')
                    ->description('Informations financières et sectorielles')
                    ->icon('heroicon-m-banknotes')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('capital_social')
                                ->label('Capital Social')
                                ->numeric()
                                ->step(0.01)
                                ->suffix('MAD')
                                ->placeholder('0.00'),
                            Select::make('devise_capital')
                                ->label('Devise')
                                ->options([
                                    'MAD' => 'MAD - Dirham Marocain',
                                    'EUR' => 'EUR - Euro',
                                    'USD' => 'USD - Dollar US'
                                ])
                                ->default('MAD'),
                            TextInput::make('secteur_activite')
                                ->label('Secteur d\'Activité')
                                ->datalist([
                                    'Agriculture', 'Industrie', 'Services', 'Commerce',
                                    'BTP', 'Transport', 'Tourisme', 'Technologie'
                                ]),
                        ]),
                    ]),

                Section::make('Adresse du Siège')
                    ->description('Adresse du siège social')
                    ->icon('heroicon-m-map-pin')
                    ->collapsible()
                    ->schema([
                        TextInput::make('adresse_siege')
                            ->label('Adresse')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('ville_siege')
                                ->label('Ville')
                                ->required()
                                ->datalist([
                                    'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger',
                                    'Agadir', 'Meknès', 'Oujda', 'Kenitra', 'Tétouan'
                                ]),
                            TextInput::make('code_postal_siege')
                                ->label('Code Postal'),
                            TextInput::make('pays_siege')
                                ->label('Pays')
                                ->default('Maroc'),
                        ]),
                    ]),

                Section::make('Contact')
                    ->description('Coordonnées de contact')
                    ->icon('heroicon-m-phone')
                    ->collapsible()
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
                    ->icon('heroicon-m-user')
                    ->collapsible()
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
                    ->icon('heroicon-m-document-text')
                    ->collapsible()
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
                    ->icon('heroicon-m-calendar')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('debut_exercice')
                                ->label('Début d\'Exercice')
                                ->required()
                                ->native(false),
                            DatePicker::make('fin_exercice')
                                ->label('Fin d\'Exercice')
                                ->required()
                                ->after('debut_exercice')
                                ->native(false),
                            TextInput::make('duree_exercice')
                                ->label('Durée (mois)')
                                ->numeric()
                                ->default(12),
                        ]),
                    ]),

                Section::make('Dates Importantes')
                    ->description('Dates clés de la société')
                    ->icon('heroicon-m-calendar-days')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('date_constitution')
                                ->label('Date de Constitution')
                                ->required()
                                ->native(false),
                            DatePicker::make('date_debut_activite')
                                ->label('Début d\'Activité')
                                ->native(false),
                            DatePicker::make('date_immatriculation_rc')
                                ->label('Immatriculation RC')
                                ->native(false),
                        ]),
                    ]),

                Section::make('Statut et Paramètres')
                    ->description('Configuration générale')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->collapsible()
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
                    ->toggleable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                TextColumn::make('forme_juridique')
                    ->label('Forme')
                    ->badge()
                    ->searchable()
                    ->color('warning'),

                TextColumn::make('ice')
                    ->label('ICE')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('ICE copié!'),

                TextColumn::make('ville_siege')
                    ->label('Ville')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

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
                    ->toggleable()
                    ->color('info'),

                TextColumn::make('date_constitution')
                    ->label('Constitution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('cabinet_id')
                    ->label('Cabinet')
                    ->relationship('cabinet', 'nom')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'active' => 'Active',
                        'suspendue' => 'Suspendue',
                        'en_liquidation' => 'En Liquidation',
                        'radiee' => 'Radiée'
                    ])
                    ->multiple(),

                SelectFilter::make('forme_juridique')
                    ->label('Forme Juridique')
                    ->options([
                        'SA' => 'SA',
                        'SARL' => 'SARL',
                        'SAS' => 'SAS',
                        'Auto-entrepreneur' => 'Auto-entrepreneur'
                    ])
                    ->multiple(),

                SelectFilter::make('regime_tva')
                    ->label('Régime TVA')
                    ->options([
                        'encaissement' => 'Encaissement',
                        'debit' => 'Débit',
                        'franchise' => 'Franchise',
                        'forfaitaire' => 'Forfaitaire'
                    ])
                    ->multiple(),

                SelectFilter::make('ville_siege')
                    ->label('Ville')
                    ->options(fn () => Company::pluck('ville_siege', 'ville_siege')->filter()->unique()->sort()->toArray())
                    ->searchable()
                    ->multiple(),

                Filter::make('created_this_year')
                    ->label('Créées cette année')
                    ->query(fn (Builder $query): Builder => $query->whereYear('created_at', now()->year))
                    ->indicator('Cette année'),

                Filter::make('without_users')
                    ->label('Sans utilisateurs')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('users'))
                    ->indicator('Sans utilisateurs'),

                TernaryFilter::make('assujetti_taxe_professionnelle')
                    ->label('Taxe Professionnelle')
                    ->placeholder('Tous')
                    ->trueLabel('Assujettis')
                    ->falseLabel('Non assujettis'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Action::make('change_status')
                        ->label('Changer le statut')
                        ->icon('heroicon-m-arrow-path')
                        ->color('gray')
                        ->form([
                            Select::make('new_status')
                                ->label('Nouveau statut')
                                ->options([
                                    'active' => 'Active',
                                    'suspendue' => 'Suspendue',
                                    'en_liquidation' => 'En Liquidation',
                                    'radiee' => 'Radiée'
                                ])
                                ->required(),
                            Textarea::make('reason')
                                ->label('Raison du changement')
                                ->rows(3),
                        ])
                        ->action(function (Company $record, array $data) {
                            $oldStatus = $record->statut;
                            $record->update(['statut' => $data['new_status']]);

                            Notification::make()
                                ->title('Statut mis à jour')
                                ->body('Le statut de la société a été changé avec succès.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('change_status')
                        ->label('Changer le statut')
                        ->icon('heroicon-m-arrow-path')
                        ->color('info')
                        ->form([
                            Select::make('new_status')
                                ->label('Nouveau statut')
                                ->options([
                                    'active' => 'Active',
                                    'suspendue' => 'Suspendue',
                                    'en_liquidation' => 'En Liquidation',
                                    'radiee' => 'Radiée'
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['statut' => $data['new_status']]);
                            });

                            Notification::make()
                                ->title('Statuts mis à jour')
                                ->body(count($records) . ' société(s) ont été mises à jour.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucune société trouvée')
            ->emptyStateDescription('Commencez par créer votre première société.')
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer une société')
                    ->icon('heroicon-m-plus'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('statut', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
