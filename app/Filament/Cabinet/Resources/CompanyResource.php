<?php

namespace App\Filament\Cabinet\Resources;

use App\Filament\Cabinet\Resources\CompanyResource\Pages;
use App\Filament\Cabinet\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
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
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Sociétés';
    protected static ?string $modelLabel = 'Société';
    protected static ?string $pluralModelLabel = 'Sociétés';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'raison_sociale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

                Section::make('Exercice Comptable et Statut')
                    ->description('Configuration comptable')
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
                        ]),
                        DatePicker::make('date_constitution')
                            ->label('Date de Constitution')
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(40),

                TextColumn::make('forme_juridique')
                    ->label('Forme')
                    ->badge()
                    ->searchable(),

                TextColumn::make('ice')
                    ->label('ICE')
                    ->searchable()
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

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('date_constitution')
                    ->label('Constitution')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->before(function (Company $record) {
                            // Vérifier s'il y a des utilisateurs associés
                            if ($record->users()->count() > 0) {
                                Notification::make()
                                    ->title('Suppression impossible')
                                    ->body('Cette société a des utilisateurs associés.')
                                    ->danger()
                                    ->send();
                                return false;
                            }
                        }),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->where('statut', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Vérifier les permissions
        $user = auth()->user();
        return $user && in_array($user->role_global, ['admin_cabinet', 'expert_comptable', 'comptable']);
    }
}
