<?php
// app/Filament/Admin/Resources/CabinetResource.php

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
use Filament\Tables\Actions\ActionGroup;

class CabinetResource extends Resource
{
    protected static ?string $model = Cabinet::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Cabinets';
    protected static ?string $modelLabel = 'Cabinet';
    protected static ?string $pluralModelLabel = 'Cabinets';
    protected static ?string $navigationGroup = 'Gestion des Cabinets';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Générales')
                    ->description('Informations de base du cabinet')
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
                                ]),
                            TextInput::make('registre_commerce')
                                ->label('Registre de Commerce'),
                            TextInput::make('identifiant_fiscal')
                                ->label('Identifiant Fiscal'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('ice')
                                ->label('ICE')
                                ->unique(ignoreRecord: true),
                            TextInput::make('patente')
                                ->label('Patente'),
                            TextInput::make('cnss')
                                ->label('CNSS'),
                        ]),
                    ]),

                Section::make('Adresse et Contact')
                    ->description('Coordonnées du cabinet')
                    ->schema([
                        TextInput::make('adresse')
                            ->label('Adresse')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('ville')
                                ->label('Ville')
                                ->required(),
                            TextInput::make('code_postal')
                                ->label('Code Postal'),
                            TextInput::make('pays')
                                ->label('Pays')
                                ->default('Maroc'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('telephone')
                                ->label('Téléphone')
                                ->tel(),
                            TextInput::make('fax')
                                ->label('Fax'),
                            TextInput::make('email')
                                ->label('Email')
                                ->email(),
                        ]),
                        TextInput::make('site_web')
                            ->label('Site Web')
                            ->url()
                            ->prefix('https://'),
                    ]),

                Section::make('Expert-Comptable Responsable')
                    ->description('Informations sur l\'expert-comptable responsable')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('expert_comptable_nom')
                                ->label('Nom de l\'Expert-Comptable'),
                            TextInput::make('expert_comptable_numero')
                                ->label('Numéro d\'Inscription'),
                            TextInput::make('expert_comptable_email')
                                ->label('Email Expert-Comptable')
                                ->email(),
                        ]),
                    ]),

                Section::make('Paramètres et Limites')
                    ->description('Configuration du cabinet')
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
                                ->default('actif'),
                            TextInput::make('limite_societes')
                                ->label('Limite Sociétés')
                                ->numeric()
                                ->default(10)
                                ->required(),
                            TextInput::make('limite_utilisateurs')
                                ->label('Limite Utilisateurs')
                                ->numeric()
                                ->default(5)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('date_creation')
                                ->label('Date de Création')
                                ->required()
                                ->default(now()),
                            DatePicker::make('date_expiration')
                                ->label('Date d\'Expiration')
                                ->after('date_creation'),
                        ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
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
                    ->weight(FontWeight::Bold),

                TextColumn::make('raison_sociale')
                    ->label('Raison Sociale')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('expert_comptable_nom')
                    ->label('Expert-Comptable')
                    ->searchable()
                    ->toggleable(),

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
                    ->alignCenter(),

                TextColumn::make('users_count')
                    ->label('Utilisateurs')
                    ->counts('users')
                    ->sortable()
                    ->alignCenter(),

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
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'suspendu' => 'Suspendu',
                        'inactif' => 'Inactif'
                    ]),
                SelectFilter::make('ville')
                    ->label('Ville')
                    ->options(fn () => Cabinet::pluck('ville', 'ville')->toArray()),
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
}
