<?php
// app/Filament/Admin/Resources/CabinetResource/RelationManagers/CompaniesRelationManager.php

namespace App\Filament\Admin\Resources\CabinetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
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
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';
    protected static ?string $title = 'Sociétés';
    protected static ?string $recordTitleAttribute = 'raison_sociale';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Générales')
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

                Section::make('Adresse du Siège')
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

                Section::make('Exercice Comptable')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('debut_exercice')
                                ->label('Début d\'Exercice')
                                ->required(),
                            DatePicker::make('fin_exercice')
                                ->label('Fin d\'Exercice')
                                ->required()
                                ->after('debut_exercice'),
                        ]),
                    ]),

                Section::make('Statut')
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
                            DatePicker::make('date_constitution')
                                ->label('Date de Constitution')
                                ->required(),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('raison_sociale')
            ->columns([
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvelle Société'),
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
            ->emptyStateHeading('Aucune société')
            ->emptyStateDescription('Ce cabinet n\'a encore aucune société associée.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }
}
