<?php
// app/Filament/Admin/Resources/CabinetResource.php - Version corrigée

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CabinetResource\Pages;
use App\Filament\Admin\Resources\CabinetResource\RelationManagers;
use App\Models\Cabinet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Enums\ActionSize;

class CabinetResource extends Resource
{
    protected static ?string $model = Cabinet::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Cabinets';
    protected static ?string $modelLabel = 'Cabinet';
    protected static ?string $pluralModelLabel = 'Cabinets';
    protected static ?string $navigationGroup = 'Gestion des Cabinets';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'nom';

    // Global search
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['companies', 'users']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nom', 'raison_sociale', 'ville', 'email', 'ice'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Ville' => $record->ville,
            'Email' => $record->email,
            'Sociétés' => $record->companies_count ?? $record->companies()->count(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Générales')
                    ->description('Informations de base du cabinet')
                    ->icon('heroicon-m-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nom')
                                ->label('Nom du Cabinet')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $context, $state, callable $set, callable $get) {
                                    if ($context === 'create') {
                                        $set('raison_sociale', $state);
                                    }
                                }),
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
                    ->collapsible()
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
                    ->collapsible()
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

                Section::make('Paramètres et Limites')
                    ->description('Configuration du cabinet')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('statut')
                                ->label('Statut')
                                ->options([
                                    'actif' => 'Actif',
                                    'suspendu' => 'Suspendu',
                                    'inactif' => 'Inactif'
                                ])
                                ->required()
                                ->default('actif')
                                ->native(false),
                            TextInput::make('limite_societes')
                                ->label('Limite Sociétés')
                                ->numeric()
                                ->default(10)
                                ->required()
                                ->minValue(1)
                                ->maxValue(1000),
                            TextInput::make('limite_utilisateurs')
                                ->label('Limite Utilisateurs')
                                ->numeric()
                                ->default(5)
                                ->required()
                                ->minValue(1)
                                ->maxValue(100),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('date_creation')
                                ->label('Date de Création')
                                ->required()
                                ->default(now())
                                ->native(false),
                            DatePicker::make('date_expiration')
                                ->label('Date d\'Expiration')
                                ->after('date_creation')
                                ->native(false),
                        ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nom')
                    ->label('Nom du Cabinet')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-building-office')
                    ->copyable()
                    ->copyMessage('Nom copié!')
                    ->tooltip('Cliquez pour copier'),

                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->searchable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('expert_comptable_nom')
                    ->label('Expert-Comptable')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Non défini'),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'suspendu' => 'warning',
                        'inactif' => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('companies_count')
                    ->label('Sociétés')
                    ->counts('companies')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('date_creation')
                    ->label('Date Création')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_expiration')
                    ->label('Expiration')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->date_expiration && $record->date_expiration->isPast() ? 'danger' : null)
                    ->icon(fn ($record) => $record->date_expiration && $record->date_expiration->isPast() ? 'heroicon-m-exclamation-triangle' : null)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'suspendu' => 'Suspendu',
                        'inactif' => 'Inactif'
                    ])
                    ->multiple(),

                SelectFilter::make('ville')
                    ->label('Ville')
                    ->options(fn () => Cabinet::pluck('ville', 'ville')->toArray())
                    ->searchable()
                    ->multiple(),

                Filter::make('expired')
                    ->label('Cabinets expirés')
                    ->query(fn (Builder $query): Builder => $query->where('date_expiration', '<', now()))
                    ->indicator('Expirés')
                    ->toggle(),

                Filter::make('without_companies')
                    ->label('Sans sociétés')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('companies'))
                    ->indicator('Sans sociétés')
                    ->toggle(),

                Filter::make('created_this_month')
                    ->label('Créés ce mois')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month))
                    ->indicator('Ce mois')
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('info'),
                    Tables\Actions\EditAction::make()
                        ->color('warning'),
                    Action::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(fn (Cabinet $record) => $record->update(['statut' => 'actif']))
                        ->requiresConfirmation()
                        ->visible(fn (Cabinet $record): bool => $record->statut !== 'actif'),
                    Action::make('suspend')
                        ->label('Suspendre')
                        ->icon('heroicon-m-pause-circle')
                        ->color('warning')
                        ->action(fn (Cabinet $record) => $record->update(['statut' => 'suspendu']))
                        ->requiresConfirmation()
                        ->visible(fn (Cabinet $record): bool => $record->statut === 'actif'),
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
                    BulkAction::make('activate')
                        ->label('Activer les cabinets')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->update(['statut' => 'actif']);
                            Notification::make()
                                ->title('Cabinets activés avec succès')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Activer les cabinets sélectionnés')
                        ->modalDescription('Êtes-vous sûr de vouloir activer tous les cabinets sélectionnés ?'),

                    BulkAction::make('suspend')
                        ->label('Suspendre les cabinets')
                        ->icon('heroicon-m-pause-circle')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each->update(['statut' => 'suspendu']);
                            Notification::make()
                                ->title('Cabinets suspendus avec succès')
                                ->warning()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Suspendre les cabinets sélectionnés')
                        ->modalDescription('Êtes-vous sûr de vouloir suspendre tous les cabinets sélectionnés ?'),

                    BulkAction::make('extend_expiration')
                        ->label('Prolonger l\'expiration')
                        ->icon('heroicon-m-calendar-days')
                        ->color('info')
                        ->form([
                            DatePicker::make('new_expiration_date')
                                ->label('Nouvelle date d\'expiration')
                                ->required()
                                ->native(false)
                                ->minDate(now()),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['date_expiration' => $data['new_expiration_date']]);
                            Notification::make()
                                ->title('Dates d\'expiration mises à jour')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun cabinet trouvé')
            ->emptyStateDescription('Commencez par créer votre premier cabinet.')
            ->emptyStateIcon('heroicon-o-building-office')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer un cabinet')
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
            RelationManagers\CompaniesRelationManager::class,
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCabinets::route('/'),
            'create' => Pages\CreateCabinet::route('/create'),
            'view' => Pages\ViewCabinet::route('/{record}'),
            'edit' => Pages\EditCabinet::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('statut', 'actif')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
