<?php
// app/Filament/Admin/Resources/CabinetResource/RelationManagers/UsersRelationManager.php

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
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Utilisateurs';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informations Personnelles')
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
                                    'expert_comptable' => 'Expert-Comptable',
                                    'comptable' => 'Comptable',
                                    'assistant' => 'Assistant',
                                    'client' => 'Client',
                                ])
                                ->default('assistant')
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('prenom')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('role_global')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin_cabinet' => 'Admin Cabinet',
                        'expert_comptable' => 'Expert-Comptable',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'client' => 'Client',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin_cabinet' => 'warning',
                        'expert_comptable' => 'success',
                        'comptable' => 'info',
                        'assistant' => 'gray',
                        'client' => 'secondary',
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
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('companies_count')
                    ->label('Sociétés')
                    ->counts('companies')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('role_global')
                    ->label('Rôle')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin_cabinet' => 'Admin Cabinet',
                        'expert_comptable' => 'Expert-Comptable',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'client' => 'Client',
                    ]),
                SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'actif' => 'Actif',
                        'suspendu' => 'Suspendu',
                        'inactif' => 'Inactif',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvel Utilisateur'),
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
            ->emptyStateHeading('Aucun utilisateur')
            ->emptyStateDescription('Ce cabinet n\'a encore aucun utilisateur.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
