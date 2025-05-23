<?php
// app/Filament/Admin/Resources/CompanyResource/RelationManagers/UsersRelationManager.php

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

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
use App\Models\User;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Accès Utilisateurs';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Utilisateur')
                    ->schema([
                        Select::make('user_id')
                            ->label('Utilisateur')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->prenom ? "{$record->prenom} {$record->name}" : $record->name)
                            ->searchable(['name', 'prenom', 'email'])
                            ->preload()
                            ->required()
                            ->createOptionForm([
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
                                    ->unique()
                                    ->maxLength(255),
                                TextInput::make('password')
                                    ->label('Mot de passe')
                                    ->password()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Accès à la Société')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('role_company')
                                ->label('Rôle dans la Société')
                                ->options([
                                    'admin' => 'Administrateur',
                                    'comptable' => 'Comptable',
                                    'assistant' => 'Assistant Comptable',
                                    'consultation' => 'Consultation uniquement'
                                ])
                                ->default('consultation')
                                ->required(),
                            Toggle::make('actif')
                                ->label('Accès Actif')
                                ->default(true),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('date_debut_acces')
                                ->label('Début d\'Accès'),
                            DatePicker::make('date_fin_acces')
                                ->label('Fin d\'Accès')
                                ->after('date_debut_acces'),
                        ]),
                    ]),

                Section::make('Permissions Spécifiques')
                    ->description('Permissions spécifiques pour cette société')
                    ->schema([
                        Textarea::make('permissions_description')
                            ->label('Description des Permissions')
                            ->placeholder('Décrivez les permissions spécifiques accordées...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->formatStateUsing(fn ($record): string =>
                    $record->user->prenom ? "{$record->user->prenom} {$record->user->name}" : $record->user->name
                    )
                    ->searchable(['user.name', 'user.prenom'])
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('role_company')
                    ->label('Rôle')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrateur',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'consultation' => 'Consultation',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'comptable' => 'warning',
                        'assistant' => 'info',
                        'consultation' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('actif')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Actif' : 'Inactif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                TextColumn::make('date_debut_acces')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date_fin_acces')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Illimité'),

                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role_company')
                    ->label('Rôle')
                    ->options([
                        'admin' => 'Administrateur',
                        'comptable' => 'Comptable',
                        'assistant' => 'Assistant',
                        'consultation' => 'Consultation',
                    ]),
                SelectFilter::make('actif')
                    ->label('Statut')
                    ->options([
                        true => 'Actif',
                        false => 'Inactif',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Donner Accès'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_access')
                        ->label(fn ($record) => $record->actif ? 'Désactiver' : 'Activer')
                        ->icon(fn ($record) => $record->actif ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                        ->color(fn ($record) => $record->actif ? 'warning' : 'success')
                        ->action(fn ($record) => $record->update(['actif' => !$record->actif]))
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => $record->actif ? 'Désactiver l\'accès' : 'Activer l\'accès')
                        ->modalDescription(fn ($record) => $record->actif
                            ? 'Êtes-vous sûr de vouloir désactiver l\'accès de cet utilisateur ?'
                            : 'Êtes-vous sûr de vouloir activer l\'accès de cet utilisateur ?'
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->label('Supprimer Accès'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activer les accès')
                        ->icon('heroicon-m-eye')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['actif' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Désactiver les accès')
                        ->icon('heroicon-m-eye-slash')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['actif' => false]))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer les accès'),
                ]),
            ])
            ->emptyStateHeading('Aucun accès utilisateur')
            ->emptyStateDescription('Cette société n\'a encore aucun utilisateur avec des droits d\'accès.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
