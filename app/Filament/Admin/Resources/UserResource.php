<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?string $pluralModelLabel = 'Utilisateurs';
    protected static ?string $navigationGroup = 'Gestion des Utilisateurs';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cabinet')
                    ->description('Affectation à un cabinet')
                    ->schema([
                        Select::make('cabinet_id')
                            ->label('Cabinet')
                            ->relationship('cabinet', 'nom')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nom')
                                    ->required(),
                                TextInput::make('raison_sociale')
                                    ->required(),
                            ]),
                    ]),

                Section::make('Informations Personnelles')
                    ->description('Informations de base de l\'utilisateur')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('prenom')
                                ->label('Prénom')
                                ->maxLength(255),
                        ]),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('telephone')
                            ->label('Téléphone')
                            ->tel(),
                    ]),

                Section::make('Accès et Rôle')
                    ->description('Paramètres d\'accès')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Mot de passe')
                                ->password()
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create')
                                ->maxLength(255),
                            Select::make('role_global')
                                ->label('Rôle Global')
                                ->options([
                                    'super_admin' => 'Super Admin',
                                    'admin_cabinet' => 'Admin Cabinet',
                                    'utilisateur' => 'Utilisateur',
                                ])
                                ->default('utilisateur')
                                ->required(),
                        ]),
                        Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'actif' => 'Actif',
                                'suspendu' => 'Suspendu',
                                'inactif' => 'Inactif',
                            ])
                            ->default('actif')
                            ->required(),
                        DateTimePicker::make('derniere_connexion')
                            ->label('Dernière Connexion')
                            ->disabled(),
                    ]),

                Section::make('Notes')
                    ->schema([
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
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('cabinet.nom')
                    ->label('Cabinet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role_global')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin_cabinet' => 'Admin Cabinet',
                        'utilisateur' => 'Utilisateur',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin_cabinet' => 'warning',
                        'utilisateur' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'actif' => 'success',
                        'suspendu' => 'warning',
                        'inactif' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('derniere_connexion')
                    ->label('Dernière Connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('companies_count')
                    ->label('Sociétés')
                    ->counts('companies')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('cabinet_id')
                    ->label('Cabinet')
                    ->relationship('cabinet', 'nom'),
                SelectFilter::make('role_global')
                    ->label('Rôle')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin_cabinet' => 'Admin Cabinet',
                        'utilisateur' => 'Utilisateur',
                    ]),
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'suspendu' => 'Suspendu',
                        'inactif' => 'Inactif',
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
            RelationManagers\CompaniesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
